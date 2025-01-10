<?php

namespace Webnic\WebnicSDK\Services\Domain;

use Webnic\WebnicSDK\Core\ApiConnector;


class DomainProduct extends ApiConnector
{



    public function __construct(string $clientId, string $clientSecret, array $config)
    {
        parent::__construct($clientId, $clientSecret, $config);

        $this->serviceUrl = '/domain' . $this->apiVersion;
    }

    /**
     * Perform a smart query to check domain availability and provide suggested TLDs.
     *
     * This function checks the availability of a domain name based on an appointed TLD,
     * provides suggestions for available TLDs, and checks WebNIC's top-selling TLD list.
     * The result is returned in 5 seconds.
     *
     * @param string $domainName The domain name to check availability for (e.g., "example.com").
     * @param string|null $sortBy A list of TLDs to prioritize in the search (optional).
     *
     * @return array The response data containing the availability status and suggested TLDs.
     *
     * Example request:
     * ```php
     * $domainName = 'example.com';
     * $response = $webnicSDK->domainProduct->getSmartQueryTLDs($domainName, 'com,my,net');
     * ```
     *
     */
    public function getSmartQueryTLDs(string $domainName, $sortBy = null): array
    {
        return $this->sendRequest('GET', '/smart-query', ['domainName' => $domainName, "sortBy" => $sortBy]);
    }

    /**
     * Get the list of domain extensions offered by WebNIC.
     *
     * This function retrieves all the domain extensions that WebNIC provides for domain registration.
     *
     * @return array The response data containing the list of available domain extensions.
     *
     * Example request:
     * ```php
     * $response = $webnicSDK->domainProduct->getDomainExtensions();
     * ```
     *
     */
    public function getDomainExtensions(): array
    {
        return $this->sendRequest('GET', '/exts');
    }

    /**
     * Get specific extensions' product pricing details.
     *
     * This function retrieves the pricing details for specific domain extensions (TLDs) including various transaction types such as registration, renewal, transfer, etc.
     *
     * @param array $postField The request body containing search filters and pagination details.
     * 
     * The $postField parameter is an associative array that contains the following keys:
     * 
     * 1. `filters` (array): This key holds an array of search conditions to filter the TLDs and transaction types. Each filter is an associative array with the following structure:
     *     - `field`: The field to filter by (e.g., `productKey` for TLDs, `transtype` for transaction types).
     *     - `value`: A string or array of values to filter the field by (e.g., `com,...` for TLDs, `register, renew` for transaction types).
     * 
     * @return array The response data containing pricing details for specific extensions.
     * 
     * Example of filters:
     * ```php
     * $filters = [
     *     [
     *         'field' => 'productKey',  // The TLDs to filter by
     *         'value' => 'com,...'      // Example TLDs to filter
     *     ],
     *     [
     *         'field' => 'transtype',   // The type of transaction to filter by (register, renew, etc.)
     *         'value' => 'register, renew' // Example transaction types
     *     ]
     * ];
     * ```
     * 
     * 2. `pagination` (array): This key holds pagination information for the response. It includes:
     *     - `page`: The page number to retrieve (e.g., `1` for the first page).
     *     - `pageSize`: The number of items per page (e.g., `10` for 10 items per page).
     * 
     * Example of pagination:
     * ```php
     * $pagination = [
     *     'page' => 1,       // The page number to retrieve
     *     'pageSize' => 10   // The number of items per page
     * ];
     * ```
     * 
     * The $postField array should be structured as follows:
     * ```php
     * $postField = [
     *     'filters' => $filters,     // The array of filters
     *     'pagination' => $pagination  // The pagination details
     * ];
     * ```
     * 
     * Example request:
     * ```php
     * $response = $webnicSDK->domainProduct->getExtensionsPrice($postField);
     * ```
     * 
     */
    public function getExtensionsPrice(array $postField): array
    {
        return $this->sendRequest('POST', '/exts/pricing', [], $postField);
    }

    /**
     * Get specific extensions' product promotional pricing details.
     *
     * This function retrieves the promotional pricing details for specific domain extensions (TLDs) during a promotional campaign.
     * The promotional pricing will be displayed in USD. Be sure to check the conditions and the effective period of the promo in GMT timezone.
     *
     * @param array $postField The request body containing search filters for transaction type and TLD.
     * 
     * The $postField parameter is an associative array that includes the following keys:
     * 
     * 1. `transtype` (string): This key filters the promotional pricing by the transaction type. Available options are:
     *     - `register`: For promotional pricing on domain registration.
     *     - `renewal`: For promotional pricing on domain renewal.
     *     - `transfer`: For promotional pricing on domain transfer.
     * 
     * 2. `ext` (string): This key filters the promotional pricing by a specific domain extension (TLD). You should specify the desired TLD to get the promo pricing for it.
     * 
     * @return array The response data containing promotional pricing details for specific extensions.
     * 
     * The response contains an array of promotional pricing details, including:
     * 
     * - `ext`: The TLD name (e.g., `.com`, `.org`).
     * - `term`: The promotional terms in years.
     * - `transType`: The transaction type for the promo (e.g., `register`, `renewal`, `transfer`).
     * - `oriPrice`: The original price of the domain.
     * - `promoPrice`: The promotional price of the domain.
     * - `savedPercent`: The percentage savings for the promotion.
     * - `savedAmount`: The total cost savings.
     * - `startDate`: The start date and time of the promotion in GMT.
     * - `endDate`: The end date and time of the promotion in GMT.
     * 
     * Example request:
     * ```php
     * $postField = [
     *     'transtype' => 'register',  // Example transaction type filter
     *     'ext' => 'com'             // Example TLD filter
     * ];
     * $response = $webnicSDK->domainProduct->getExtensionsPromoPricing($postField);
     * ```
     * 
     */
    public function getExtensionsPromoPricing(array $postField): array
    {
        return $this->sendRequest('POST', '/exts/pricing', [], $postField);
    }

    /**
     * Retrieves domain extension rules based on the specified rule type(RG),(TF),(GRACE)(DOC).
     *
     * This function fetches rules for a given domain extension (`ext`) and rule type (`ruleType`), 
     * such as registration, transfer, grace period, or document upload rules.
     *
     * @param string $ext        The domain extension (e.g., '.com', '.org').
     * @param string $ruleType   The type of rule to retrieve. Accepted values are:
     *                           - 'registration'  - Rules for domain registration.
     *                           - 'transfer'      - Rules for domain transfer.
     *                           - 'grace'         - Rules for grace period after domain expiration.
     *                           - 'DOCUPLOAD'     - Rules related to document uploads.
     * @param string $category   [Optional] Required for the 'DOCUPLOAD' rule type. Specifies the category 
     *                           of the document (e.g., 'ID', 'Address Proof'). Default is an empty string.
     * @param bool   $isProxy    [Optional] Indicates if the request is made via a proxy. Applicable 
     *                           only for the 'DOCUPLOAD' rule type. Default is `false`.
     *
     * @return array An associative array containing the rules for the specified domain extension
     *               and rule type.
     * 
     *
     * Example request:
     * ```php
     * // Fetch registration rules for '.com' domains.
     * $response =  $webnicSDK->domainProduct->getExtensionsRule('.com', 'registration');
     *
     * // Fetch document upload rules for '.org' domains with category and proxy.
     * $response =  $webnicSDK->domainProduct->getExtensionsRule('.org', 'DOCUPLOAD', 'ID', true);
     * ```
     */
    public function getExtensionsRule(string $ext, string $ruleType, string $category = "", bool $isProxy = false): array
    {
        $options = ['ext' => $ext, 'ruleType' => $ruleType];

        if ($ruleType === "DOCUPLOAD") {
            $options["category"] = $category;

            if ($isProxy) {
                $options["isProxy"] = $isProxy;
            }
        }

        return $this->sendRequest('GET', '/ext-rules', $options);
    }
}
