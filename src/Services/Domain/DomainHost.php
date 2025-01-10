<?php

namespace Webnic\WebnicSDK\Services\Domain;

use Webnic\WebnicSDK\Core\ApiConnector;

/**
 * Class Domain
 *
 * Handles domain-related operations such as checking domain patterns,
 * querying domain information, updating nameservers, and managing domain statuses.
 *
 * @package Webnic\WebnicDomainSDK
 */
class DomainHost extends ApiConnector
{

    public function __construct(string $clientId, string $clientSecret, array $config)
    {
        parent::__construct($clientId, $clientSecret, $config);
        $this->serviceUrl = '/domain' . $this->apiVersion . '/host';
    }


    /**
     * Create a host by providing a list of extensions.
     *
     * This function allows you to create a host by specifying the host name, a list of IP addresses, and a list of domain extensions.
     *
     * @param array $postField The request body containing host information.
     * - host: The host name (e.g., "ns2.example.com").
     * - ipList: An array of IP addresses for the host (e.g., ["1.1.1.2", "1.1.1.3"]).
     * - extList: An array of extensions for the host (e.g., ["com.sg", "net.my", "org"]).
     *
     * @return array The response data, including success and failure details from the provider.
     *
     * Example request:
     * ```php
     * $postField = [
     *     'host' => 'ns2.example.com',
     *     'ipList' => ['1.1.1.2', '1.1.1.3', '1.1.1.4', '1.1.1.5'],
     *     'extList' => ['com.sg', 'net.my', 'org']
     * ];
     * $response = $webnicSDK->domainHost->createHostByExtension($postField);
     * ```
     */
    public function createHostByExtension(array $postField): array
    {
        return $this->sendRequest('POST', '/create/extension', [], $postField);
    }

    /**
     * Modify a host.
     *
     * This function allows you to modify an existing host by providing a new list of IP addresses.
     *
     * @param array $postField The data for modifying the host, including host name and IP list.
     * - host: The host name to modify (e.g., "ns1.example.com").
     * - ipList: The list of IP addresses to update for the host.
     *
     * @return array The response data from the modification request.
     *
     * Example request:
     * ```php
     * $postField = [
     *     'host' => 'ns1.example.com',
     *     'ipList' => [
     *         '1.1.1.2',
     *         '1.1.1.3',
     *         '1.1.1.4',
     *         '1.1.1.1'
     *     ]
     * ];
     * $response = $webnicSDK->domainHost->modifyHost($postField);
     * ```
     *
     */
    public function modifyHost(array $postField): array
    {
        return $this->sendRequest('POST', '/modify', [], $postField);
    }

    /**
     * Get host info.
     *
     * This function retrieves information about a specific host, including the associated IPs, registry list, and host name.
     *
     * @param string $host The host name to retrieve information for (e.g., "ns1.example.com").
     *
     * @return array The response data containing host information.
     *
     * Example request:
     * ```php
     * $host = 'ns1.example.com';
     * $response = $webnicSDK->domainHost->getHostInfo($host);
     * ```
     *
     */
    public function getHostInfo(string $host): array
    {
        return $this->sendRequest('GET', '/info', ['host' => $host]);
    }

    /**
     * Delete domain host by extension.
     *
     * This function deletes a domain host by specifying the host name and the extension name.
     *
     * @param string $host The host name to delete (e.g., "ns1.example.com").
     * @param string $ext The extension name (e.g., "com").
     *
     * @return array The response data after the deletion request.
     *
     * Example request:
     * ```php
     * $host = 'ns1.example.com';
     * $ext = 'com';
     * $response = $webnicSDK->domainHost->deleteHostByExtension($host, $ext);
     * ```
     *
     */

    public function deleteHostByExtension(string $host, string $ext): array
    {
        return $this->sendRequest('DELETE', '/extension', ['host' => $host, 'ext' => $ext]);
    }

    /**
     * Check if a nameserver is available for a specific extension.
     *
     * This function checks whether a nameserver is available for a given domain extension.
     *
     * @param string $nameserver The nameserver to check (e.g., "ns1.example.com").
     * @param string $ext The extension name (e.g., "com").
     *
     * @return array The response data indicating whether the nameserver is available.
     *
     * Example request:
     * ```php
     * $nameserver = 'ns10.example.com';
     * $ext = 'com';
     * $response = $webnicSDK->domainHost->checkHost($nameserver, $ext);
     * ```
     *
     */
    public function checkHost(string $nameserver, string $ext): array
    {
        return $this->sendRequest('POST', '/check', ['nameserver' => $nameserver, 'ext' => $ext]);
    }

    /**
     * Get the registered registries for a specific nameserver.
     *
     * This function retrieves the list of registries that have registered a specific nameserver.
     *
     * @param string $nameserver The nameserver to check (e.g., "ns1.example.com").
     *
     * @return array The response data containing the registered registries for the nameserver.
     *
     * Example request:
     * ```php
     * $nameserver = 'ns1.example.com';
     * $response = $webnicSDK->domainHost->getHostRegisteredRegistries($nameserver);
     * ```
     *
     */
    public function getHostRegisteredRegistries(string $nameserver): array
    {
        return $this->sendRequest('GET', '/registered-registries', ['nameserver' => $nameserver]);
    }
}
