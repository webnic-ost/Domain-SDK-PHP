<?php

namespace Webnic\WebnicSDK\Services\Domain;

use Webnic\WebnicSDK\Core\ApiConnector;


class Registrant extends ApiConnector
{



    public function __construct(string $clientId, string $clientSecret, array $config)
    {
        parent::__construct($clientId, $clientSecret, $config);

        // Initialize the serviceUrl based on apiVersion
        $this->serviceUrl = '/domain' . $this->apiVersion;
    }

    /**
     * Create registrant account.
     *
     * This function creates a new registrant account using the provided username.
     *
     * @param string $username The username for the registrant account.
     *   - Must be at least 10 characters long.
     *   - Can only include alphanumeric characters, dots (`.`), dashes (`-`), and underscores (`_`).
     *
     * @return array The API response containing details of the created registrant account:
     *   - `registrantUserId` (string): The unique ID of the registrant user.
     *   - `resid` (number): The reseller ID associated with the account.
     *   - `username` (string): The username of the created registrant account.
     *   - `password` (string): The password of the registrant account.
     *   - `dtcreate` (string): The date and time when the registrant account was created.
     *
     * Example request:
     * ```php
     * $username = 'new_user123';
     * $response = $webnicSDK->registrant->createAccount($username);
     * ```
     */
    public function createAccount(string $username): array
    {
        return $this->sendRequest('POST', '/registrant/create', ['username' => $username]);
    }

    /**
     * Get registrant account list.
     *
     * This function retrieves a list of registrant accounts based on the provided username.
     *
     * @param string $username The username of the registrant account for which to fetch the account list.
     *
     * @return array The API response containing the registrant account list:
     *   - `id` (string): The unique ID of the registrant account.
     *   - `username` (string): The username of the registrant account.
     *
     * Example request:
     * ```php
     * $username = 'existing_user';
     * $response = $webnicSDK->registrant->getAccountList($username);
     * ```
     */
    public function getAccountList(string $username): array
    {
        return $this->sendRequest('GET', '/registrant/list', ['username' => $username]);
    }

    /**
     * Send registrant's login info to registrant email.
     *
     * This function sends the login information associated with a specific domain to the registrant's registered email address.
     *
     * @param string $domainName The domain name for which the login information should be sent.
     * 
     * @return array The API response indicating the result of the login info sending:
     *   - `status` (string): Status of the operation, e.g., "success" or "failure".
     *   - `message` (string): Detailed message about the operation's result.
     *
     * Example request:
     * ```php
     * $domainName = 'example.com';
     * $response = $webnicSDK->registrant->sendLoginInfo($domainName);
     * ```
     */
    public function sendLoginInfo(string $domainName): array
    {
        return $this->sendRequest('POST', '/send/login-info', ['domainName' => $domainName]);
    }

    /**
     * Update the registrant user for a list of domains.
     *
     * This function allows you to update the registrant user for a list of domains. You provide the new registrant username and the domains
     * for which the registrant needs to be updated.
     *
     * @param array $postField An array containing the parameters for updating the registrant:
     *   - `username` (string): The registrant username to assign to the domains.
     *   - `domainList` (array of strings): A list of domain names for which the registrant user should be updated.
     *
     * @return array The API response containing the result of the update:
     *   - `success` (array): List of domains where the registrant user was successfully updated.
     *   - `fail` (array): List of domains where the registrant user update failed.
     *
     * Example request:
     * ```php
     * $postField = [
     *     'username' => 'callmedragon23',
     *     'domainList' => [
     *         '20240306wbtest.com',
     *         'jimtestrestreg15.com',
     *         'webtest.ac'
     *     ],
     * ];
     * $response = $webnicSDK->registrant->updateRegistrantUserByDomainList($postField);
     * ```
     */

    public function updateRegistrantUserByDomainList(array $postField): array
    {
        return $this->sendRequest('POST', '/domain-list/registrant', [], $postField);
    }

    /**
     * Relocate registrant account.
     *
     * This function allows you to relocate the registrant account for a given domain. You provide the domain name and the new registrant
     * username, and the registrant account will be updated accordingly.
     *
     * @param string $domainName The domain name for which the registrant account should be modified.
     * @param string $registrantUsername The new registrant username to assign to the domain.
     *
     * @return array The API response containing the result of the account modification:
     *   - `status` (string): The status of the relocation request, such as "success" or "failure".
     *   - `domain` (string): The domain name that was updated.
     *   - `registrantUsername` (string): The new registrant username assigned to the domain.
     *
     * Example request:
     * ```php
     * $domainName = 'example.com';
     * $registrantUsername = 'newregistrantUser';
     * $response = $webnicSDK->registrant->modifyAccount($domainName, $registrantUsername);
     * ```
     */

    public function modifyAccount(string $domainName, string $registrantUsername): array
    {
        return $this->sendRequest('POST', '/registrant-account', ['domainName' => $domainName, 'registrantUsername' => $registrantUsername]);
    }
}
