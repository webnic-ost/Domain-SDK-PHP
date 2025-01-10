<?php

namespace Webnic\WebnicSDK\Services\DNS;

use Webnic\WebnicSDK\Core\ApiConnector;

class DNSSubscriptionWhitelabel extends ApiConnector
{


    public function __construct(string $clientId, string $clientSecret, array $config)
    {
        parent::__construct($clientId, $clientSecret, $config);

        // Initialize the serviceUrl based on apiVersion
        $this->serviceUrl = '/dns' . $this->apiVersion . '/subscription/whitelabel/ns';
    }

    /**
     * Retrieves configured whitelabel nameserver details.
     *
     * This endpoint allows you to retrieve the details of the configured whitelabel nameservers.
     *
     * @return array The API response, including:
     *               - array `nameservers`: List of nameservers.
     *               - string `adminEmail`: The administrator email address.
     *
     * Example request:
     * ```php
     * $response = $webnicSDK->DNSSubscriptionWhitelabel->getWhitelabelNameserver();
     * ```
     */

    public function getWhitelabelNameserver(): array
    {
        return $this->sendRequest('GET', "");
    }

    /**
     * Saves whitelabel nameservers and verifies if they can resolve to designated IPs.
     *
     * This endpoint allows you to save whitelabel nameservers. The system will verify whether the submitted nameservers can resolve to the designated IPs.
     * Make sure the whitelabel nameservers have been properly configured before submitting.
     *
     * @param array $postField The request body, including:
     *                         - array `nameservers`: (Required) List of 4 nameservers.
     *                         - string `adminEmail`: (Required) Administrator email address.
     *
     * @return array The API response, including:
     *               - bool `submitted`: Returns true if the changes succeeded.
     *
     * Example request:
     * ```php
     * $postField = [
     *     'nameservers' => ["ns1.example.com", "ns2.example.com", "ns3.example.com", "ns4.example.com"],
     *     'adminEmail' => "admin@example.com"
     * ];
     * $response = $webnicSDK->DNSSubscriptionWhitelabel->saveWhitelabelNameservers($postField);
     * ```
     */

    public function saveWhitelabelNameservers(array $postField): array
    {
        return $this->sendRequest('POST', "", [], $postField);
    }

    /**
     * Removes whitelabel nameservers.
     *
     * This endpoint allows you to remove whitelabel nameservers. Once removed, the system will no longer use the specified nameservers for DNS resolution.
     *
     * @return array The API response, including:
     *               - bool `submitted`: Returns true if the changes succeeded.
     *
     * Example request:
     * ```php
     * $response = $webnicSDK->DNSSubscriptionWhitelabel->removeWhitelabelNameservers();
     * ```
     */

    public function removeWhitelabelNameservers(): array
    {
        return $this->sendRequest('DELETE', "");
    }
}
