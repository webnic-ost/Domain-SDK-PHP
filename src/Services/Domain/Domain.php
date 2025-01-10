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
class Domain extends ApiConnector
{


    /**
     * Domain constructor.
     *
     * Initializes the Domain object with client credentials and token endpoint.
     *
     * @param string $clientId     The client ID for authentication.
     * @param string $clientSecret The client secret for authentication.
     * @param string $tokenEndpoint The endpoint to retrieve the access token.
     */
    public function __construct(string $clientId, string $clientSecret, array $config)
    {
        parent::__construct($clientId, $clientSecret, $config);

        // Initialize the serviceUrl based on apiVersion
        $this->serviceUrl =  '/domain' . $this->apiVersion;
    }

    /**
     * Checks the pattern of a domain name.
     *
     * This function allows you to verify if a domain name follows the specified pattern. It checks whether the domain 
     * name is in a valid format based on the allowed naming conventions.
     *
     * @param string $domainName The domain name to be checked for validity, e.g., `example.com`. (Required)
     *
     * @return array The API response indicating whether the domain name follows the specified pattern:
     *   - `valid` (boolean): Indicates whether the domain name follows the specified pattern.
     *
     * Example request:
     * ```php
     * $domainName = 'example.com';
     * $response = $webnicSDK->domain->checkDomainPattern($domainName);
     * ```
     */

    public function checkDomainPattern(string $domainName): array
    {
        return $this->sendRequest('GET', '/check-domain-pattern', ['domainName' => $domainName]);
    }

    /**
     * Queries a domain name to identify its availability.
     *
     * This function checks if a domain name is available for registration and retrieves additional
     * information about the domain, such as whether it is a premium domain or an online extension.
     *
     * @param string $domainName The domain name to be queried, e.g., `example.com`. (required)
     * @param string|null $rescode A reseller code to provide special pricing or details. (optional)
     *
     * @return array The API response with domain availability and pricing information:
     *   - `available` (boolean): Indicates whether the domain name is available for registration.
     *   - `premium` (boolean): Indicates if the domain name is a premium domain.
     *   - `online` (boolean): Indicates if the domain extension is an online extension.
     *   - `punyCodeDomainName` (string): The puny code version of the domain name.
     *   - `premiumInfo` (object): Contains premium domain information, such as:
     *     - `currency` (string): The pricing currency.
     *     - `registerPrice` (number): The registration price.
     *     - `renewPrice` (number): The renewal price.
     *     - `transferPrice` (number): The transfer price.
     *     - `restorePrice` (number): The restoration price.
     *
     * Example request:
     * ```php
     * $domainName = 'example.com';
     * $response = $webnicSDK->domain->queryDomain($domainName);
     * ```
     */

    public function queryDomain(string $domainName, ?string $rescode = null): array
    {
        return $this->sendRequest('GET', '/query', array_filter([
            'domainName' => $domainName,
            'rescode' => $rescode
        ]));
    }

    /**
     * Retrieves the total number of domains you own.
     *
     * This function allows you to get the count of domains associated with your account.
     *
     * @return array The API response containing the total number of domains:
     *   - `data` (number): The total number of domains you own.
     *
     * Example request:
     * ```php
     * $response = $webnicSDK->domain->countTotalDomains();
     * ```
     */

    public function countTotalDomains(): array
    {
        return $this->sendRequest('GET', '/count');
    }

    /**
     * Retrieves information about a domain.
     *
     * This function allows you to get detailed information about a specific domain, including its status, nameservers,
     * ownership, and expiration details.
     *
     * @param string $domainName The domain name to be queried, e.g., `example.com`. (required)
     *
     * @return array The API response containing the domain information:
     *   - `domainName` (string): The domain name.
     *   - `status` (string): The current status of the domain name.
     *   - `nameservers` (array): A list of domain nameservers.
     *   - `ownership` (string): The domain name ownership, e.g., WebNIC.
     *   - `lang` (string): The language of the domain.
     *   - `verified` (boolean): Indicates whether the domain name is verified.
     *   - `whoisPrivacy` (boolean): Indicates if Whois Privacy is enabled.
     *   - `proxy` (boolean): Indicates if a proxy service is subscribed for the domain.
     *   - `autoRenew` (boolean): Indicates if automatic renewal is enabled for the domain.
     *   - `dtcreate` (string): The date and time when the domain was created.
     *   - `dtmodify` (string): The date and time of the last modification to the domain.
     *   - `dtexpire` (string): The date and time when the domain is set to expire.
     *   - `contactId` (object): Contains contact IDs for different roles (registrant, admin, technical, and billing).
     *   - `userId` (string): The user ID (registrant) associated with the domain.
     *
     * Example request:
     * ```php
     * $domainName = 'example.com';
     * $response = $webnicSDK->domain->getDomainInfo($domainName);
     * ```
     */

    public function getDomainInfo(string $domainName): array
    {
        return $this->sendRequest('GET', '/info', ['domainName' => $domainName]);
    }

    /**
     * Displays domain name information using Universal WHOIS.
     *
     * This endpoint retrieves and displays the standard WHOIS result for a domain. The WHOIS information may vary
     * depending on the Registry and Registrar for the domain.
     *
     * @param string $domainName (required) The domain name to retrieve WHOIS information for, e.g., `example.com`.
     *
     * @return array The API response containing the Universal WHOIS information:
     *   - The result will include standard WHOIS details which may vary by registry and registrar.
     *
     * Example request:
     * ```php
     * $domainName = 'example.com';
     * $response = $webnicSDK->domain->getUniversalWhoisInfo($domainName);
     * ```
     */

    public function getUniversalWhoisInfo(string $domainName): array
    {
        return $this->sendRequest('GET', '/whois', ['domainName' => $domainName]);
    }

    /**
     * Updates the status of a domain.
     *
     * This function allows you to change the status of a domain. You can set the domain status to one of the allowed values:
     * `active`, `transfer_protected`, or `name_protected`. The status determines the actions allowed on the domain.
     *
     * @param string $domainName (required) The target domain name to update the status for, e.g., `example.com`.
     * @param string $status (required) The status to set for the domain. Allowed values: `"active"`, `"transfer_protected"`, `"name_protected"`.
     *
     * @return array The API response containing the updated status of the domain.
     *   - `domainName` (string): The domain name that was updated.
     *   - `status` (string): The new status of the domain.
     *
     * Example request:
     * ```php
     * $domainName = 'example.com';
     * $status = 'transfer_protected';
     * $response = $webnicSDK->domain->updateDomainStatus($domainName, $status);
     * ```
     */

    public function updateDomainStatus(string $domainName, string $status): array
    {
        return $this->sendRequest('PUT', '/status', [
            'domainName' => $domainName,
            'status' => $status
        ]);
    }

    /**
     * Retrieves WebNIC's basic default nameservers.
     *
     * This endpoint retrieves a list of WebNIC's default nameservers that can be used for domain names.
     *
     * @return array The API response containing the list of WebNIC default nameservers:
     *   - `data` (array): An array of strings containing the list of WebNIC's basic default nameservers.
     *
     * Example request:
     * ```php
     * $response = $webnicSDK->domain->getWebnicDefaultNameservers();
     * ```
     */

    public function getWebnicDefaultNameservers(): array
    {
        return $this->sendRequest('GET', '/dns/default');
    }

    /**
     * Modifies the nameservers of a domain.
     *
     * This function allows you to update the nameservers for a specific domain. You must provide an array of nameservers
     * to set for the domain.
     *
     * @param string $domainName (required) The domain name to be modified, e.g., `example.com`.
     * @param array $nameservers (required) An array containing the new nameservers to be set for the domain. 
     *
     * @return array The API response indicating the result of the nameserver modification.
     *   - `data` (array): An array containing the updated nameservers.
     *
     * Example request:
     * ```php
     * $domainName = 'example.com';
     * $nameservers = [
     *     'ns1.1webnic.com',
     *     'ns2.1webnic.com', 
     * ];
     * $response = $webnicSDK->domain->updateDomainNameserver($domainName, $nameservers);
     * ```
     */
    public function updateDomainNameserver(string $domainName, array $nameservers): array
    {
        // Prepare the request body
        $body = ['nameservers' => $nameservers];

        return $this->sendRequest('PUT', '/dns', ['domainName' => $domainName], $body);
    }

    /**
     * Toggles auto-renewal for a list of domains.
     *
     * This function allows you to enable or disable the auto-renewal setting for one or more domains. You can specify
     * a list of domains or apply the change to all domains associated with your account.
     *
     * @param array $postField An array containing the parameters for auto-renewal:
     *   - `autoRenew` (bool): Whether to enable (`true`) or disable (`false`) auto-renewal.
     *   - `target` (string, optional): Specifies the target, e.g., `"all"` to apply to all domains.
     *   - `domainList` (array of strings, optional): An array of domain names to update for auto-renewal.
     *
     * @return array The API response containing the result of the auto-renewal toggle:
     *   - `data` (array): An array indicating the updated auto-renewal status for the specified domains.
     *
     * Example request:
     * ```php
     * $postField = [
     *     'autoRenew' => true,
     *     'domainList' => ['testing1.net', 'testing2.id', 'testing3.info'],
     * ];
     * $response = $webnicSDK->domain->toggleAutoRenew($postField);
     * ```
     */
    public function toggleAutoRenew(array $postField): array
    {
        return $this->sendRequest('PUT', '/auto-renew/toggle', [], $postField);
    }

    /**
     * Activates or deactivates WHOIS privacy for a domain.
     *
     * This function allows you to enable or disable WHOIS privacy for a specific domain. You can specify whether to activate
     * or deactivate WHOIS privacy by setting the `active` field to `true` or `false`.
     * 
     * @param array $postField The request body data, including:
     *   - `active` (boolean, required): Whether WHOIS privacy should be activated or deactivated.
     *   - `domainName` (string, required): The domain name to update for WHOIS privacy.
     *
     * @return array The API response indicating the result of the WHOIS privacy toggle.
     *   - `data` (array): An array with information about the domain and its WHOIS privacy status.
     *
     * Example request:
     * ```php
     * $postField = [
     *     'active' => true,
     *     'domainName' => 'example.com'
     * ];
     * $response = $webnicSDK->domain->toggleWhoisPrivacy($postField);
     * ```
     */

    public function toggleWhoisPrivacy(array $postField): array
    {
        return $this->sendRequest('PUT', '/whois-privacy/toggle', [], $postField);
    }

    /**
     * Toggles proxy subscription for a domain.
     *
     * This function allows you to subscribe or unsubscribe from the proxy service for a specific domain. 
     * You can specify whether to subscribe or unsubscribe by setting the `action` field to `"subscribe"` or `"unsubscribe"`.
     * 
     * @param array $postField The request body data, including:
     *   - `action` (string, required): The action to perform: `"subscribe"` or `"unsubscribe"`.
     *   - `domainName` (string, required): The domain name to update for proxy subscription.
     *
     * @return array The API response indicating the result of the proxy subscription toggle.
     *   - `data` (array): An array with information about the domain and its proxy status.
     *
     * Example request:
     * ```php
     * $postField = [
     *     'action' => 'unsubscribe',
     *     'domainName' => 'zhuantestingeppmy.my'
     * ];
     * $response = $webnicSDK->domain->toggleProxySubscription($postField);
     * ```
     */

    public function toggleProxySubscription(array $postField): array
    {
        return $this->sendRequest('PUT', '/proxy', [], $postField);
    }

    /**
     * Registers a new domain name.
     *
     * This function allows you to register a new domain. You must provide the domain name, the term of registration in years,
     * a list of nameservers, and the contact IDs for the registrant, administrator, technical, and billing contacts.
     * Optional parameters like domain type, language, and auto-renewal settings can also be specified.
     *
     * @param array $postField The request body data, including:
     *   - `domainName` (string, required): The domain name to be registered, e.g., `example.com`.
     *   - `domainType` (string, optional): Specifies the type of domain to be registered. Default is `"standard"`.
     *     Allowed values: `"standard"`, `"premium"`, `"rereg"`.
     *   - `term` (number, required): The length of the term in years for the domain, e.g., `1`.
     *   - `lang` (string, optional): The language of the domain registration. Default is `"eng"`.
     *   - `nameservers` (array of strings, required): A list of domain nameservers, e.g., `["ns1.web.cc", "ns2.web.cc"]`.
     *   - `registrantContactId` (string, required): The registrant contact ID.
     *   - `administratorContactId` (string, required): The administrator contact ID.
     *   - `technicalContactId` (string, required): The technical contact ID.
     *   - `billingContactId` (string, required): The billing contact ID.
     *   - `registrantUserId` (string, required): The registrant user ID.
     *   - `addons` (object, optional): Domain addons, including:
     *     - `proxy` (boolean, optional): Specifies whether the domain is subscribed to proxy. Default is `false`.
     *     - `autoRenew` (boolean, optional): Specifies whether the domain is set to auto-renew. Default is `false`.
     *
     * @return array The API response containing the result of the domain registration:
     *   - `pendingOrder` (boolean): Indicates whether the domain registration is pending.
     *   - `dtexpire` (string): The expiration date of the registered domain.
     *
     * Example request:
     * ```php
     * $postField = [
     *     'domainName' => 'example.com',
     *     'term' => 1,
     *     'nameservers' => ['ns1.web.cc', 'ns2.web.cc'],
     *     'registrantContactId' => 'WNC970359T',
     *     'administratorContactId' => 'WNC970359T',
     *     'technicalContactId' => 'WNC970359T',
     *     'billingContactId' => 'WNC970359T',
     *     'registrantUserId' => 'REG100170'
     * ];
     * $response = $webnicSDK->domain->registerDomain($postField);
     * ```
     */

    public function registerDomain(array $postField): array
    {
        return $this->sendRequest('POST', '/register', [], $postField);
    }

    /**
     * Renews a domain.
     *
     * This function allows you to renew a domain. Upon a successful domain renewal, the system will lock renewal of the domain
     * name for the same day to prevent duplicate renewals.
     *
     * @param array $renewData The request body data, including:
     *   - `domainName` (string, required): The domain name to be renewed, e.g., `example.com`.
     *   - `domainType` (string, optional): Specifies the type of domain name to be renewed. Default value: `"standard"`. 
     *     Allowed values: `"standard"`, `"premium"`, `"rereg"`.
     *   - `term` (number, required): The length of the renewal term in years.
     *
     * @return array The API response containing the details of the renewed domain:
     *   - `pendingOrder` (boolean): Indicates whether the renewal is a pending order.
     *   - `dtexpire` (string): The expiry date of the renewed domain.
     *
     * Example request:
     * ```php
     * $renewData = [
     *     'domainName' => 'example.com',
     *     'domainType' => 'standard',
     *     'term' => 1,
     * ];
     * $response = $webnicSDK->domain->renewDomain($renewData);
     * ```
     */

    public function renewDomain(array $renewData): array
    {
        return $this->sendRequest('POST', '/renew', [], $renewData);
    }

    /**
     * Restores a domain.
     *
     * This function allows you to restore a domain that has expired or been deleted. You must provide the domain name
     * and confirm the restoration agreement policy in the request body.
     *
     * @param array $restoreData The request body data, including:
     *   - `domainName` (string, required): The domain name to be restored, e.g., `example.com`.
     *   - `agreeRestorePolicy` (boolean, required): A flag to indicate agreement with the restore policy. Allowed value: `"true"`.
     *
     * @return array The response indicating the result of the restoration operation.
     *
     * Example request:
     * ```php
     * $restoreData = [
     *     'domainName' => 'example.com',
     *     'agreeRestorePolicy' => true,
     * ];
     * $response = $webnicSDK->domain->restoreDomain($restoreData);
     * ```
     */
    public function restoreDomain(array $restoreData): array
    {
        $body = $restoreData;

        return $this->sendRequest('POST', '/restore', [], $body);
    }

    /**
     * Deletes a domain name.
     *
     * This function allows you to delete a domain. You must provide the domain name to be deleted. The response includes information 
     * about the operation, including whether the deletion is pending, if the domain is eligible for a refund, and if the operation 
     * is conducted online.
     *
     * @param string $domainName (required) The domain name to be deleted, e.g., `example.com`.
     *
     * @return array The API response containing the result of the domain deletion:
     *   - `pendingOrder` (boolean): Indicates whether the domain deletion is pending.
     *   - `domainName` (string): The domain name being deleted.
     *   - `online` (boolean): Indicates if the operation is conducted online.
     *   - `refund` (boolean): Indicates whether the domain is eligible for a refund, only present if `online` is true.
     *
     * Example request:
     * ```php
     * $domainName = 'example.com';
     * $response = $webnicSDK->domain->deleteDomain($domainName);
     * ```
     */

    public function deleteDomain(string $domainName): array
    {
        return $this->sendRequest('DELETE', '/delete', ['domainName' => $domainName]);
    }

    /**
     * Resends the domain verification email.
     *
     * This function allows you to resend the domain verification email to the registrant. You must provide the domain name for 
     * which the verification email needs to be sent again. The response will include the recipient's email address.
     *
     * @param string $domainName (required) The domain name for which the verification email is being resent, e.g., `example.com`.
     *
     * @return array The API response containing:
     *   - `recipient` (string): The email address of the recipient.
     *
     * Example request:
     * ```php
     * $domainName = 'example.com';
     * $response = $webnicSDK->domain->resendDomainVerificationEmail($domainName);
     * ```
     */

    public function resendDomainVerificationEmail(string $domainName): array
    {
        return $this->sendRequest('POST', '/resend-verification-email', ['domainName' => $domainName]);
    }

    /**
     * Downloads the domain certificate.
     *
     * This function allows you to download the certificate for a domain. You must provide the domain name for which the certificate
     * is being downloaded. You can also specify the language of the certificate, with the default being English (eng).
     *
     * Available Certificate Language Options:
     * - English (eng) [default]
     * - Simplified Chinese (chi)
     * - Traditional Chinese (zho)
     * - Indonesian (ind)
     * - Thai (tha)
     * - Vietnamese (vie)
     *
     * @param string $domainName (required) The domain name for which the certificate is being downloaded, e.g., `example.com`.
     * @param string|null $lang (optional) The language for the downloaded certificate. Default value: `eng`. Allowed values: `"eng"`, `"chi"`, `"zho"`, `"ind"`, `"tha"`, `"vie"`.
     *
     * @return array The API response containing the result of the certificate download.
     *
     * Example request:
     * ```php
     * $domainName = 'example.com';
     * $lang = 'chi';
     * $response = $webnicSDK->domain->downloadCertificate($domainName, $lang);
     * ```
     */


    public function downloadCertificate(string $domainName, ?string $lang = 'eng'): array
    {
        return $this->sendRequest('GET', '/download/certificate', array_filter([
            'domainName' => $domainName,
            'lang' => $lang
        ]));
    }


    /**
     * Resets the domain's authorization information without informing the registrant.
     *
     * This function allows you to reset the authorization information for a domain without notifying the registrant. You must provide
     * the domain name whose authorization information needs to be reset.
     *
     * @param string $domainName (required) The domain name for which the authorization information is being reset, e.g., `example.com`.
     *
     * @return array The API response indicating the result of the authorization information reset.
     *
     * Example request:
     * ```php
     * $domainName = 'example.com';
     * $response = $webnicSDK->domain->resetAuthorizationInformation($domainName);
     * ```
     */

    public function resetAuthorizationInformation(string $domainName): array
    {
        return $this->sendRequest('POST', '/auth-info/reset', ['domainName' => $domainName]);
    }

    /**
     * Sends the authorization information to the registrant's email.
     *
     * This function allows you to send the authorization information to the registrant's email for a specific domain. You must provide 
     * the domain name for which the authorization information will be sent.
     *
     * @param string $domainName (required) The domain name for which the authorization information will be sent, e.g., `example.com`.
     *
     * @return array The API response indicating the result of sending the authorization information.
     *
     * Example request:
     * ```php
     * $domainName = 'example.com';
     * $response = $webnicSDK->domain->sendAuthorizationInformation($domainName);
     * ```
     */

    public function sendAuthorizationInformation(string $domainName): array
    {
        return $this->sendRequest('POST', '/auth-info/send', ['domainName' => $domainName]);
    }

    /**
     * Uploads a verification document for a pending order.
     *
     * This function allows you to upload a verification document for a pending order. You must provide the order ID and the document 
     * details, including the type of document and the file path for the document to be uploaded.
     *
     * @param int $id (required) The ID of the pending order to upload the verification document for, e.g., `72`.
     * @param array $document (required) The document details, including:
     *   - `type` (string, required): The type of the document to be uploaded, e.g., `business_registration`.
     *   - `filePath` (string, required): The file path of the document to be uploaded.
     *
     * @return array The API response containing:
     *   - `data` (string): The URL path of the uploaded verification document.
     *
     * Example request:
     * ```php
     * $id = 72;
     * $document = [
     *     'type' => 'business_registration',
     *     'filePath' => '/path/to/document.pdf'
     * ];
     * $response = $webnicSDK->domain->uploadVerificationDocument($id, $document);
     * ```
     */

    public function uploadVerificationDocument(int $id, array $document): array
    {
        // Prepare the form data
        $formData = [
            'type' => $document['type'], // Document type
            'file' => fopen($document['filePath'], 'r'), // Open the file for reading
        ];

        // Build the URL with the provided ID
        $url = $this->buildUrl("/upload-document/$id");

        return $this->request('POST', $url, [
            'multipart' => $formData
        ]);
    }

    /**
     * Downloads a verification document for a pending order.
     *
     * This function allows you to download a verification document for a pending order. You must provide the order ID and the URL 
     * of the document to be downloaded.
     *
     * @param int $id (required) The ID of the pending order for which the verification document is being downloaded, e.g., `72`.
     * @param string $documentUrl (required) The URL path of the document to be downloaded, e.g., `https://xxxxxx.pdf`.
     *
     * @return array The API response containing the downloaded document.
     *
     * Example request:
     * ```php
     * $id = 72;
     * $documentUrl = 'https://xxxxxx.pdf';
     * $response = $webnicSDK->domain->downloadVerificationDocument($id, $documentUrl);
     * ```
     */

    public function downloadVerificationDocument(int $id, string $documentUrl): array
    {
        // Build the URL with the provided ID and document URL
        $url = $this->buildUrl("/download-document/$id") . '?url=' . urlencode($documentUrl);

        // Make the GET request
        return $this->request('GET', $url);
    }

    /**
     * Retrieves domain statistics based on the specified type.
     *
     * This function allows you to fetch various domain statistics. You can query the total number of domains or specific categories
     * such as active, expired, or transfer protected domains. Provide the desired statistics type as a query parameter.
     *
     * @param string $type (optional) The type of domain statistics to retrieve. Allowed values:
     *   - `"total"`: Total number of domains.
     *   - `"totalActive"`: Total number of active domains.
     *   - `"totalNewTransferIn"`: Total number of domains in new transfer-in status.
     *   - `"totalExpired"`: Total number of expired domains.
     *   - `"totalExpiring"`: Total number of domains in expiring status.
     *   - `"totalDeleted"`: Total number of deleted domains.
     *   - `"totalTransferProtected"`: Total number of transfer-protected domains.
     *   - `"totalNameProtected"`: Total number of name-protected domains.
     *   - `"totalRedemptionGrace"`: Total number of domains in redemption grace status.
     *   - `"totalPendingVerify"`: Total number of domains pending verification.
     *   - `"totalPending"`: Total number of domains pending registration.
     *
     * @return array The API response containing the domain statistics:
     *   - `total` (number): Total number of domains.
     *   - `totalActive` (number): Total number of active domains.
     *   - `totalNewTransferIn` (number): Total number of domains in new transfer-in status.
     *   - `totalExpired` (number): Total number of expired domains.
     *   - `totalExpiring` (number): Total number of expiring domains.
     *   - `totalDeleted` (number): Total number of deleted domains.
     *   - `totalTransferProtected` (number): Total number of domains in transfer-protected status.
     *   - `totalNameProtected` (number): Total number of domains in name-protected status.
     *   - `totalRedemptionGrace` (number): Total number of domains in redemption grace status.
     *   - `totalPendingVerify` (number): Total number of domains pending verification.
     *   - `totalPending` (number): Total number of domains pending registration.
     *
     * Example request:
     * ```php
     * $type = 'totalActive';
     * $response = $webnicSDK->domain->getDomainStatistics($type);
     * ```
     */


    public function getDomainStatistics(string $type): array
    {
        return $this->sendRequest('GET', '/statistics', ['type' => $type]);
    }

    /**
     * Retrieves a list of available top-level domain names (TLDs) based on the given domain name.
     *
     * This function allows you to query available top-level domains (TLDs) for a given domain name. It returns a list of available 
     * TLDs, along with their respective prices. Use this method to check availability for various TLDs that can be registered.
     *
     * @param string $domainName (required) The base domain name for which the top-level domains are being queried, e.g., `example.com`.
     *
     * @return array The API response containing the list of available top-level domains:
     *   - `price` (number): The price for registering the domain with the top-level domain.
     *   - `domainName` (string): The available top-level domain name.
     *
     * Example request:
     * ```php
     * $domainName = 'testing.com';
     * $response = $webnicSDK->domain->getTopDomainAvailableList($domainName);
     * ```
     */



    public function getTopDomainAvailableList(string $domainName): array
    {
        return $this->sendRequest('GET', '/top-domain-available-list', ['domainName' => $domainName]);
    }
}
