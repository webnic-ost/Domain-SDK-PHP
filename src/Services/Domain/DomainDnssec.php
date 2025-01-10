<?php

namespace Webnic\WebnicSDK\Services\Domain;

use Webnic\WebnicSDK\Core\ApiConnector;


class DomainDnssec extends ApiConnector
{



    public function __construct(string $clientId, string $clientSecret, array $config)
    {
        parent::__construct($clientId, $clientSecret, $config);
        $this->serviceUrl = '/domain' . $this->apiVersion . '/dnssec';
    }


    /**
     * Check whether the domain's DNSSEC is supported or not.
     *
     * This function checks if a domain supports DNSSEC (Domain Name System Security Extensions).
     *
     * @param string $domainName The domain name to check (e.g., "example.com").
     *
     * @return array The response data, which includes:
     *   - `dnssecSupported` (bool): Indicates whether DNSSEC is supported for the domain.
     *
     * Example request:
     * ```php
     * $domainName = 'example.com';
     * $response = $webnicSDK->domainDnssec->checkDnssecSupported($domainName);
     * ```
     */
    public function checkDnssecSupported(string $domainName): array
    {
        return $this->sendRequest('GET', '/support', ['domainName' => $domainName]);
    }


    /**
     * Get the DNSSEC information for a domain.
     *
     * This function retrieves the DNSSEC (Domain Name System Security Extensions) info for a specified domain.
     *
     * @param string $domainName The domain name to get the DNSSEC info for (e.g., "example.com").
     *
     * @return array The response data, which includes DNSSEC information:
     *   - `dsDatas` (array of objects): DNSSEC information, with each object containing:
     *     - `keyTag` (string): DNSSEC key tag (0 - 65535)
     *     - `algorithm` (string): DNSSEC algorithm (e.g., 2, 3, 4, 5, etc. as per RFC 4034, Appendix A.1)
     *     - `digestType` (string): DNSSEC digest type (1 = SHA-1, 2 = SHA-256, etc.)
     *     - `digest` (string): Digest value for DNSSEC.
     *
     * Example request:
     * ```php
     * $domainName = 'example.com';
     * $response = $webnicSDK->domainDnssec->getDnssecInfo($domainName);
     * ```
     */
    public function getDnssecInfo(string $domainName): array
    {
        return $this->sendRequest('GET', '', ['domainName' => $domainName]);
    }
    /**
     * Update the DNSSEC information for a domain.
     *
     * This function allows updating the DNSSEC (Domain Name System Security Extensions) information for a domain.
     *
     * @param string $domainName The domain name to update the DNSSEC info for (e.g., "example.com").
     * @param array $postField The DNSSEC information to update. The required fields include:
     *   - `dsDatas` (array of objects): DNSSEC data to update, with each object containing:
     *     - `keyTag` (string): DNSSEC key tag (0 - 65535)
     *     - `algorithm` (string): DNSSEC algorithm (e.g., 2, 3, 4, 5, etc. as per RFC 4034, Appendix A.1)
     *     - `digestType` (string): DNSSEC digest type (1 = SHA-1, 2 = SHA-256, etc.)
     *     - `digest` (string): Digest value for DNSSEC.
     *
     * @return array The response data from the update request.
     * 
     * Example request:
     * ```php
     * $domainName = 'example.com';
     * $dnssecData = [
     *     'dsDatas' => [
     *         [
     *             'keyTag' => '111',
     *             'algorithm' => '12',
     *             'digestType' => '3',
     *             'digest' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'
     *         ],
     *         [
     *             'keyTag' => '112',
     *             'algorithm' => '12',
     *             'digestType' => '4',
     *             'digest' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'
     *         ]
     *     ]
     * ];
     * $response = $webnicSDK->domainDnssec->updateDnssec($domainName, $dnssecData);
     * ``` 
     */
    public function updateDnssec(string $domainName, array $postField): array
    {
        return $this->sendRequest('POST', '', ['domainName' => $domainName], $postField);
    }


    /**
     * Delete (disable) DNSSEC for a domain.
     *
     * This function allows disabling the DNSSEC (Domain Name System Security Extensions) for a domain by deleting its DNSSEC records.
     *
     * @param string $domainName The domain name to delete DNSSEC info for (e.g., "example.com").
     *
     * @return array The response data from the delete request.
     *
     * Example request:
     * ```php
     * $domainName = 'example.com';
     * $response = $webnicSDK->domainDnssec->deleteDnssec($domainName);
     * ```
     */
    public function deleteDnssec(string $domainName): array
    {
        return $this->sendRequest('DELETE', '', ['domainName' => $domainName]);
    }
}
