<?php


namespace Webnic\WebnicSDK\Core;

// use GuzzleHttp\Client as GuzzleClient;
// use GuzzleHttp\Exception\GuzzleException;
// use GuzzleHttp\Promise\Utils;

class ApiConnector
{
    protected ?string $apiKey = null; // Initialize as null
    // protected GuzzleClient $client;
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;
    protected string $tokenEndpoint;
    protected string $apiVersion;
    protected ?\DateTime $tokenExpiration = null; // Store token expiration time

    protected string $serviceUrl = '';

    // public function __construct(string $clientId, string $clientSecret, array $config, string $tokenEndpoint, string $baseUrl = 'https://oteapi.webnic.cc')
    public function __construct(string $clientId, string $clientSecret, array $config)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;

        // Set config values with default for baseUrl
        $this->tokenEndpoint = $config['tokenEndpoint'] ?? 'https://oteapi.webnic.cc/reseller/v2/api-user/token';
        $this->baseUrl = $config['baseUrl'] ?? 'https://oteapi.webnic.cc';
        $this->apiVersion = $config['apiVersion'] ?? '/v2';

        // $this->client = new GuzzleClient();
    }

    protected function retrieveAccessToken(): array
    {

        $filePath = __DIR__ . '/webnic_token.json';
        $baseUrl = $this->baseUrl;

        // Ensure the file exists before reading
        if (!file_exists($filePath)) {
            file_put_contents($filePath, json_encode([])); // Create an empty JSON file
        }
        // Read token from file if it exists
        $tokenData = json_decode(file_get_contents($filePath), true);
        if (isset($tokenData['token'], $tokenData['expires_at'])) {
            if (new \DateTime($tokenData['expires_at']) > new \DateTime() && $tokenData['base_url'] == $baseUrl) {
                return array(
                    "code" => '1000',
                    "token" => $tokenData['token'],
                    'message' => ''
                );
            }
        }

        try {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->tokenEndpoint);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
                'username' => $this->clientId,
                'password' => $this->clientSecret,
            )));

            $response = curl_exec($ch);
            curl_close($ch);

            $responseDecode = json_decode($response, true);
            if (isset($responseDecode['data']['access_token'])) {
                $accessToken = $responseDecode['data']['access_token'];
                $tokenExpiration = (new \DateTime('+50 minutes'))->format('Y-m-d H:i:s');

                // Save token to file (overwrite existing or create new file)
                file_put_contents($filePath, json_encode([
                    'token' => $accessToken,
                    'expires_at' => $tokenExpiration,
                    'base_url' => $baseUrl,
                ], JSON_PRETTY_PRINT));

                return array(
                    "code" => '1000',
                    "token" => $accessToken,
                    'message' => ''
                );
            } else {
                return array(
                    "code" => '2400',
                    "token" => "",
                    'message' => "Access Token retrieval Error: " . $responseDecode['error']['message'] ?? ""
                );
            }
        } catch (\Exception $e) {
            // Fall back to default error message
            return array(
                'code' => '2400',
                'error' => array(
                    'message' => "Access Token retrieval Error: " . $e->getMessage()
                ),
            );
        }
    }

    protected function isTokenValid(): bool
    {
        // Check if the token has expired
        return $this->tokenExpiration && $this->tokenExpiration > new \DateTime();
    }

    protected function request(string $method, string $url, array $options = [])
    {
        $getToken = $this->retrieveAccessToken();
        if ($getToken['code'] != '1000') {
            return $getToken;
        }

        $token = $getToken['token'];

        // Prepare headers
        $headers = [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        // Handle query parameters
        if (!empty($options['query'])) {
            $queryString = '';
            foreach ($options['query'] as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $v) {
                        $queryString .= urlencode($key) . '=' . urlencode($v) . '&';
                    }
                } else {
                    $queryString .= urlencode($key) . '=' . urlencode($value) . '&';
                }
            }
            $url .= '?' . rtrim($queryString, '&');
        }

        // Handle body
        $body = '';
        if (!empty($options['json'])) {
            $body = json_encode($options['json']);
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if (!empty($body)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        // Handle response
        if ($response === false) {
            return [
                'code' => '2400',
                'error' => [
                    'message' => $curlError
                ]
            ];
        }

        $decoded = json_decode($response, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            $decoded['url_request'] = $url;
            $decoded['http_status_code'] = $httpCode;

            // Token invalidation logic
            if (
                isset($decoded['error']['message']) &&
                $decoded['error']['message'] == "Invalid or expired token. Reason: Provided token isn't active"
            ) {
                $filePath = __DIR__ . '/webnic_token.json';
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            return $decoded;
        }

        return [
            'code' => '2400',
            'error' => [
                'message' => $response
            ]
        ];
    }

    protected function buildUrl(string $endpoint): string
    {
        return "{$this->baseUrl}{$this->serviceUrl}{$endpoint}";
    }

    protected function sendRequest(string $method, string $endpoint, array $queryParams = [], array $body = []): array
    {
        $options = [];

        if (!empty($queryParams)) {
            $options['query'] = $queryParams;
        }

        if (!empty($body)) {
            $options['json'] = $body;
        }

        try {
            $response = $this->request($method, $this->buildUrl($endpoint), $options);
            return $response;
        } catch (\Exception $e) {

            return array(
                'code' => '2400',
                'error' =>  array(
                    'message' =>  $e->getMessage()
                )
            );
        }
    }

    /**
     * Send multiple asynchronous API requests.
     *
     * @param array $requests Array of requests with 'method', 'endpoint', 'queryParams', 'body'.
     * @return array Associative array with responses.
     */
    public function asyncRequests(array $requests): array
    {
        $getToken = $this->retrieveAccessToken();
        if ($getToken['code'] != '1000') {
            return $getToken;
        }

        $token = $getToken['token'];

        $multiHandle = curl_multi_init();
        $curlHandles = array();
        $responses = array();
        $endpoints = array();

        foreach ($requests as $key => $req) {
            $fullRequestUrl = $this->buildUrl($req['endpoint']);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $fullRequestUrl);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($req['method']));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_ENCODING, '');
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ));

            if (!empty($req['body'])) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req['body']));
            }

            curl_multi_add_handle($multiHandle, $ch);
            $curlHandles[$key] = $ch;
        }

        $running = null;
        do {
            curl_multi_exec($multiHandle, $running);
            curl_multi_select($multiHandle);
        } while ($running > 0);

        foreach ($curlHandles as $key => $ch) {
            $response = curl_multi_getcontent($ch);
            $decodedResponse = json_decode($response, true);
            $responses[$key] = $decodedResponse;

            $info = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            $endpoints[] = $info;

            curl_multi_remove_handle($multiHandle, $ch);
            curl_close($ch);
        }

        curl_multi_close($multiHandle);

        return $responses;
    }
}
