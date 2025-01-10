<?php

namespace Webnic\WebnicSDK\Services\Domain;

use Webnic\WebnicSDK\Core\ApiConnector;

class DomainTransfer extends ApiConnector
{

    public function __construct(string $clientId, string $clientSecret, array $config)
    {
        parent::__construct($clientId, $clientSecret, $config);

        // Initialize the serviceUrl based on apiVersion
        $this->serviceUrl = '/domain' . $this->apiVersion;
    }

    /**
     * Query the transfer type for a specific domain name.
     *
     * This function helps determine the transfer type to use when transferring a domain name. 
     * It provides hints on whether the domain should be transferred from another registrar or if it's an ownership transfer between WebNIC resellers.
     *
     * @param string $domainName The domain name to query for transfer type (e.g., "example.com").
     *
     * @return array The response data indicating the transfer type for the domain.
     *
     * The response contains:
     * 
     * - `transferType`: A string indicating the type of transfer:
     *     - `registrar_transfer`: Transfer-in from another registrar.
     *     - `reseller_transfer`: Ownership transfer among WebNIC resellers.
     * 
     * Example request:
     * ```php
     * $domainName = 'example.com';
     * $response = $webnicSDK->domainTransfer->queryTransferType($domainName);
     * ```
     */

    public function queryTransferType(string $domainName): array
    {
        return $this->sendRequest('GET', '/query-transfer-type', ['domainName' => $domainName]);
    }

    /**
     * Submit registrar transfer in for a domain.
     *
     * This function submits a registrar transfer request for a specific domain, including details such as the domain name, authorization information, and relevant contact information.
     *
     * @param array $postField The request body containing the domain name, authorization info, contact IDs, nameservers, and other related information.
     * 
     * Detailed Field Descriptions:
     * 
     * - 'domainName' (string): The domain name to be transferred, e.g., 'auth20190311-001.com'.
     * - 'domainType' (string, optional): The type of domain name to be transferred. Default is "standard". Allowed values: "standard", "premium", "rereg".
     * - 'authInfo' (string): Authorization information for domain transfer (required).
     * - 'registrantUserId' (number): The user ID of the registrant (required).
     * - 'registrantContactId' (string): The contact ID of the registrant (required).
     * - 'administratorContactId' (string): The contact ID of the administrator (required).
     * - 'technicalContactId' (string): The contact ID of the technical contact (required).
     * - 'billingContactId' (string): The contact ID of the billing contact (required).
     * - 'nameservers' (array): A list of domain nameservers (required). Example: ["ns1.web.cc", "ns2.web.cc"].
     * - 'subscribeProxy' (boolean): Whether the domain is subscribed to proxy service (required).
     * - 'bundledDomain' (string, optional): The bundled domain if applicable.
     * 
     * @return array The response data from the transfer-in request, which includes the transfer status and reference ID.
     *
     * Example request:
     * ```php
     * $postField = [
     *     'domainName' => 'auth20190311-001.com', // The domain name to be transferred
     *     'authInfo' => 'r$k6a#d-',              // Authorization code for transfer
     *     'registrantUserId' => 856224,          // The registrant user ID
     *     'registrantContactId' => 'WN964984T',  // Registrant contact ID
     *     'administratorContactId' => 'WN964984T', // Administrator contact ID
     *     'technicalContactId' => 'WN964984T',   // Technical contact ID
     *     'billingContactId' => 'WN964984T',     // Billing contact ID
     *     'nameservers' => ['ns1.web.cc', 'ns2.web.cc'], // List of nameservers
     *     'subscribeProxy' => false,             // Proxy service subscription status
     *     'registrantUsername' => 'contest02'    // Registrant username
     * ];
     * $response = $webnicSDK->domainTransfer->submitRegistrarTransferIn($postField);
     * ```
     */
    public function submitRegistrarTransferIn(array $postField): array
    {
        return $this->sendRequest('POST', '/transfer-in', [], $postField);
    }


    /**
     * Get domain transfer in status by domain name.
     *
     * This function retrieves the status of a registrar transfer-in request for a specific domain by its domain name.
     *
     * @param string $domainName The domain name for which the transfer status is being queried (e.g., "example.com").
     * 
     * @return array The response data containing transfer status details for the requested domain.
     * 
     * Detailed Field Descriptions (Response Data):
     * 
     * - 'id' (number): A reference ID for the registrar transfer-in status request. This ID can be used to track the transfer status.
     * - 'domain' (string): The domain name for which the transfer request status is being queried.
     * - 'ext' (string): The top-level domain (TLD) extension of the domain, such as "com", "net", "org", etc.
     * - 'status' (string): The current status of the transfer request. Possible values include:
     *   - "pending": The transfer is still in progress.
     *   - "approve": The transfer has been approved.
     *   - "reject": The transfer has been rejected.
     *   - "cancel": The transfer request has been canceled.
     *   - "insert_fail": The transfer failed during the insertion process.
     *   - "complete": The transfer is complete.
     * - 'remark' (string): Additional remarks or comments provided regarding the transfer request status.
     * - 'dtcreate' (string): The date and time when the transfer request was created in a timestamp format (e.g., "2025-01-10T10:00:00Z").
     * - 'pendingOrder' (boolean): Indicates whether the transfer request is still pending (true) or if it has been processed (false).
     *
     * Example request:
     * ```php
     * $domainName = 'example.com';
     * $response = $webnicSDK->domainTransfer->getRegistrarTransferInStatusByDomain($domainName);
     * ```
     */
    public function getRegistrarTransferInStatusByDomain(string $domainName): array
    {
        return $this->sendRequest('GET', '/transfer-in/status', ['domainName' => $domainName]);
    }
    /**
     * Get domain transfer in status by transfer request ID.
     *
     * This function retrieves the status of a registrar transfer-in request using the specific transfer request ID.
     *
     * @param string $id The reference ID for the registrar transfer-in request (e.g., "123456").
     * 
     * @return array The response data containing transfer status details for the requested transfer ID.
     * 
     * Detailed Field Descriptions (Response Data):
     * 
     * - 'id' (number): A reference ID for the registrar transfer-in status request. This ID can be used to track the transfer status.
     * - 'domain' (string): The domain name for which the transfer request status is being queried.
     * - 'ext' (string): The top-level domain (TLD) extension of the domain, such as "com", "net", "org", etc.
     * - 'status' (string): The current status of the transfer request. Possible values include:
     *   - "pending": The transfer is still in progress.
     *   - "approve": The transfer has been approved.
     *   - "reject": The transfer has been rejected.
     *   - "cancel": The transfer request has been canceled.
     *   - "insert_fail": The transfer failed during the insertion process.
     *   - "complete": The transfer is complete.
     * - 'remark' (string): Additional remarks or comments provided regarding the transfer request status.
     * - 'dtcreate' (string): The date and time when the transfer request was created in a timestamp format (e.g., "2025-01-10T10:00:00Z").
     * - 'pendingOrder' (boolean): Indicates whether the transfer request is still pending (true) or if it has been processed (false).
     *
     * Example request:
     * ```php
     * $id = '123456';
     * $response = $webnicSDK->domainTransfer->getRegistrarTransferInStatusById($id);
     * ```
     */

    public function getRegistrarTransferInStatusById(string $id): array
    {
        return $this->sendRequest('GET', "/transfer-in/status/$id");
    }

    /**
     * Get registrar transfer away status by domain name.
     *
     * This function retrieves the status of a transfer-away request for a specific domain name.
     *
     * @param string $domainName The domain name for which the transfer-away status is being queried (e.g., "example.com").
     * 
     * @return array The response data containing transfer-away status details for the specified domain name.
     * 
     * Detailed Field Descriptions (Response Data):
     * 
     * - 'id' (number): A reference ID for the registrar transfer-away request. This ID can be used to track the transfer status.
     * - 'uuid' (string): The unique identifier (UUID) for the transfer-away request.
     * - 'domain' (string): The domain name for which the transfer-away request status is being queried.
     * - 'ext' (string): The top-level domain (TLD) extension of the domain, such as "com", "net", "org", etc.
     * - 'status' (string): The current status of the transfer-away request. Possible values include:
     *   - "pending": The transfer is still in progress.
     *   - "approve": The transfer has been approved.
     *   - "reject": The transfer has been rejected.
     *   - "cancel": The transfer request has been canceled.
     *   - "complete": The transfer is complete.
     * - 'remark' (string): Additional remarks or comments provided regarding the transfer-away request status.
     * - 'dtcreate' (string): The date and time when the transfer-away request was created in a timestamp format (e.g., "2025-01-10T10:00:00Z").
     * - 'registrantApproved' (boolean): Indicates whether the registrant has approved the transfer request (true if approved, false otherwise).
     * - 'resellerApproved' (boolean): Indicates whether the reseller has approved the transfer request (true if approved, false otherwise).
     *
     * Example request:
     * ```php
     * $domainName = 'example.com';
     * $response = $webnicSDK->domainTransfer->getRegistrarTransferAwayStatusByDomain($domainName);
     * ```
     */

    public function getRegistrarTransferAwayStatusByDomain(string $domainName): array
    {
        return $this->sendRequest('GET', '/transfer-away/status', ['domainName' => $domainName]);
    }

    /**
     * Get registrar transfer away status by ID.
     *
     * This function retrieves the status of a transfer-away request for a specific domain transfer ID.
     *
     * @param string $id The unique identifier for the registrar transfer-away request (e.g., "123456").
     * 
     * @return array The response data containing transfer-away status details for the specified transfer ID.
     * 
     * Detailed Field Descriptions (Response Data):
     * 
     * - 'id' (number): A reference ID for the registrar transfer-away request. This ID uniquely identifies the transfer request.
     * - 'uuid' (string): The unique identifier (UUID) for the transfer-away request.
     * - 'domain' (string): The domain name associated with the transfer-away request.
     * - 'ext' (string): The top-level domain (TLD) extension of the domain, such as "com", "net", "org", etc.
     * - 'status' (string): The current status of the transfer-away request. Possible values include:
     *   - "pending": The transfer is still in progress.
     *   - "approve": The transfer has been approved.
     *   - "reject": The transfer has been rejected.
     *   - "cancel": The transfer request has been canceled.
     *   - "complete": The transfer is complete.
     * - 'remark' (string): Additional remarks or comments regarding the transfer-away request status.
     * - 'dtcreate' (string): The date and time when the transfer-away request was created, formatted as a timestamp (e.g., "2025-01-10T10:00:00Z").
     * - 'registrantApproved' (boolean): Indicates whether the registrant has approved the transfer request (`true` if approved, `false` otherwise).
     * - 'resellerApproved' (boolean): Indicates whether the reseller has approved the transfer request (`true` if approved, `false` otherwise).
     *
     * Example request:
     * ```php
     * $id = '123456';
     * $response = $webnicSDK->domainTransfer->getRegistrarTransferAwayStatusById($id);
     * ```
     */

    public function getRegistrarTransferAwayStatusById(string $id): array
    {
        return $this->sendRequest('GET', "/transfer-away/status/$id");
    }

    /**
     * Update registrar transfer away status.
     *
     * This function updates the status of a registrar transfer-away request by specifying approval details.
     *
     * @param string $id The unique identifier for the registrar transfer-away request (e.g., "123456").
     * @param array $postField An array containing the parameters for updating the transfer-away status:
     *   - `approveType` (string): (Required) Specifies the type of approval.
     *     Allowed values:
     *       - `"registrant"`: Approval from the registrant.
     *       - `"partner"`: Approval from the reseller or partner.
     *   - `approve` (bool): (Required) Indicates whether the transfer request is approved (`true`) or not (`false`).
     *   - `approveRemarks` (string, optional): Additional remarks or comments related to the approval decision.
     *
     * @return array The API response containing the updated transfer-away status:
     *   - `status` (string): Status of the transfer request.
     *   - `remark` (string): Any additional remarks or comments.
     *   - `registrantApproved` (bool): Indicates if the registrant has approved the transfer.
     *   - `resellerApproved` (bool): Indicates if the reseller has approved the transfer.
     *
     * Example request:
     * ```php
     * $id = '123456';
     * $postField = [
     *     'approveType' => 'registrant',
     *     'approve' => true,
     *     'approveRemarks' => 'Approval granted by registrant.',
     * ];
     * $response = $webnicSDK->domainTransfer->updateRegistrarTransferAwayStatus($id, $postField);
     * ```
     */

    public function updateRegistrarTransferAwayStatus(string $id, array $postField): array
    {
        return $this->sendRequest('PUT', "/transfer-away/status/$id", [], $postField);
    }

    /**
     * Submit reseller transfer.
     *
     * This function submits a request to transfer a domain to a new reseller.
     *
     * @param array $postField An array containing the details for the reseller transfer:
     *   - `domainName` (string): (Required) The domain name to be transferred.
     *   - `domainType` (string, optional): Specifies the type of domain name being transferred.  
     *     Default value: `"standard"`  
     *     Allowed values:
     *       - `"standard"`: Standard domain.
     *       - `"premium"`: Premium domain.
     *       - `"rereg"`: Re-registration domain.
     *   - `registrantUserId` (int): (Required) The user ID of the registrant initiating the transfer.
     *
     * @return array The API response containing the details of the submitted reseller transfer request:
     *   - `id` (int): Reference ID for the reseller transfer status.
     *   - `domain` (string): The domain name involved in the transfer.
     *   - `ext` (string): The domain extension.
     *   - `status` (string): Status of the transfer request.  
     *     Possible values: `"pending"`, `"approved"`, `"rejected"`, `"cancelled"`, `"failed"`.
     *   - `remark` (string): Additional remarks or comments about the transfer request.
     *   - `gainingReseller` (bool): Indicates whether the gaining reseller is involved in the transfer.
     *   - `losingReseller` (bool): Indicates whether the losing reseller is involved in the transfer.
     *
     * Example request:
     * ```php
     * $postField = [
     *     'domainName' => 'example.com',
     *     'domainType' => 'standard',
     *     'registrantUserId' => 123456,
     * ];
     * $response = $webnicSDK->domainTransfer->submitResellerTransfer($postField);
     * ```
     */

    public function submitResellerTransfer(array $postField): array
    {
        return $this->sendRequest('POST', '/reseller-transfer', [], $postField);
    }

    /**
     * Get reseller transfer status by domain name.
     *
     * This function retrieves the status of a reseller transfer request for a specific domain name.
     *
     * @param string $domainName The domain name to retrieve the reseller transfer status for.
     *
     * @return array The API response containing the details of the reseller transfer status:
     *   - `id` (int): Reference ID for the reseller transfer status.
     *   - `domain` (string): The domain name involved in the transfer.
     *   - `ext` (string): The domain extension.
     *   - `status` (string): Status of the transfer request.  
     *     Possible values: `"pending"`, `"approved"`, `"rejected"`, `"cancelled"`, `"failed"`.
     *   - `remark` (string): Additional remarks or comments about the transfer request.
     *   - `gainingReseller` (bool): Indicates whether the gaining reseller is involved in the transfer.
     *   - `losingReseller` (bool): Indicates whether the losing reseller is involved in the transfer.
     *   - `dtcreate` (string): Date and time when the transfer request was created.
     *
     * Example request:
     * ```php
     * $domainName = 'example.com';
     * $response = $webnicSDK->domainTransfer->getResellerTransferStatusByDomain($domainName);
     * ```
     */

    public function getResellerTransferStatusByDomain(string $domainName): array
    {
        return $this->sendRequest('GET', "/reseller-transfer/status", ["domainName" => $domainName]);
    }

    /**
     * Get reseller transfer status by ID.
     *
     * This function retrieves the status of a reseller transfer request using a specific transfer ID.
     *
     * @param string $id The transfer ID to retrieve the reseller transfer status for.
     *
     * @return array The API response containing the details of the reseller transfer status:
     *   - `id` (int): Reference ID for the reseller transfer status.
     *   - `domain` (string): The domain name involved in the transfer.
     *   - `ext` (string): The domain extension.
     *   - `status` (string): Status of the transfer request.  
     *     Possible values: `"pending"`, `"approved"`, `"rejected"`, `"cancelled"`, `"failed"`.
     *   - `remark` (string): Additional remarks or comments about the transfer request.
     *   - `gainingReseller` (bool): Indicates whether the gaining reseller is involved in the transfer.
     *   - `losingReseller` (bool): Indicates whether the losing reseller is involved in the transfer.
     *   - `dtcreate` (string): Date and time when the transfer request was created.
     *
     * Example request:
     * ```php
     * $id = '123456';
     * $response = $webnicSDK->domainTransfer->getResellerTransferStatusById($id);
     * ```
     */

    public function getResellerTransferStatusById(string $id): array
    {
        return $this->sendRequest('GET', "/reseller-transfer/status/$id");
    }

    /**
     * Update reseller transfer away status.
     *
     * This function updates the status of a reseller transfer away request using a specific transfer ID.
     *
     * @param string $id The transfer ID for the reseller transfer request to update.
     * @param array $postField The request body containing the update parameters:
     *   - `approveType` (string): (Required) Type of approval.  
     *     Allowed values: `"registrant"`, `"partner"`.
     *   - `approve` (bool): (Required) Indicates whether the request is approved (`true`) or not approved (`false`).
     *   - `approveRemarks` (string, optional): Remarks or comments regarding the approval.
     *
     * @return array The API response containing the result of the status update:
     *   - `success` (bool): Indicates if the operation was successful.
     *   - `message` (string): Description or message related to the operation result.
     *
     * Example request:
     * ```php
     * $id = '123456';
     * $postField = [
     *     'approveType' => 'registrant',
     *     'approve' => true,
     *     'approveRemarks' => 'Approval granted by registrant.',
     * ];
     * $response = $webnicSDK->domainTransfer->updateResellerTransferAwayStatus($id, $postField);
     * ```
     */

    public function updateResellerTransferAwayStatus(string $id, array $postField): array
    {
        return $this->sendRequest('PUT', "/reseller-transfer/status/$id", [], $postField);
    }
}
