<?php

namespace Webnic\WebnicSDK\Services\DNS;

use Webnic\WebnicSDK\Core\ApiConnector;

class DNSZoneForwarding extends ApiConnector
{


    public function __construct(string $clientId, string $clientSecret, array $config)
    {
        parent::__construct($clientId, $clientSecret, $config);

        // Initialize the serviceUrl based on apiVersion
        $this->serviceUrl = '/dns' . $this->apiVersion . '/zone';
    }
    /**
     * Retrieves URL forwarding settings for a specific domain zone.
     *
     * This endpoint allows you to fetch all URL forwarding settings for a given domain zone. It will return the list of subdomains that have URL forwarding set up, along with the target URLs, forwarding status, and timestamps of when the forwardings were created or last modified.
     * 
     * @param string $zone The domain zone from which to fetch URL forwarding details (e.g., `example.com`).
     *
     * @return array The API response containing a list of URL forwarding settings:
     *   - `zone` (string): The domain zone for which the URL forwardings are fetched.
     *   - `subdomain` (string): The subdomain with the URL forwarding.
     *   - `targetUrl` (string): The target URL that the subdomain forwards to.
     *   - `status` (string): The status of the URL forwarding (e.g., active, inactive).
     *   - `dtcreate` (string): Date and time when the URL forwarding was created.
     *   - `dtmodify` (string): Date and time when the URL forwarding was last modified.
     *
     * Example request:
     * ```php
     * $zone = '3promojmxtest280.com';
     * $response = $webnicSDK->DNSZoneForwarding->getZoneUrlForwardings($zone);
     * ```
     */
    public function getZoneUrlForwardings(string $zone): array
    {
        return $this->sendRequest('GET', "/$zone/url-forwardings");
    }

    /**
     * Adds URL forwarding for a specific subdomain in a domain zone.
     *
     * This endpoint allows you to set up URL forwarding for a specific subdomain in a domain zone. You must specify the subdomain and the target URL to which the subdomain should redirect.
     * Additionally, an optional parameter allows you to specify whether to override conflicting records, which will set the A record for the subdomain to the URL forwarding server's IP address.
     * 
     * Request Body:
     * - `subdomain` (string, required): The subdomain name that you want to forward (e.g., `subdomain.example.com`).
     * - `targetUrl` (string, required): The target URL address to which the subdomain will forward (e.g., `https://targetwebsite.com`).
     * - `overrideConflictingRecord` (boolean, optional): Indicates whether to override conflicting records. 
     *   Default value is `false`. If set to `true`, the A record for the subdomain will be set to the URL forwarding server IP address.
     *
     * @param string $zone The domain zone where the URL forwarding should be added (e.g., `example.com`).
     * @param array $postField The request body data, including:
     *   - `subdomain` (string, required): The subdomain name to be forwarded.
     *   - `targetUrl` (string, required): The target URL for the forwarding.
     *   - `overrideConflictingRecord` (boolean, optional): Whether to override conflicting records (default: `false`).
     *
     * @return array The API response containing details of the added URL forwarding.
     *   - `zone` (string): The domain zone for which URL forwarding was added.
     *   - `subdomain` (string): The subdomain that was set up for URL forwarding.
     *   - `targetUrl` (string): The target URL where the subdomain forwards.
     *   - `status` (string): The status of the URL forwarding.
     *   - `dtcreate` (string): Date and time when the URL forwarding was created.
     *   - `dtmodify` (string): Date and time when the URL forwarding was last modified.
     *
     * Example request:
     * ```php
     * $zone = '3promojmxtest280.com';
     * $postField = [
     *     'subdomain' => 'subdomain',
     *     'targetUrl' => 'https://targetwebsite.com',
     *     'overrideConflictingRecord' => true
     * ];
     * $response = $webnicSDK->DNSZoneForwarding->addZoneUrlForwarding($zone, $postField);
     * ```
     */
    public function addZoneUrlForwarding(string $zone, array $postField): array
    {
        return $this->sendRequest('POST', "/$zone/url-forwarding", [], $postField);
    }

    /**
     * Removes URL forwarding for a specific subdomain of a domain zone.
     *
     * This endpoint allows you to remove the URL forwarding configuration for a specific subdomain in a domain zone.
     * The request requires the zone name and the subdomain that you wish to remove the forwarding for.
     * 
     * @param string $zone The domain zone for which to remove URL forwarding (e.g., example.com).
     * @param string $subdomain The subdomain for which the URL forwarding is to be removed (e.g., subdomain.example.com).
     *
     * @return array The API response indicating whether the URL forwarding was successfully removed. 
     *   It typically includes a success flag and status information.
     *
     * Example request:
     * ```php
     * $zone = '3promojmxtest280.com';
     * $subdomain = 'subdomain';
     * $response = $webnicSDK->DNSZoneForwarding->removeZoneUrlForwarding($zone, $subdomain);
     * ```
     */
    public function removeZoneUrlForwarding(string $zone, string $subdomain): array
    {
        return $this->sendRequest('DELETE', "/$zone/url-forwarding", ["subdomain" => $subdomain]);
    }

    /**
     * Retrieves the list of email forwardings for a domain zone.
     *
     * This endpoint allows you to get all the email forwarding configurations for a specific domain zone.
     * The response will include details about each forwarding, such as the user, the target email, status, and timestamps of creation and modification.
     * 
     * @param string $zone The domain zone for which to get email forwardings (e.g., example.com).
     *
     * @return array The API response containing the list of email forwardings for the domain zone, including:
     *   - `zone` (string): The domain zone for which email forwarding is configured.
     *   - `user` (string): The username whose email forwarding is set up.
     *   - `targetEmail` (string): The target email address to which emails are forwarded.
     *   - `status` (string): The current status of the email forwarding.
     *   - `dtcreate` (string): The date and time when the forwarding was created.
     *   - `dtmodify` (string): The date and time when the forwarding was last modified.
     *
     * Example request:
     * ```php
     * $zone = 'example.com';
     * $response = $webnicSDK->DNSZoneForwarding->getZoneEmailForwardings($zone);
     * ```
     */
    public function getZoneEmailForwardings(string $zone): array
    {
        return $this->sendRequest('GET', "/$zone/email-forwardings");
    }

    /**
     * Adds email forwarding for a specific user in a domain zone.
     *
     * This endpoint allows you to set up email forwarding for a specific user in a domain zone. The user's email will be forwarded to the target email provided in the request.
     * Additionally, an MX record will be added to point to the forwarding server `mail.ezydomain.com`. 
     * It is important to note that no other MX records should be added, as this will interfere with the forwarding service.
     * 
     * @param string $zone The domain zone for which to add email forwarding (e.g., example.com).
     * @param array $postField The request body data, including:
     *   - `user` (string, required): The username for which email forwarding will be set up.
     *   - `targetEmail` (string, required): The email address to which the user's emails will be forwarded.
     *   - `overrideConflictingRecord` (boolean, optional): If true, conflicting MX records will be overridden. Default is false.
     *
     * @return array The API response containing the details of the added email forwarding.
     *
     * Example request:
     * ```php
     * $zone = 'example.com';
     * $postField = [
     *     'user' => 'user',
     *     'targetEmail' => 'angie@sample.com',
     *     'overrideConflictingRecord' => false,
     * ];
     * $response = $webnicSDK->DNSZoneForwarding->addZoneEmailForwarding($zone, $postField);
     * ```
     */
    public function addZoneEmailForwarding(string $zone, array $postField): array
    {
        return $this->sendRequest('POST', "/$zone/email-forwarding", [], $postField);
    }


    /**
     * Removes email forwarding for a specific user in a domain zone.
     *
     * This endpoint allows you to remove the email forwarding for a specific user in a domain zone.
     * 
     * @param string $zone The domain zone for which to remove email forwarding (e.g., example.com).
     * @param string $user The username whose email forwarding will be removed (e.g., user1).
     *
     * @return array The API response confirming the removal of email forwarding.
     *
     * Example request:
     * ```php
     * $zone = 'example.com';
     * $user = 'user1';
     * $response = $webnicSDK->DNSZoneForwarding->removeZoneEmailForwarding($zone, $user);
     * ```
     */
    public function removeZoneEmailForwarding(string $zone, string $user): array
    {
        return $this->sendRequest('DELETE', "/$zone/email-forwarding", ["user" => $user]);
    }
}
