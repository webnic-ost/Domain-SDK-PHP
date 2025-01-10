<?php

namespace Webnic\WebnicSDK\Services\DNS;

use Webnic\WebnicSDK\Core\ApiConnector;

class DNSZoneDNSSEC extends ApiConnector
{


    public function __construct(string $clientId, string $clientSecret, array $config)
    {
        parent::__construct($clientId, $clientSecret, $config);

        // Initialize the serviceUrl based on apiVersion
        $this->serviceUrl = '/dns' . $this->apiVersion;
    }
    /**
     * Retrieves DNSSEC information for a domain zone.
     *
     * This endpoint allows you to get the DNSSEC (Domain Name System Security Extensions) information for a specified domain zone.
     * 
     * @param string $zone The domain zone for which to retrieve DNSSEC information (e.g., example.com).
     *
     * @return array The API response containing DNSSEC information for the domain zone:
     *   - `enabled` (boolean): Indicates whether DNSSEC is enabled for the domain zone.
     *   - `type` (string): The DNS record type that this signature covers.
     *   - `algorithm` (string): The cryptographic algorithm used to create the signature.
     *
     * Example request:
     * ```php
     * $zone = 'example.com';
     * $response = $webnicSDK->DNSZoneDNSSEC->getDomainZoneDNSSECInfo($zone);
     * ```
     */
    public function getDomainZoneDNSSECInfo(string $zone): array
    {
        return $this->sendRequest('GET', "/zone/$zone/subscription/dnssec/info");
    }

    /**
     * Enables DNSSEC for a domain zone.
     *
     * This endpoint allows you to enable DNSSEC (Domain Name System Security Extensions) for a specified domain zone.
     * 
     * @param string $zone The domain zone for which to enable DNSSEC (e.g., example.com).
     *
     * @return array The API response containing the DNSSEC status after the operation:
     *   - `enabled` (boolean): Indicates whether DNSSEC is enabled for the domain zone.
     *   - `type` (string): The DNS record type that this signature covers.
     *   - `algorithm` (string): The cryptographic algorithm used to create the signature.
     *
     * Example request:
     * ```php
     * $zone = 'example.com';
     * $response = $webnicSDK->DNSZoneDNSSEC->enableDomainZoneDNSSEC($zone);
     * ```
     */

    public function enableDomainZoneDNSSEC(string $zone): array
    {
        return $this->sendRequest('PUT', "/zone/$zone/subscription/dnssec/enable");
    }


    /**
     * Disables DNSSEC for a domain zone.
     *
     * This endpoint allows you to disable DNSSEC (Domain Name System Security Extensions) for a specified domain zone.
     * 
     * @param string $zone The domain zone for which to disable DNSSEC (e.g., example.com).
     *
     * @return array The API response containing the DNSSEC status after the operation:
     *   - `enabled` (boolean): Indicates whether DNSSEC is enabled for the domain zone.
     *   - `type` (string): The DNS record type that this signature covers.
     *   - `algorithm` (string): The cryptographic algorithm used to create the signature.
     *
     * Example request:
     * ```php
     * $zone = 'example.com';
     * $response = $webnicSDK->DNSZoneDNSSEC->disableDomainZoneDNSSEC($zone);
     * ```
     */
    public function disableDomainZoneDNSSEC(string $zone): array
    {
        return $this->sendRequest('PUT', "/zone/$zone/subscription/dnssec/disable");
    }


    /**
     * Retrieves the DNSSEC DNS key record for a domain zone.
     *
     * This endpoint allows you to get the DNSSEC DNS key record for a given domain zone, which includes information about the key's attributes, signatures, and related data.
     *
     * @param string $zone The domain zone for which to retrieve the DNSSEC DNS key record (e.g., example.com).
     *
     * @return array The API response containing the DNSSEC DNS key record details:
     *   - `type` (string): The record type.
     *   - `name` (string): The record name.
     *   - `ttl` (number): The time-to-live (TTL) in milliseconds, indicating how often a DNS server will refresh the record.
     *   - `rdatas` (array of objects): A list of rdata objects containing additional record data:
     *     - `value` (string): The record value.
     *     - `attributes` (object): Attributes associated with the record.
     *   - `dnstableId` (number): The DNS table ID.
     *   - `remarks` (string): Additional remarks about the record.
     *   - `rrsigs` (array of objects): A list of signature details, including:
     *     - `algorithm` (string): The cryptographic algorithm used to create the signature.
     *     - `labels` (number): The number of labels in the original RRSIG-record name, used for wildcard validation.
     *     - `originalTtl` (number): The TTL value of the covered record set.
     *     - `expiration` (string): The expiration date and time of the signature.
     *     - `inception` (string): The creation date and time of the signature.
     *     - `keyTag` (string): A numeric value to help identify the DNSKEY record used to validate the signature.
     *     - `signer` (string): The name of the DNSKEY record used to validate the signature.
     *     - `signature` (string): The cryptographic signature.
     *
     * Example request:
     * ```php
     * $zone = 'example.com';
     * $response = $webnicSDK->DNSZoneDNSSEC->getDomainZoneDNSSECDNSKeyRecord($zone);
     * ```
     */
    public function getDomainZoneDNSSECDNSKeyRecord(string $zone): array
    {
        return $this->sendRequest('GET', "/zone/$zone/subscription/dnssec/dnskey");
    }

    /**
     * Retrieves the DNS DS record for a domain zone.
     *
     * This endpoint allows you to get the DNS DS (Delegation Signer) record for a given domain zone, which includes the key tag, algorithm, and cryptographic hash information related to DNSSEC.
     *
     * @param string $zone The domain zone for which to retrieve the DNS DS record (e.g., example.com).
     *
     * @return array The API response containing the DNS DS record details:
     *   - `keyTag` (string): A short numeric value that helps identify the referenced DNSKEY record.
     *   - `algorithm` (string): The algorithm used for the referenced DNSKEY record.
     *   - `digestType` (string): The cryptographic hash algorithm used to create the digest value.
     *   - `digest` (string): The cryptographic hash value of the referenced DNSKEY record.
     *
     * Example request:
     * ```php
     * $zone = 'example.com';
     * $response = $webnicSDK->DNSZoneDNSSEC->getDomainZoneDNSSECDSRecord($zone);
     * ```
     */
    public function getDomainZoneDNSSECDSRecord(string $zone): array
    {
        return $this->sendRequest('GET', "/zone/$zone/subscription/dnssec/ds");
    }
}
