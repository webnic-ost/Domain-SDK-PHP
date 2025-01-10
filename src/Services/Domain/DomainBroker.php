<?php

namespace Webnic\WebnicSDK\Services\Domain;

use Webnic\WebnicSDK\Core\ApiConnector;


class DomainBroker extends ApiConnector
{



    public function __construct(string $clientId, string $clientSecret, array $config)
    {
        parent::__construct($clientId, $clientSecret, $config);

        // Initialize the serviceUrl based on apiVersion
        $this->serviceUrl = '/domain' . $this->apiVersion;
    }

    /**
     * Initiate a domain brokerage service.
     *
     * This function helps you purchase a domain that is owned by someone else, while keeping you anonymous. You can specify the domain name and the amount you're willing to pay for it.
     *
     * @param array $postField An associative array containing:
     *   - `domainName` (string): The domain name to purchase (e.g., "example.com").
     *   - `amount` (float): The amount you are willing to pay for the domain in decimal form (e.g., 10.00).
     *
     * @return array The response data, including:
     *   - The result of the brokerage service initiation.
     *
     * Example request:
     * ```php
     * $postField = [
     *   'domainName' => 'example.com',
     *   'amount' => 10.00
     * ];
     * $response = $webnicSDK->domainBroker->initiateDomainBroker($postField);
     * ```
     */
    public function initiateDomainBroker(array $postField): array
    {
        return $this->sendRequest('POST', '/broker/initiate', [], $postField);
    }
}
