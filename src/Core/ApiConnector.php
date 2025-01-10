<?php


namespace Webnic\WebnicSDK\Core;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;

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
        if ($this->apiKey !== null && $this->isTokenValid()) {
            return $this->apiKey; // Return existing valid token
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
                $this->apiKey = $data['data']['access_token'];
                $this->tokenExpiration = new \DateTime('+40 minutes'); // Set token expiration time to 40 minutes
                return $this->apiKey;
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
            if ($e instanceof \GuzzleHttp\Exception\ClientException) {
                $response = $e->getResponse();
                if ($response) {
                    $responseBody = json_decode($response->getBody()->getContents(), true);
                    $responseBody['http_status_code'] = $response->getStatusCode();
                    return $responseBody;
                }
            }
            throw new \Exception('Request failed: ' . $e->getMessage());
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
            throw new \Exception('Request failed: ' . $e->getMessage());
        }
    }
}
