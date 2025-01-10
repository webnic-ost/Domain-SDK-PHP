<?php

namespace Webnic\WebnicSDK\Services\DNS;

use Webnic\WebnicSDK\Core\ApiConnector;

class DNSZoneRecordTemplate extends ApiConnector
{

    public function __construct(string $clientId, string $clientSecret, array $config)
    {
        parent::__construct($clientId, $clientSecret, $config);

        // Initialize the serviceUrl based on apiVersion
        $this->serviceUrl = '/dns' . $this->apiVersion . '/zone/subscription';
    }

    /**
     * Retrieves the DNS zone subscription record templates.
     *
     * This endpoint returns the list of DNS zone subscription record templates.
     *
     * @param array $params Associative array of query parameters to filter the templates:
     *     - `name` (string, optional): The name of the template.
     *     - `limit` (integer, optional): The number of templates to show.
     *
     * @return array The API response containing zone subscription record templates:
     *   - `id` (number): The template ID.
     *   - `name` (string): The template name.
     *   - `defaultTemplate` (boolean): Indicates if the template is the default template.
     *   - `records` (object[]): A list of DNS record objects.
     *     - `type` (string): The type of the DNS record (e.g., 'A', 'AAAA', 'MX').
     *     - `name` (string): The name of the DNS record.
     *     - `ttl` (number): The time-to-live (TTL) in milliseconds for the record.
     *     - `rdatas` (object[]): A list of rdata objects with record values and metadata.
     *       - `value` (string): The value of the DNS record.
     *       - `attributes` (object): Metadata related to the DNS record.
     *     - `remarks` (string): Additional remarks about the record.
     *   - `dtcreate` (string): The date and time when the template was created.
     *   - `dtmodify` (string): The date and time when the template was modified.
     *
     * Example usage:
     * ```php
     * $response = $webnicSDK->DNSZoneRecordTemplate->getZoneSubscriptionRecordTemplates(['name' => 'default', 'limit' => 10]);
     * ```
     */
    public function getZoneSubscriptionRecordTemplates(array $params): array
    {
        return $this->sendRequest('GET', "/templates", ['name' => $params['name'] ?? "", 'limit' => $params['limit'] ?? ""]);
    }

    /**
     * Creates a new zone subscription record template.
     *
     * When a template is defined as the default template, it will automatically be applied to any new domains
     * added to your account, ensuring they inherit the predefined DNS settings.
     * Applying a default template will override any existing DNS records for those domains.
     *
     * @param array $postField The data required to create the template, including the template name and default template flag.
     * 
     * @return array The API response containing the template details.
     *
     * Example usage:
     * ```php
     * $response = $webnicSDK->DNSZoneRecordTemplate->createZoneSubscriptionRecordTemplate([
     *     'templateName' => 'NewTemplate',
     *     'defaultTemplate' => true
     * ]);
     * ```
     */
    public function createZoneSubscriptionRecordTemplate(array $postField): array
    {
        return $this->sendRequest('POST', "/template", [], $postField);
    }

    /**
     * Retrieves a zone subscription record template by its ID.
     *
     * This endpoint allows you to fetch details of a zone subscription record template using its template ID.
     *
     * @param string $templateId The ID of the template to retrieve.
     *
     * @return array The API response containing the template details.
     *
     * Example usage:
     * ```php
     * $response = $webnicSDK->DNSZoneRecordTemplate->getZoneSubscriptionRecordTemplatebyId('12345');
     * ```
     */
    public function getZoneSubscriptionRecordTemplatebyId(string $templateId): array
    {
        return $this->sendRequest('GET', "/template/$templateId");
    }


    /**
     * Updates the template name and defaultTemplate status for a zone subscription record template by template ID.
     *
     * This endpoint allows you to change the `templateName` and the `defaultTemplate` status using the template ID.
     *
     * @param string $templateId The ID of the template to be updated.
     * @param array $postField The details for updating the template, including `templateName` and `defaultTemplate`.
     *
     * @return array The API response confirming the template update.
     *
     * Example usage:
     * ```php
     * $postField = [
     *     'templateName' => 'New Template Name',
     *     'defaultTemplate' => true
     * ];
     * $response = $webnicSDK->DNSZoneRecordTemplate->updateZoneSubscriptionRecordTemplatebyId('12345', $postField);
     * ```
     */
    public function updateZoneSubscriptionRecordTemplatebyId(string $templateId, array $postField): array
    {
        return $this->sendRequest('PUT', "/template/$templateId", [], $postField);
    }


    /**
     * Add Zone Subscription Record to Zone Subscription Record Template
     * Removes a zone subscription record from a specific zone subscription record template.
     *
     * This endpoint allows you to remove a zone subscription record from an existing template using the template ID.
     *
     * @param string $templateId The ID of the template from which the record will be removed.
     * @param array $postField The details of the record to be removed.
     *
     * @return array The API response confirming the record removal from the template.
     *
     * Example usage:
     * ```php
     * $postField = [
     *     'type' => 'A',
     *     'name' => 'www',
     *     'rdatas' => [
     *         [
     *             'value' => '192.0.2.1'
     *         ]
     *     ]
     * ];
     * $response = $webnicSDK->DNSZoneRecordTemplate->removeZoneSubFromTemplate('12345', $postField);
     * ```
     */

    public function addZoneSubToTemplate(string $templateId, array $postField): array
    {
        return $this->sendRequest('POST', "/template/$templateId/add", [], $postField);
    }


    /**
     * Remove Zone Subscription Record from Zone Subscription Record Template
     * Removes a zone subscription record from a specific zone subscription record template.
     *
     * This endpoint allows you to remove a zone subscription record from an existing template using the template ID.
     *
     * @param string $templateId The ID of the template from which the record will be removed.
     * @param array $postField The details of the record to be removed.
     *
     * @return array The API response confirming the record removal from the template.
     *
     * Example usage:
     * ```php
     * $postField = [
     *     'type' => 'A',
     *     'name' => 'www',
     *     'rdatas' => [
     *         [
     *             'value' => '192.0.2.1'
     *         ]
     *     ]
     * ];
     * $response = $webnicSDK->DNSZoneRecordTemplate->removeZoneSubFromTemplate('12345', $postField);
     * ```
     */
    public function removeZoneSubFromTemplate(string $templateId, array $postField): array
    {
        return $this->sendRequest('POST', "/template/$templateId/remove", [], $postField);
    }

    /**
     * Deletes a specific zone subscription record template.
     *
     * This endpoint allows you to delete a zone subscription record template using its template ID.
     *
     * @param string $templateId The ID of the template to delete.
     *
     * @return array The API response confirming the deletion.
     *
     * Example usage:
     * ```php
     * $response = $webnicSDK->DNSZoneRecordTemplate->deleteZoneSubscriptionRecordTemplate('12345');
     * ```
     */

    public function deleteZoneSubscriptionRecordTemplate(string $templateId): array
    {
        return $this->sendRequest('DELETE', "/template/$templateId");
    }
}
