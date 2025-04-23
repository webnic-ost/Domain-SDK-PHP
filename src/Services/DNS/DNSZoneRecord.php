<?php

namespace Webnic\WebnicSDK\Services\DNS;

use Webnic\WebnicSDK\Core\ApiConnector;

class DNSZoneRecord extends ApiConnector
{


    public function __construct(string $clientId, string $clientSecret, array $config)
    {
        parent::__construct($clientId, $clientSecret, $config);

        // Initialize the serviceUrl based on apiVersion
        $this->serviceUrl = '/dns' . $this->apiVersion . '/zone';
    }
    /**
     * Retrieves the DNS records for a given zone.
     *
     * This endpoint first fetches the zone subscription records. If no zone subscription records exist, it retrieves the basic zone records.
     *
     * @param string $zone The domain zone (e.g., example.com).
     * @param array $postField An associative array of query parameters to filter the records.
     *     - `type` (string, optional): The type of the record (e.g., 'A', 'AAAA', 'CNAME', 'MX').
     *     - `name` (string, optional): The name of the record.
     *
     * @return array The API response containing zone records:
     *   - `records` (object[]): An array of zone record data objects.
     *     - `type` (string): The type of the DNS record.
     *     - `name` (string): The name of the DNS record.
     *     - `ttl` (number): The time-to-live (TTL) in milliseconds for the record.
     *     - `rdatas` (object[]): A list of rdata objects with record values and metadata.
     *       - `value` (string): The value of the DNS record.
     *       - `attributes` (object): Metadata related to the DNS record.
     *     - `remarks` (string): Additional remarks about the record.
     *     - `sourceFrom` (string): The source from where the records were fetched (e.g., 'subscription', 'basic').
     *
     * Example usage:
     * ```php
     * $response = $webnicSDK->DNSZoneRecord->getZoneRecords('example.com', ['type' => 'A']);
     * ```
     * Response example:
     * ```php
     * [
     *     'records' => [
     *         [
     *             'type' => 'A',
     *             'name' => 'www',
     *             'ttl' => 3600,
     *             'rdatas' => [
     *                 [
     *                     'value' => '192.0.2.1',
     *                     'attributes' => ['priority' => 1],
     *                 ],
     *             ],
     *             'remarks' => 'Main A record for www',
     *             'sourceFrom' => 'subscription',
     *         ],
     *     ],
     * ]
     * ```
     */
    public function getZoneRecords(string $zone, array $postField): array
    {
        return $this->sendRequest('GET', "/zone/$zone/records", [], $postField);
    }


    /**
     * Retrieves the list of supported DNS record types.
     *
     * This endpoint allows you to fetch the list of record types that are supported by the DNS service.
     *
     * @return array The API response containing the list of supported record types:
     *   - `data` (string[]): An array of supported record types.
     *
     * Example usage:
     * ```php
     * $response = $webnicSDK->DNSZoneRecord->getSupportedRecordTypes();
     * ```
     * Response example:
     * ```php
     * [
     *     'data' => [
     *         'A',
     *         'AAAA',
     *         'CNAME',
     *         'MX',
     *         'TXT',
     *     ],
     * ]
     * ```
     */
    public function getSupportedRecordTypes(): array
    {
        return $this->sendRequest('GET', "/record-types");
    }


    /**
     * Retrieves the basic record nameservers (basic NS).
     *
     * This endpoint allows you to fetch the list of nameservers associated with basic DNS records.
     *
     * @return array The API response containing the list of basic record nameservers:
     *   - `data` (string[]): An array of basic record nameservers.
     *
     * Example usage:
     * ```php
     * $response = $webnicSDK->DNSZoneRecord->getBasicRecordNameservers();
     * ```
     * Response example:
     * ```php
     * [
     *     'data' => [
     *         'ns1.basicdns.com',
     *         'ns2.basicdns.com',
     *     ],
     * ]
     * ```
     */
    public function getBasicRecordNameservers(): array
    {
        return $this->sendRequest('GET', "/basic/record/nameservers");
    }
    /**
     * Retrieves the nameservers for the subscription record.
     *
     * This endpoint allows you to fetch the list of nameservers associated with the subscription record. For Premium DNS subscribers, a whitelabel value is provided.
     *
     * @return array The API response containing the list of nameservers:
     *   - `data` (string[]): An array of nameservers.
     *
     * Example usage:
     * ```php
     * $response = $webnicSDK->DNSZoneRecord->getSubscriptionRecordNameservers();
     * ```
     * Response example:
     * ```php
     * [
     *     'data' => [
     *         'ns1.premiumdns.com',
     *         'ns2.premiumdns.com',
     *         'ns3.premiumdns.com',
     *         'ns4.premiumdns.com',
     *     ],
     * ]
     * ```
     */
    public function getSubscriptionRecordNameservers(): array
    {
        return $this->sendRequest('GET', "/subscription/record/nameservers");
    }

    /**
     * Retrieves basic DNS records for the specified zone.
     *
     * This endpoint allows you to fetch a list of basic DNS records associated with a specific zone. You can filter the records based on their type or name using query parameters.
     *
     * @param string $zone The domain zone for which the records are to be retrieved (e.g., `example.com`).
     * @param array $params The query parameters to filter the records:
     *   - `type` (string, optional): The type of DNS records to retrieve (e.g., `A`, `CNAME`, `MX`).
     *   - `name` (string, optional): The name of the DNS record to filter (e.g., `www`).
     *
     * @return array The API response containing the list of DNS records:
     *   - Each record in the response will include:
     *     - `name` (string): The record name.
     *     - `type` (string): The record type.
     *     - `value` (string): The record value.
     *
     * Example usage:
     * ```php
     * $zone = 'example.com';
     * $params = [
     *     'type' => 'A',
     *     'name' => 'www',
     * ];
     * $response = $webnicSDK->DNSZoneRecord->getZoneBasicRecords($zone, $params);
     * ```
     * Response example:
     * ```php
     * [
     *     [
     *         'name' => 'www',
     *         'type' => 'A',
     *         'value' => '192.0.2.1',
     *     ],
     *     [
     *         'name' => 'mail',
     *         'type' => 'MX',
     *         'value' => 'mail.example.com',
     *     ],
     * ]
     * ```
     */

    public function getZoneBasicRecords(string $zone, array $params): array
    {
        return $this->sendRequest('GET', "/$zone/basic/records", ['type' => $params['type'] ?? "", 'name' => $params['name'] ?? ""]);
    }


    /**
     * Saves a basic DNS record for the specified zone.
     *
     * This endpoint allows you to save or add a basic DNS record to a specific zone. The record details, such as name, type, and value, are provided in the request body.
     *
     * @param string $zone The domain zone for which the record is to be saved (e.g., `example.com`).
     * @param array $postField The request body containing the record details:
     *   - `name` (string, required): The record name. Use `@` or leave blank to denote the current origin.
     *   - `type` (string, required): The type of the DNS record (e.g., `A`, `CNAME`, `MX`).
     *   - `value` (string, required): The value of the DNS record.
     *   - `remarks` (string, optional): Any additional remarks or metadata for the record.
     *
     * @return array The API response indicating the result of the operation:
     *   - `saved` (boolean): `true` if the record was successfully saved, `false` otherwise.
     *   - `record` (object): Details of the saved record, including:
     *     - `name` (string): The record name.
     *     - `type` (string): The record type.
     *     - `value` (string): The record value.
     *     - `remarks` (string): Additional remarks for the record.
     *
     * Example request:
     * ```php
     * $zone = 'example.com';
     * $postField = [
     *     'name' => 'www',
     *     'type' => 'A',
     *     'value' => '192.0.2.1',
     *     'remarks' => 'Web server IP',
     * ];
     * $response = $webnicSDK->DNSZoneRecord->saveZoneBasicRecord($zone, $postField);
     * ```
     */

    public function saveZoneBasicRecord(string $zone, array $postField): array
    {
        return $this->sendRequest('POST', "/$zone/basic/record", [], $postField);
    }

    /**
     * Deletes a specific basic DNS record from the specified zone.
     *
     * This endpoint allows you to delete a zone's basic record based on the record type and name provided in the query parameters.
     *
     * @param string $zone The domain zone for which the record is to be deleted (e.g., `example.com`).
     * @param array $params Query parameters to specify the record to be deleted:
     *   - `type` (string, required): The type of the DNS record (e.g., `A`, `CNAME`, `MX`).
     *   - `name` (string, required): The name of the DNS record (e.g., `mail2`).
     * @param array $postField Additional data to send in the request body, if required.
     *
     * @return array The API response indicating whether the record was successfully removed:
     *   - `removed` (boolean): `true` if the record was successfully removed, `false` otherwise.
     *
     * Example request:
     * ```php
     * $zone = 'example.com';
     * $params = [
     *     'type' => 'A',
     *     'name' => 'mail2',
     * ];
     * $postField = []; // Add any necessary additional fields here.
     * $response = $webnicSDK->DNSZoneRecord->deleteZoneBasicRecord($zone, $params, $postField);
     * ```
     */
    public function deleteZoneBasicRecord(string $zone, array $params): array
    {
        return $this->sendRequest('DELETE', "$zone/basic/record", ['type' => $params['type'], 'name' => $params['name']]);
    }

    /**
     * Deletes a specific DNS record from the specified zone.
     *
     * This endpoint allows you to delete a zone's record based on the record type and name provided in the query parameters.
     *
     * @param string $zone The domain zone for which the record is to be deleted (e.g., `example.com`).
     * @param array $params Query parameters to specify the record to be deleted:
     *   - `type` (string, required): The type of the DNS record (e.g., `A`, `CNAME`, `MX`).
     *   - `name` (string, required): The name of the DNS record (e.g., `mail2`). 
     *
     * @return array The API response indicating whether the record was successfully removed:
     *   - `removed` (boolean): `true` if the record was successfully removed, `false` otherwise.
     *
     * Example request:
     * ```php
     * $zone = 'example.com';
     * $params = [
     *     'type' => 'A',
     *     'name' => 'mail2',
     * ]; 
     * $response = $webnicSDK->DNSZoneRecord->deleteZoneRecord($zone, $params);
     * ```
     */
    public function deleteZoneRecord(string $zone, array $params): array
    {
        return $this->sendRequest('DELETE', "/$zone/record", ['type' => $params['type'], 'name' => $params['name']]);
    }

    /**
     * Retrieves subscription records for a specific domain zone.
     *
     * This endpoint allows you to get zone subscription records based on optional filters such as record type and record name.
     *
     * @param string $zone The domain zone for which the records are to be retrieved (e.g., `example.com`).
     * @param array $params Optional query parameters to filter the records:
     *   - `type` (string, optional): The type of the DNS record (e.g., `A`, `CNAME`, `MX`).
     *   - `name` (string, optional): The name of the DNS record (e.g., `www`).
     *
     * @return array The API response containing the list of subscription records:
     *   - `type` (string): The type of the DNS record.
     *   - `name` (string): The name of the DNS record.
     *   - `ttl` (number): The TTL value (in milliseconds), indicating how often a DNS server should refresh the record.
     *   - `rdatas` (array): List of rdata objects:
     *     - `value` (string): The value of the DNS record.
     *     - `attributes` (object): Additional metadata as key-value pairs related to the DNS record.
     *   - `remarks` (string): Any additional remarks for the record.
     *
     * Example request:
     * ```php
     * $zone = 'captainmarvel3.com.mm';
     * $params = [
     *     'type' => 'A',
     *     'name' => 'www',
     * ];
     * $response = $webnicSDK->DNSZoneRecord->getZoneSubscriptionRecords($zone, $params);
     * ```
     */
    public function getZoneSubscriptionRecords(string $zone, array $params): array
    {
        return $this->sendRequest('GET', "/$zone/subscription/records", ['type' => $params['type'], 'name' => $params['name']]);
    }

    /**
     * Adds a zone record to a domain zone.
     *
     * This endpoint allows you to add a new zone record within a specified domain zone. You need to provide details such as the record name, type, TTL, and rdata.
     *
     * @param string $zone The domain zone to which the record will be added (e.g., `example.com`).
     * @param array $postField The request body data containing details of the record to add:
     *   - `name` (string, required): The name of the record.
     *   - `type` (string, required): The type of the record (e.g., `A`, `CNAME`).
     *   - `ttl` (number, optional): The TTL value (in milliseconds) indicating how often a DNS server should refresh the record.
     *   - `rdatas` (array, required): List of rdata objects:
     *     - `value` (string, required): The value of the record.
     *     - `attributes` (object, optional): Additional key-value pairs for extra metadata related to the record:
     *       - `sub-attribute1`: (optional) Sub-attribute details.
     *       - `sub-attribute2`: (optional) Sub-attribute details.
     *   - `remarks` (string, optional): Additional remarks.
     *
     * @return array The API response containing details of the added record:
     *   - `record` (object): Details of the added record:
     *     - `type` (string): The record type.
     *     - `name` (string): The record name.
     *     - `ttl` (number): The TTL value (in milliseconds).
     *     - `rdatas` (array): List of rdata objects:
     *       - `value` (string): The record value.
     *       - `attributes` (object): Additional metadata related to the record.
     *     - `remarks` (string): Additional remarks for the record.
     *
     * Example request:
     * ```php
     * $zone = '2webnic-excel2.com';
     * $postField = [
     *     'name' => 'www',
     *     'type' => 'A',
     *     'ttl' => 3600,
     *     'rdatas' => [
     *         [
     *             'value' => '192.0.2.1',
     *             'attributes' => [
     *                 'country' => 'MY',
     *             ],
     *         ],
     *     ],
     *     'remarks' => 'Adding A record for www',
     * ];
     * $response = $webnicSDK->DNSZoneRecord->addZoneSubscriptionRecord($zone, $postField);
     * ```
     */
    public function addZoneSubscriptionRecord(string $zone, array $postField): array
    {
        return $this->sendRequest('POST', "/$zone/subscription/record", [], $postField);
    }


    /**
     * Removes a specific zone record from a domain zone.
     *
     * This endpoint allows you to remove a specific zone record within a domain zone. You need to provide the record name, type, and rdata details.
     *
     * @param string $zone The domain zone from which to remove the record (e.g., `example.com`).
     * @param array $postField The request body data containing details of the record to remove:
     *   - `name` (string, required): The name of the record.
     *   - `type` (string, required): The type of the record (e.g., `A`, `CNAME`).
     *   - `rdatas` (array, required): List of rdata objects:
     *     - `value` (string, required): The value of the record.
     *     - `attributes` (object, optional): Additional key-value pairs for extra metadata.
     *
     * @return array The API response containing details of the removed record:
     *   - `record` (object): Details of the removed record:
     *     - `type` (string): The record type.
     *     - `name` (string): The record name.
     *     - `ttl` (number): The TTL value (in milliseconds).
     *     - `rdatas` (array): List of rdata objects:
     *       - `value` (string): The record value.
     *       - `attributes` (object): Additional metadata related to the record.
     *     - `dnstableId` (number): The DNS table ID.
     *     - `remarks` (string): Additional remarks for the record.
     *
     * Example request:
     * ```php
     * $zone = 'captainmarvel3.com.mm';
     * $postField = [
     *     'name' => 'www',
     *     'type' => 'A',
     *     'rdatas' => [
     *         ['value' => '192.0.2.1']
     *     ],
     * ];
     * $response = $webnicSDK->DNSZoneRecord->removeZoneSubscriptionRecord($zone, $postField);
     * ```
     */
    public function removeZoneSubscriptionRecord(string $zone, array $postField): array
    {
        return $this->sendRequest('POST', "/$zone/subscription/record/remove", [], $postField);
    }

    /**
     * Replaces a zone record in a domain zone.
     *
     * This endpoint allows you to replace an existing zone record within a domain zone. You need to provide the record name, type, and rdata details.
     *
     * @param string $zone The domain zone in which to replace the record (e.g., `example.com`).
     * @param array $postField The request body data containing details of the record to replace:
     *   - `name` (string, required): The name of the record.
     *   - `type` (string, required): The type of the record (e.g., `A`, `CNAME`).
     *   - `ttl` (number, optional): How often a DNS server will refresh the record (in milliseconds).
     *   - `rdatas` (array, required): List of rdata objects:
     *     - `value` (string, required): The value of the record.
     *     - `attributes` (object, optional): Additional key-value pairs for extra metadata.
     *   - `remarks` (string, optional): Additional remarks for the record.
     *
     * @return array The API response indicating whether the record was replaced and the details of the replaced record:
     *   - `replaced` (boolean): Whether the record was replaced successfully.
     *   - `record` (object): Details of the replaced record:
     *     - `type` (string): The record type.
     *     - `name` (string): The record name.
     *     - `ttl` (number): The TTL value (in milliseconds).
     *     - `rdatas` (array): List of rdata objects:
     *       - `value` (string): The record value.
     *       - `attributes` (object): Additional metadata related to the record.
     *     - `dnstableId` (number): The DNS table ID.
     *     - `remarks` (string): Additional remarks for the record.
     *
     * Example request:
     * ```php
     * $zone = 'captainmarvel3.com.mm';
     * $postField = [
     *     'name' => 'www',
     *     'type' => 'A',
     *     'ttl' => 3600,
     *     'rdatas' => [
     *         ['value' => '192.0.2.1']
     *     ],
     *     'remarks' => 'Updated record',
     * ];
     * $response = $webnicSDK->DNSZoneRecord->replaceZoneRecord($zone, $postField);
     * ```
     */
    public function replaceZoneSubscriptionRecord(string $zone, array $postField): array
    {
        return $this->sendRequest('POST', "/$zone/subscription/record/replace", [], $postField);
    }

    /**
     * Deletes all subscription records of a specific type and name within a domain zone.
     *
     * This endpoint allows you to delete all subscription records in a domain zone that match the provided type and name. If a `value` parameter is included, it will only delete records that match the specified value as well.
     *
     * @param string $zone The domain zone from which to delete subscription records (e.g., `example.com`).
     * @param array $params The parameters to filter the records to delete:
     *   - `type` (string, required): The DNS record type (e.g., `A`, `CNAME`, `TXT`).
     *   - `name` (string, required): The DNS record name (e.g., `www`).
     *   - `value` (string, optional): The DNS record value to filter specific records.
     *
     * @return array The API response containing details of the deleted records:
     *   - `record` (object): The deleted subscription record data object:
     *     - `type` (string): The record type (e.g., `A`, `CNAME`).
     *     - `name` (string): The record name.
     *     - `ttl` (number): The TTL value indicating how often the record will refresh (in milliseconds).
     *     - `rdatas` (object[]): A list of rdata objects:
     *       - `value` (string): The record value.
     *       - `attributes` (object): Additional key-value pairs for metadata.
     *     - `dnstableId` (number): The DNS table ID of the record.
     *     - `remarks` (string): Additional remarks for the record.
     *
     * Example request:
     * ```php
     * $zone = 'captainmarvel3.com.mm';
     * $params = [
     *     'type' => 'A',
     *     'name' => 'www',
     *     'value' => '192.0.2.1', // Optional
     * ];
     * $response = $webnicSDK->DNSZoneRecord->deleteZoneSubscriptionRecord($zone, $params);
     * ```
     */
    public function deleteZoneSubscriptionRecord(string $zone, array $params): array
    {
        return $this->sendRequest('DELETE', "/$zone/subscription/record", ['type' => $params['type'] ?? '', 'name' => $params['name'] ?? '', 'value' => $params['value'] ?? '']);
    }
    public function saveZoneRecord(string $zone, array $postField): array
    {
        return $this->sendRequest('POST', "/$zone/record", [], $postField);
    }
}
