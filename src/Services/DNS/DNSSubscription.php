<?php

namespace Webnic\WebnicSDK\Services\DNS;

use Webnic\WebnicSDK\Core\ApiConnector;

class DNSSubscription extends ApiConnector
{


    public function __construct(string $clientId, string $clientSecret, array $config)
    {
        parent::__construct($clientId, $clientSecret, $config);

        // Initialize the serviceUrl based on apiVersion
        $this->serviceUrl = '/dns' . $this->apiVersion . '/subscription';
    }

    /**
     * Adds a domain zone to a partner subscription.
     *
     * This endpoint adds a domain zone to a partner subscription. Zone usage will be consumed.
     * This endpoint is not applicable for non-WebNIC domains.
     *
     * @param string $zone The domain zone to add to the partner subscription.
     *
     * @return array The API response, including:
     *               - string `zone`: The domain zone name.
     *               - string `zoneType`: The zone type (Possible values: "inzone" for WebNIC domain, "outzone" for Non-WebNIC domain).
     *               - string `subscription`: The partner subscription type.
     *               - string `subscriptionId`: The partner subscription ID.
     *               - string `dtcreate`: The date and time when the domain zone was created.
     *               - string `dtmodify`: The date and time when the domain zone was last modified.
     *
     * Example request:
     * ```php
     * $zone = "example.com";
     * $response = $webnicSDK->DNSSubscription->addDomainZoneToPartnerSubscription($zone);
     * ```
     */

    public function addDomainZoneToPartnerSubscription(string $zone): array
    {
        return $this->sendRequest('POST', "/partner/zone/$zone");
    }

    /** 
     * This endpoint allows you to remove domain zone from partner subscription. 
     *
     * @param string $zone The domain zone to remove domain zone from partner subscription. 
     *
     * Example request:
     * ```php
     * $zone = "example.com";
     * $response = $response = $webnicSDK->DNSSubscription->removeDomainZoneFromPartnerSubscription($zone);
     * ```
     */

    public function removeDomainZoneFromPartnerSubscription(string $zone): array
    {
        return $this->sendRequest('DELETE', "/partner/zone/$zone");
    }

    /**
     * Retrieves domain subscriptions with optional filtering criteria.
     *
     * @param array $params Query parameters for filtering the results:
     *                      - domainName (string, optional): Domain name to filter by.
     *                      - subscriptionId (string, optional): Subscription ID to filter by.
     *                      - subscriptionProduct (string, optional): Subscription Product Type. 
     *                        Allowed value: "premium_ns_georoute".
     *                      - subscriptionAutoRenew (string, optional): Indicates if the subscription is set to auto-renew.
     *                      - limit (int, optional): Number of domain subscriptions to be shown. 
     *                        Default: 10, Maximum: 100.
     * @return array The response containing domain subscription details.
     *
     * @throws \Exception If the request fails.
     */
    public function getDomainSubscription(array $filters): array
    {
        return $this->sendRequest('GET', "/standalone-domains", $filters);
    }

    /**
     * Subscribes a domain to a subscription product.
     *
     * @param array $postField The subscription details, including:
     *                         - string `domainName`: The domain name to subscribe (e.g., "boardingpass.com").
     *                         - string `subscriptionProduct`: The product to subscribe to (e.g., "premium_ns_georoute").
     *                         - int `subscriptionTerm`: The term of the subscription in months (e.g., 1 or 12).
     *                         - bool `subscriptionAutoRenew`: Whether to enable auto-renewal (e.g., false).
     *
     * @return array The API response, including:
     *               - string `domainName`: The domain name.
     *               - string `subscriptionId`: The subscription ID.
     *               - string `subscriptionProduct`: The subscription product type.
     *               - int `subscriptionTerm`: The subscription term in months.
     *               - string `subscriptionExpiry`: The subscription expiration date and time.
     *               - bool `subscriptionAutoRenew`: Whether auto-renewal is enabled.
     *               - string `subscriptionNextRenewal`: Date and time of the next renewal.
     *               - string `dtrenew`: The date and time the subscription was last renewed.
     *               - string `dtcreate`: The date and time when the subscription was created.
     *               - string `dtmodify`: The date and time when the subscription was last modified.
     *
     * Example request structure:
     * ```php
     * [
     *   "domainName" => "boardingpass.com",
     *   "subscriptionProduct" => "premium_ns_georoute",
     *   "subscriptionTerm" => 1,
     *   "subscriptionAutoRenew" => true
     * ]
     * ```
     */


    public function subscribeDomainSubscription(array $postField): array
    {
        return $this->sendRequest('POST', "/standalone-domains", [], $postField);
    }

    /**
     * Retrieves a domain subscription by its ID.
     *
     * @param string $subscriptionId The subscription ID to retrieve the domain subscription.
     *
     * @return array The API response, including:
     *               - string `domainName`: The domain name.
     *               - string `subscriptionId`: The subscription ID.
     *               - string `subscriptionProduct`: The subscription product type.
     *               - int `subscriptionTerm`: The subscription term in months.
     *               - string `subscriptionExpiry`: The subscription expiration date and time.
     *               - bool `subscriptionAutoRenew`: Whether auto-renewal is enabled.
     *               - string `subscriptionNextRenewal`: Date and time of the next renewal.
     *               - string `dtrenew`: The date and time the subscription was last renewed.
     *               - string `dtcreate`: The date and time when the subscription was created.
     *               - string `dtmodify`: The date and time when the subscription was last modified.
     *
     * Example request:
     * ```php
     * $subscriptionId = "your-subscription-id";
     * $response = $webnicSDK->DNSSubscription->getDomainSubscriptionById($subscriptionId);
     * ```
     */

    public function getDomainSubscriptionById(string $subscriptionId): array
    {
        return $this->sendRequest('GET', "/standalone-domains/$subscriptionId");
    }

    /**
     * Retrieves a domain subscription by its domain name.
     *
     * @param string $domainName The domain name to retrieve the domain subscription.
     *
     * @return array The API response, including:
     *               - string `domainName`: The domain name.
     *               - string `subscriptionId`: The subscription ID.
     *               - string `subscriptionProduct`: The subscription product type.
     *               - int `subscriptionTerm`: The subscription term in months.
     *               - string `subscriptionExpiry`: The subscription expiration date and time.
     *               - bool `subscriptionAutoRenew`: Whether auto-renewal is enabled.
     *               - string `subscriptionNextRenewal`: Date and time of the next renewal.
     *               - string `dtrenew`: The date and time the subscription was last renewed.
     *               - string `dtcreate`: The date and time when the subscription was created.
     *               - string `dtmodify`: The date and time when the subscription was last modified.
     *
     * Example request:
     * ```php
     * $domainName = "boardingpass.com";
     * $response = $webnicSDK->DNSSubscription->getDomainSubscriptionByDomainName($domainName);
     * ```
     */

    public function getDomainSubscriptionByDomainName(string $domainName): array
    {
        return $this->sendRequest('GET', "/standalone-domain/get-by-domain", ['domainName' => $domainName]);
    }

    /**
     * Renews a domain subscription.
     *
     * @param string $subscriptionId The subscription ID of the domain subscription to renew.
     * @param array $postField The renewal details, including:
     *                         - int `subscriptionTerm`: The subscription term in months (e.g., 1 or 12).
     *
     * @return array The API response, including:
     *               - string `domainName`: The domain name.
     *               - string `subscriptionId`: The subscription ID.
     *               - string `subscriptionProduct`: The subscription product type.
     *               - int `subscriptionTerm`: The subscription term in months.
     *               - string `subscriptionExpiry`: The subscription expiration date and time.
     *               - bool `subscriptionAutoRenew`: Whether auto-renewal is enabled.
     *               - string `subscriptionNextRenewal`: Date and time of the next renewal.
     *               - string `dtrenew`: The date and time the subscription was last renewed.
     *               - string `dtcreate`: The date and time when the subscription was created.
     *               - string `dtmodify`: The date and time when the subscription was last modified.
     *
     * Example request:
     * ```php
     * $subscriptionId = "your-subscription-id";
     * $postField = [
     *   "subscriptionTerm" => 12
     * ];
     * $response = $webnicSDK->DNSSubscription->renewDomainSubscription($subscriptionId, $postField);
     * ```
     */

    public function renewDomainSubscription(string $subscriptionId, array $postField): array
    {
        return $this->sendRequest('PUT', "/standalone-domain/$subscriptionId/renew", [], $postField);
    }


    /**
     * Unsubscribes a domain subscription.
     *
     * @param string $subscriptionId The subscription ID to unsubscribe the domain.
     *
     * @return array The API response, including:
     *               - string `domainName`: The domain name.
     *               - string `subscriptionId`: The subscription ID.
     *               - string `subscriptionProduct`: The subscription product type.
     *               - int `subscriptionTerm`: The subscription term in months.
     *               - string `subscriptionExpiry`: The subscription expiration date and time.
     *               - bool `subscriptionAutoRenew`: Whether auto-renewal is enabled.
     *               - string `subscriptionNextRenewal`: Date and time of the next renewal.
     *               - string `dtrenew`: The date and time the subscription was last renewed.
     *               - string `dtcreate`: The date and time when the subscription was created.
     *               - string `dtmodify`: The date and time when the subscription was last modified.
     *
     * Example request:
     * ```php
     * $subscriptionId = "your-subscription-id";
     * $response = $webnicSDK->DNSSubscription->unsubscribeDomainSubscription($subscriptionId);
     * ```
     */

    public function unsubscribeDomainSubscription(string $subscriptionId): array
    {
        return $this->sendRequest('DELETE', "/standalone-domain/$subscriptionId");
    }

    /**
     * Enables auto-renewal for a domain subscription.
     *
     * @param string $subscriptionId The subscription ID to enable auto-renewal.
     *
     * @return array The API response, including:
     *               - string `domainName`: The domain name.
     *               - string `subscriptionId`: The subscription ID.
     *               - string `subscriptionProduct`: The subscription product type.
     *               - int `subscriptionTerm`: The subscription term in months.
     *               - string `subscriptionExpiry`: The subscription expiration date and time.
     *               - bool `subscriptionAutoRenew`: Whether auto-renewal is enabled.
     *               - string `subscriptionNextRenewal`: Date and time of the next renewal.
     *               - string `dtrenew`: The date and time the subscription was last renewed.
     *               - string `dtcreate`: The date and time when the subscription was created.
     *               - string `dtmodify`: The date and time when the subscription was last modified.
     *
     * Example request:
     * ```php
     * $subscriptionId = "your-subscription-id";
     * $response = $webnicSDK->DNSSubscription->enableDomainSubscriptionAutoRenewal($subscriptionId);
     * ```
     */

    public function enableDomainSubscriptionAutoRenewal(string $subscriptionId): array
    {
        return $this->sendRequest('PUT', "/standalone-domain/$subscriptionId/auto-renewal/enable");
    }


    /**
     * This endpoint allows you to disable domain subscription auto renewal.
     *
     * @param string $subscriptionId The subscription ID to disable auto-renewal.
     *
     * @return array The API response, including:
     *               - string `domainName`: The domain name.
     *               - string `subscriptionId`: The subscription ID.
     *               - string `subscriptionProduct`: The subscription product type.
     *               - int `subscriptionTerm`: The subscription term in months.
     *               - string `subscriptionExpiry`: The subscription expiration date and time.
     *               - bool `subscriptionAutoRenew`: Whether auto-renewal is enabled.
     *               - string `subscriptionNextRenewal`: Date and time of the next renewal.
     *               - string `dtrenew`: The date and time the subscription was last renewed.
     *               - string `dtcreate`: The date and time when the subscription was created.
     *               - string `dtmodify`: The date and time when the subscription was last modified.
     *
     * Example request:
     * ```php
     * $subscriptionId = "your-subscription-id";
     * $response = $webnicSDK->DNSSubscription->disableDomainSubscriptionAutoRenewal($subscriptionId);
     * ```
     */
    public function disableDomainSubscriptionAutoRenewal(string $subscriptionId): array
    {
        return $this->sendRequest('PUT', "/standalone-domain/$subscriptionId/auto-renewal/disable");
    }
}
