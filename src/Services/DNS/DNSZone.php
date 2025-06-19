<?php

namespace Webnic\WebnicSDK\Services\DNS;

use Webnic\WebnicSDK\Core\ApiConnector;

class DNSZone extends ApiConnector
{


    public function __construct(string $clientId, string $clientSecret, array $config)
    {
        parent::__construct($clientId, $clientSecret, $config);

        // Initialize the serviceUrl based on apiVersion
        $this->serviceUrl = '/dns' . $this->apiVersion;
    }

    /**
     * Retrieves domain zones.
     *
     * This endpoint allows you to retrieve domain zones. You can filter the results using various query parameters such as zone, zone type, subscription, subscription ID, and limit.
     *
     * Query Parameters:
     * - `zone` (string, optional): Zone name, e.g., example.com.
     * - `zoneType` (string, optional): Zone type.
     *   Allowed values: `"inzone"` (WebNIC domain), `"outzone"` (Non-WebNIC domain).
     * - `subscription` (string, optional): Subscription type.
     *   Allowed values: `"premium_ns_partner"` (high-performance DNS services with advanced management tools and enhanced security features), `"premium_ns_georoute"` (includes geolocation routing).
     * - `subscriptionId` (string, optional): Subscription ID.
     * - `limit` (int, optional): Number of domain zones to be returned.
     *   Default: 10, Maximum: 100.
     *
     * @param array $filters The query parameters for filtering domain zones.
     * @return array The API response, including:
     *               - string `zone`: The domain zone.
     *               - string `zoneType`: The zone type (e.g., "inzone" or "outzone").
     *               - string `subscription`: The subscription type.
     *               - string `subscriptionId`: The subscription ID.
     *               - string `dtcreate`: The date and time the domain zone was created.
     *               - string `dtmodify`: The date and time the domain zone was modified.
     *
     * Example request:
     * ```php
     * $filters = [
     *     'zone' => 'example.com',
     *     'limit' => 10,
     * ];
     * $response = $webnicSDK->DNSZone->getDomainZones($filters);
     * ```
     */
    public function getDomainZones(array $filters): array
    {
        return $this->sendRequest('GET', "/zones", $filters);
    }
    /**
     * Adds a new domain zone.
     *
     * This endpoint allows you to add a domain zone to the system. You must provide the zone name in the request body. The zone type will be automatically determined based on whether it's a WebNIC domain or not.
     *
     * Request Body:
     * - `zone` (string, required): The domain zone name to be added, e.g., `example.com`.
     *
     * @param array $postField The request body data, including:
     *   - `zone` (string, required): The zone name to be added.
     *
     * @return array The API response containing the details of the added domain zone:
     *   - `zone` (string): The added domain zone.
     *   - `zoneType` (string): The type of zone.
     *     Possible values:
     *       - `"inzone"` (WebNIC domain)
     *       - `"outzone"` (Non-WebNIC domain)
     *   - `subscription` (string): The subscription associated with the zone.
     *   - `subscriptionId` (string): The subscription ID for the zone.
     *   - `dtcreate` (string): The date and time when the domain zone was created.
     *   - `dtmodify` (string): The date and time when the domain zone was last modified.
     *
     * Example request:
     * ```php
     * $postField = [
     *     'zone' => 'example.com',
     * ];
     * $response = $webnicSDK->DNSZone->addDomainZone($postField);
     * ```
     */
    public function addDomainZone(array $postField): array
    {
        return $this->sendRequest('POST', "/zone", [], $postField);
    }


    /**
     * Retrieves domain zone details by zone.
     *
     * This endpoint allows you to retrieve information about a specific domain zone by providing the zone name in the URL path. The response will contain details such as the zone type, associated subscription, and timestamps for creation and modification.
     *
     * Path Variables:
     * - `zone` (string, required): The domain zone name, e.g., `example.com`.
     *
     * @param string $zone The domain zone name to be retrieved.
     *
     * @return array The API response containing details about the specified domain zone:
     *   - `zone` (string): The domain zone.
     *   - `zoneType` (string): The type of zone.
     *     Possible values: 
     *       - `"inzone"` (WebNIC domain)
     *       - `"outzone"` (Non-WebNIC domain)
     *   - `subscription` (string): The subscription associated with the zone.
     *   - `subscriptionId` (string): The subscription ID for the zone.
     *   - `dtcreate` (string): The date and time when the domain zone was created.
     *   - `dtmodify` (string): The date and time when the domain zone was last modified.
     *
     * Example request:
     * ```php
     * $zone = 'domaintesting123.com';
     * $response = $webnicSDK->DNSZone->getDomainZone($zone);
     * ```
     */
    public function getDomainZone(string $zone): array
    {
        return $this->sendRequest('GET', "/zone/$zone");
    }

    /**
     * Deletes a domain zone from your subscription.
     *
     * This endpoint allows you to delete a domain zone from your subscription. You need to provide the zone name in the path parameter.
     *
     * @param string $zone The domain zone name to be deleted, e.g., `example.com`.
     *
     * @return array The API response containing the message of the delete operation.
     * Example request:
     * ```php
     * $zone = 'example.com';
     * $response = $webnicSDK->DNSZone->deleteDomainZone($zone);
     * ```
     */
    public function deleteDomainZone(string $zone): array
    {
        return $this->sendRequest('DELETE', "/zone/$zone");
    }

    /**
     * Retrieves domain zone statistics.
     *
     * This endpoint allows you to get statistics about the domain zones in your account, including the number of domain zones under various types of subscriptions.
     *
     * @return array The API response containing the domain zone statistics:
     *   - `totalBasicNs` (int): The total number of domain zones under the basic nameserver subscription.
     *   - `totalPremiumNs` (int): The total number of domain zones under the premium nameserver subscription.
     *   - `totalGeoroute` (int): The total number of domain zones under the premium nameserver with georoute subscription.
     *
     * Example request:
     * ```php
     * $response = $webnicSDK->DNSZone->getDomainZoneStatistics();
     * ```
     */
    public function getDomainZoneStatistics(): array
    {
        return $this->sendRequest('GET', "/statistics");
    }

    /**
     * Retrieves premium subscription statistics for the past 30 days.
     *
     * This endpoint allows you to get the statistics for premium subscriptions, including the number of records and the current date.
     *
     * @return array The API response containing the premium subscription statistics:
     *   - `records` (int): The number of records for the past 30 days.
     *   - `activityDate` (string): The current date for the statistics.
     *
     * Example request:
     * ```php
     * $response = $webnicSDK->DNSZone->getPremiumSubscriptionStatistics();
     * ```
     */
    public function getPremiumSubscriptionStatistics(): array
    {
        return $this->sendRequest('GET', "/premium-subscription/statistics");
    }

    public function addDomainZoneToNSSubscription(string $zone, array $postField): array
    {
        return $this->sendRequest('POST', "/zone/$zone/nameserver-subscription", [], $postField);
    }
}
