<?php


namespace Webnic\WebnicSDK\Core;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\Utils;

class ApiConnector
{
    protected ?string $apiKey = null; // Initialize as null
    protected GuzzleClient $client;
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

        $this->client = new GuzzleClient();

        // Retrieve access token on initialization
        $this->apiKey = $this->retrieveAccessToken();
    }

    protected function retrieveAccessToken(): string
    {
        // if ($this->apiKey !== null && $this->isTokenValid()) {
        //     return $this->apiKey; // Return existing valid token
        // }

        $filePath = __DIR__ . '/webnic_token.json';

        // Ensure the file exists before reading
        if (!file_exists($filePath)) {
            file_put_contents($filePath, json_encode([])); // Create an empty JSON file
        }
        // Read token from file if it exists
        $tokenData = json_decode(file_get_contents($filePath), true);
        if (isset($tokenData['token'], $tokenData['expires_at'])) {
            if (new \DateTime($tokenData['expires_at']) > new \DateTime()) {
                return $tokenData['token'];
            }
        }

        try {
            $response = $this->client->post($this->tokenEndpoint, [
                'json' => [
                    'username' => $this->clientId,
                    'password' => $this->clientSecret,
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            if (isset($data['data']['access_token'])) {
                $accessToken = $data['data']['access_token'];
                $tokenExpiration = (new \DateTime('+50 minutes'))->format('Y-m-d H:i:s');

                // Save token to file (overwrite existing or create new file)
                file_put_contents($filePath, json_encode([
                    'token' => $accessToken,
                    'expires_at' => $tokenExpiration
                ], JSON_PRETTY_PRINT));

                return $accessToken;
            } else {
                throw new \Exception('No access token received');
            }
        } catch (GuzzleException $e) {
            throw new \Exception('Token retrieval failed: ' . $e->getMessage());
        }
    }

    protected function isTokenValid(): bool
    {
        // Check if the token has expired
        return $this->tokenExpiration && $this->tokenExpiration > new \DateTime();
    }

    protected function request(string $method, string $url, array $options = [])
    {
        // Ensure we have a valid token before making the request
        $options['headers']['Authorization'] = 'Bearer ' . $this->retrieveAccessToken();

        // echo "Request URL: $url\n";

        // $remainingTime = $this->tokenExpiration->getTimestamp() - (new \DateTime())->getTimestamp();
        // if ($remainingTime <= 50 * 60 && $remainingTime > 0) {
        //     echo "Token will expire in " . floor($remainingTime / 60) . " minutes. \n\n";
        // }

        try {
            // return $this->client->request($method, $url, $options);
            // Make the request
            $response = $this->client->request($method, $url, $options);

            // Decode JSON response body if it’s JSON, and add HTTP status code
            $responseBody = json_decode($response->getBody()->getContents(), true);
            $responseBody['http_status_code'] = $response->getStatusCode();
            return $responseBody;
        } catch (GuzzleException $e) {
            $response = method_exists($e, 'getResponse') ? $e->getResponse() : null;

            if ($response) {
                $body = (string) $response->getBody();
                $decoded = json_decode($body, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    // Return the actual decoded message from response
                    $decoded['http_status_code'] = $response->getStatusCode();
                    return $decoded;
                } else {
                    // Return raw body if JSON decoding fails
                    return [
                        'code' => '2400',
                        'error' => [
                            'message' => $body
                        ]
                    ];
                }
            }

            // Fall back to default error message
            return [
                'code' => '2400',
                'error' => [
                    'message' => $e->getMessage()
                ]
            ];

            // throw new \Exception('Request failed: ' . $e->getMessage());
        }
    }

    protected function buildUrl(string $endpoint): string
    {
        return "{$this->baseUrl}{$this->serviceUrl}{$endpoint}";
    }

    /**
     * sendRequest from the specified endpoint using the given HTTP method.
     *
     * This function handles both query parameters and JSON body for the request.
     *
     * @param string $method       The HTTP method to use (GET, POST, PUT, etc.).
     * @param string $endpoint     The API endpoint to request.
     * @param array $queryParams   Optional query parameters for the request.
     * @param array $body          Optional JSON body for the request.
     * @return array The response decoded as an associative array.
     *
     * @throws \Exception If the request fails.
     */
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
        } catch (GuzzleException $e) {

            return array(
                'code' => '2400',
                'error' =>  array(
                    'message' =>  $e->getMessage()
                )
            );

            // throw new \Exception('Request failed: ' . $e->getMessage());
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
        $promises = [];

        foreach ($requests as $key => $request) {
            $options = [];

            if (!empty($request['queryParams'])) {
                $options['query'] = $request['queryParams'];
            }

            if (!empty($request['body'])) {
                $options['json'] = $request['body'];
            }

            // Add Authorization header
            $options['headers']['Authorization'] = 'Bearer ' . $this->retrieveAccessToken();

            // Create an async request
            $promises[$key] = $this->client->requestAsync($request['method'], $this->buildUrl($request['endpoint']), $options);
        }

        // Wait for all async requests to complete
        $responses = Utils::settle($promises)->wait();

        $results = [];

        foreach ($responses as $key => $result) {
            if ($result['state'] === 'fulfilled') {
                // Successfully received a response
                $body = json_decode($result['value']->getBody()->getContents(), true);
                $body['http_status_code'] = $result['value']->getStatusCode();
                $results[$key] = $body;
            } else {
                // Handle request failures
                $results[$key] = [
                    'error' => 'Request failed',
                    'code' => '2400',
                    'message' => $result['reason']->getMessage()
                ];
            }
        }

        return $results;
    }
}
