<?php

namespace Webnic\WebnicSDK\Services\Domain;

use Webnic\WebnicSDK\Core\ApiConnector;

/**
 * Class Contact
 *
 * Manages contact-related operations, including:
 * - Creating and querying contact information.
 * - Validating contact patterns and structures.
 * - Updating contact details, such as nameservers.
 * - Managing contact statuses and categories (e.g., registrant, admin, technical, billing).
 *
 * This class interacts with WebNIC's domain API to handle contact data efficiently.
 *
 * @package Webnic\WebnicDomainSDK
 */
class Contact extends ApiConnector
{

    public function __construct(string $clientId, string $clientSecret, array $config)
    {
        parent::__construct($clientId, $clientSecret, $config);

        // Initialize the serviceUrl based on apiVersion
        $this->serviceUrl = '/domain' . $this->apiVersion . '/contact';
    }


    /**
     * Creates a domain contact.
     *
     * This function allows you to create domain contacts for the registrant, administrator, billing, and technical 
     * contact types. You can provide all contact types at once, or create specific contact types individually.
     *
     * @param array $postField The contact data for the registrant, administrator, billing, and technical contacts, including:
     *   - `registrant` (object, optional): The domain's registrant contact data.
     *   - `administrator` (object, optional): The domain's administrator contact data.
     *   - `billing` (object, optional): The domain's billing contact data.
     *   - `technical` (object, optional): The domain's technical contact data.
     * 
     * Each contact object contains the following fields:
     *   - `category` (string, required): Contact category. Allowed values: `organization`, `individual`.
     *   - `company` (string, required): Company name.
     *   - `firstName` (string, required): First name.
     *   - `lastName` (string, required): Last name.
     *   - `address1` (string, required): Address line 1.
     *   - `address2` (string, optional): Address line 2.
     *   - `city` (string, required): City.
     *   - `state` (string, required): State.
     *   - `countryCode` (string, required): Country code.
     *   - `zip` (string, required): Zip code.
     *   - `phoneNumber` (string, required): Phone number in `+0.0` format.
     *   - `faxNumber` (string, optional): Fax number in `+0.0` format.
     *   - `email` (string, required): Email address.
     *   - `customFields` (object, optional): Custom fields as key-value pairs.
     *   - `authInfo` (string, optional): Authentication information.
     *
     * @return array The API response containing the result of the contact creation:
     *   - `contactId` (string): The contact ID.
     *   - `resid` (number): The reseller ID.
     *   - `details` (object): The created contact details.
     *     - `category` (string): Contact category.
     *     - `company` (string): Company name.
     *     - `firstName` (string): First name.
     *     - `lastName` (string): Last name.
     *     - `address1` (string): Address line 1.
     *     - `address2` (string): Address line 2.
     *     - `city` (string): City.
     *     - `state` (string): State.
     *     - `countryCode` (string): Country code.
     *     - `zip` (string): Zip code.
     *     - `phoneNumber` (string): Phone number in `+0.0` format.
     *     - `faxNumber` (string): Fax number in `+0.0` format.
     *     - `email` (string): Email address.
     *     - `customFields` (object): Custom fields as key-value pairs.
     *     - `authInfo` (string): Authentication information.
     *     - `contactType` (string): Contact type.
     *     - `dtcreate` (string): Date and time when the contact was created.
     * 
     * Example request:
     * ```php
     * $postField = [
     *     'registrant' => [
     *         'category' => 'individual',
     *         'company' => 'qinetics.net',
     *         'firstName' => 'John',
     *         'lastName' => 'Smith',
     *         'address1' => 'Technology Park Malaysia',
     *         'city' => 'Kuala Lumpur',
     *         'state' => 'Kuala Lumpur',
     *         'countryCode' => 'MY',
     *         'zip' => '98000',
     *         'phoneNumber' => '+60.123456789',
     *         'email' => 'john.smith@gmail.com',
     *         'customFields' => [
     *             'organizationType' => 'ORG',
     *             'organizationRegistrationNumber' => '1111111',
     *             'identificationNumber' => '000101081122',
     *         ]
     *     ],
     *     'administrator' => [
     *         // Administrator contact details (similar to registrant)
     *     ],
     *     'billing' => [
     *         // Billing contact details (similar to registrant)
     *     ],
     *     'technical' => [
     *         // Technical contact details (similar to registrant)
     *     ]
     * ];
     * $response = $webnicSDK->contact->createContact($postField);
     * ```
     *
     * ### Required Custom Fields for Common TLDs
     *
     * #### Custom Fields (Organization)
     * | Unique Code | Explanation |
     * |-------------|-------------|
     * | organizationType | Organization type. Allowed values: OTA000005, OTA000003, OTA000025, OTA000006, OTA000012, OTA000032 |
     * | organizationRegistrationNumber | Organization registration number |
     * 
     * #### Custom Fields (Individual)
     * | Type | Description |
     * |------|-------------|
     * | identificationNumber | Identification number |
     * | dateOfBirth | (optional) Date of birth |
     * | gender | (optional) Gender. Allowed values: MALE, FEMALE |
     *
     * ### Required Custom Fields for Specific TLDs
     *
     * #### For .com.cn, .gov.cn, .net.cn, .org.cn, .cn, .中国, .公司, .网络
     * 
     * ##### Custom Fields (Organization)
     * | Unique Code | Explanation |
     * |-------------|-------------|
     * | organizationType | Organization type. Allowed values: ORG, YYZZ, TYDM, BDDM, JDDWFW, SYDWFR, SHTTFR, ZJCS, MBFQY, JJHFR, LSZY, WGZHWH, WLCZJG, SFJD, JWJG, SHFWJG, MBXXBX, YLJGZY, GZJGZY, BJWSXX |
     * | organizationRegistrationNumber | Organization registration number |
     * 
     * ##### Custom Fields (Individual)
     * | Unique Code | Explanation |
     * |-------------|-------------|
     * | identificationNumber | Identification number |
     * | individualType | Legal representative document type. Allowed values: SFZ, HZ, GAJMTX, TWJMTX, WJLSFZ, GAJZZ, TWJZZ, JGZ, QT |
     *
     * #### For .us
     * 
     * ##### Custom Fields (Organization)
     * | Unique Code | Explanation |
     * |-------------|-------------|
     * | organizationType | Organization type. Allowed values: OTA000005, OTA000003, OTA000025, OTA000006, OTA000012, OTA000032 |
     * | organizationRegistrationNumber | Organization registration number |
     * | nexus | Nexus type. Allowed values: C12, C32, C31, C11, C21 |
     *
     * #### For .vn
     * 
     * ##### Custom Fields (Individual)
     * | Unique Code | Explanation |
     * |-------------|-------------|
     * | identificationNumber | Identification number |
     * | organizationRegistrationNumber | Organization registration number |
     * | dateOfBirth | (optional) Date of birth |
     * | gender | (optional) Gender. Allowed values: MALE, FEMALE |
     *
     * #### For .hk, .com.hk, .my
     * 
     * ##### Custom Fields (Individual)
     * | Unique Code | Explanation |
     * |-------------|-------------|
     * | identificationNumber | Identification number |
     * | dateOfBirth | Date of birth |
     * | gender | (optional) Gender. Allowed values: MALE, FEMALE |
     *
     * ### Appendix: Unique Code Explanation
     *
     * #### Unique Code (Organization Type)
     * | Unique Code | Explanation |
     * |-------------|-------------|
     * | OTA000005 | Business |
     * | OTA000003 | Company |
     * | OTA000025 | Non Profit Organization |
     * | OTA000006 | Education |
     * | OTA000012 | Legal Professional |
     * | OTA000032 | Others |
     * | ORG | Organization Code Certificate |
     * | YYZZ | Business License |
     * | TYDM | Certificate for Uniform Social Credit Code |
     * | BDDM | Military Code Designation |
     * | JDDWFW | Military Paid External Service License |
     * | SYDWFR | Public Institution Legal Person Certificate |
     * | SHTTFR | Social Organization Legal Person Registration Certificate |
     * | ZJCS | Religion Activity Site Registration Certificate |
     * | MBFQY | Private Non-Enterprise Entity Registration Certificate |
     * | JJHFR | Fund Legal Person Registration Certificate |
     * | LSZY | Practicing License of Law Firm |
     * | WGZHWH | Registration Certificate of Foreign Cultural Center in China |
     * | WLCZJG | Resident Representative Office of Tourism Departments of Foreign Government Approval Registration Certificate |
     * | SFJD | Judicial Expertise License |
     * | JWJG | Overseas Organization Certificate |
     * | SHFWJG | Social Service Agency Registration Certificate |
     * | MBXXBX | Private School Permit |
     * | YLJGZY | Medical Institution Practicing License |
     * | GZJGZY | Notary Organization Practicing License |
     * | BJWSXX | Beijing School for Children of Foreign Embassy Staff in China Permit |
     * 
     * #### Unique Code (Individual Type)
     * | Unique Code | Explanation |
     * |-------------|-------------|
     * | SFZ | ID |
     * | HZ | Passport |
     * | GAJMTX | Exit-Entry Permit for Travelling to and from Hong Kong and Macao |
     * | TWJMTX | Travel passes for Taiwan Residents to Enter or Leave the Mainland |
     * | WJLSFZ | Foreign Permanent Resident ID Card |
     * | GAJZZ | Residence permit for Hong Kong and Macao residents |
     * | TWJZZ | Residence permit for Taiwan residents |
     * | JGZ | Medical Institution Practicing License |
     * | QT | Others |
     * 
     * #### Unique Code (Nexus)
     * | Unique Code | Explanation |
     * |-------------|-------------|
     * | C12 | A permanent resident of the United States or any U.S. territory/possession |
     * | C32 | An entity that has an office or facility in the U.S |
     * | C31 | An entity that regularly engages in lawful activities (sales of goods or services or other business) |
     * | C11 | Citizen or National of the United States |
     * | C21 | Incorporated within one of the U.S. states or U.S. territory |
     */
    public function createContact(array $postField): array
    {
        return $this->sendRequest('POST', '/create', [], $postField);
    }

    /**
     * Create contact at registry.
     *
     * This function allows you to create a contact at the registry with specific details. You can specify the domain extension,
     * contact type, and the action for which the contact is being created (such as registering, renewing, or transferring the domain).
     *
     * @param string $contactId (required) The ID of the contact.
     * @param string $action (required) The action to perform, allowed values: `"register"`, `"renewal"`, `"transfer"`.
     * @param string $ext (required) The domain extension (e.g., `.com`).
     * @param string $contactType (required) The type of contact (e.g., `"registrant"`, `"administrator"`, `"technical"`, `"billing"`).
     *
     * @return array The response data, including:
     *   - `contactId` (string): Contact ID.
     *   - `resid` (number): Reseller ID.
     *   - `details` (object): Domain's detailed contact object data, including:
     *     - `category` (string): Contact category.
     *     - `company` (string): Company name.
     *     - `firstName` (string): First name.
     *     - `lastName` (string): Last name.
     *     - `address1` (string): Address line 1.
     *     - `address2` (string): Address line 2.
     *     - `city` (string): City.
     *     - `state` (string): State.
     *     - `countryCode` (string): Country code.
     *     - `zip` (string): Zip code.
     *     - `phoneNumber` (string): Phone number in +0.0 format.
     *     - `faxNumber` (string): Fax number in +0.0 format.
     *     - `email` (string): Email address.
     *     - `customFields` (object): Custom fields as key-value pairs.
     *     - `contactType` (string): Contact type.
     *     - `dtcreate` (string): Date and time when the contact was created.
     *     - `id` (number): Associate contact record ID.
     *   - `customFields` (object): Possible response values like:
     *     - `organizationType`: Organization type values (e.g., "OTA000005", "ORG").
     *     - `organizationRegistrationNumber`: Organization registration number.
     *     - `individualType`: Individual type values (e.g., "SFZ", "HZ").
     *     - `identificationNumber`: Identification number.
     *     - `nexus`: Nexus type values (e.g., "C12", "C32").
     *     - `dateOfBirth`: Date of birth.
     *     - `gender`: Gender (e.g., "MALE", "FEMALE").
     *
     * Example request:
     * ```php
     * $ext = "com";
     * $contactType = "registrant";
     * $contactId = "WEBNIC1030T";
     * $action = "register";
     * $response = $webnicSDK->createContactAtRegistry($ext, $contactType, $contactId, $action);
     * ```
     */

    public function createContactAtRegistry(string $ext, string $contactType, string $contactId, string $action): array
    {
        return $this->sendRequest('POST', "/create/$ext/$contactType",  ['contactId' => $contactId, 'action' => $action]);
    }


    /**
     * Query a contact handle information.
     *
     * This function allows you to query the contact information based on a given contact ID. The details of the contact are returned
     * including its category, company, name, address, phone number, email, and custom fields.
     *
     * @param string $contactId The ID of the contact to query.
     * 
     * @return array The response data, including:
     *   - `contactId` (string): Contact ID.
     *   - `resid` (number): Reseller ID.
     *   - `details` (object): Domain's detailed contact object data, including:
     *     - `category` (string): Contact category.
     *     - `company` (string): Company name.
     *     - `firstName` (string): First name.
     *     - `lastName` (string): Last name.
     *     - `address1` (string): Address line 1.
     *     - `address2` (string): Address line 2.
     *     - `city` (string): City.
     *     - `state` (string): State.
     *     - `countryCode` (string): Country code.
     *     - `zip` (string): Zip code.
     *     - `phoneNumber` (string): Phone number in +0.0 format.
     *     - `faxNumber` (string): Fax number in +0.0 format.
     *     - `email` (string): Email address.
     *     - `customFields` (object): Custom fields as key-value pairs.
     *     - `contactType` (string): Contact type.
     *     - `dtcreate` (string): Date and time when the contact was created.
     *     - `id` (number): Contact record ID.
     *
     * Example request:
     * ```php
     * $contactId = "WN964975T";
     * $response = $webnicSDK->queryContactInfo($contactId);
     * ```
     */

    public function queryContactInfo(string $contactId): array
    {
        return $this->sendRequest('GET', '/query', ['contactId' => $contactId]);
    }

    /**
     * Modify a contact at the registry.
     *
     * This function allows you to modify an existing contact's details at the registry. The request includes the contact ID
     * and the new contact details to be updated, such as company, address, phone number, email, and custom fields.
     *
     * @param array $postField The contact details to modify, including:
     *   - `contactId` (string, required): The ID of the contact to modify.
     *   - `details` (object, required): The domain's detailed contact object data.
     *     - `category` (string, required): Contact category. Allowed values: `"organization"`, `"individual"`.
     *     - `company` (string, required): Company name.
     *     - `firstName` (string, required): First name.
     *     - `lastName` (string, required): Last name.
     *     - `address1` (string, required): Address line 1.
     *     - `address2` (string, optional): Address line 2.
     *     - `city` (string, required): City.
     *     - `state` (string, required): State.
     *     - `countryCode` (string, required): Country code.
     *     - `zip` (string, required): Zip code.
     *     - `phoneNumber` (string, required): Phone number in +0.0 format.
     *     - `faxNumber` (string, optional): Fax number in +0.0 format.
     *     - `email` (string, required): Email address.
     *     - `customFields` (object, optional): Custom fields as key-value pairs.
     *
     * Custom Fields (organization):
     *   - `organizationType` (string, required): Organization type. E.g., `"OTA000005"`, `"ORG"`, `"SFJD"`, etc.
     *   - `organizationRegistrationNumber` (string, required): Organization registration number.
     *   
     * Custom Fields (individual):
     *   - `identificationNumber` (string, required): Identification number.
     *   - `individualType` (string, optional): Individual type. E.g., `"SFZ"`, `"GAJMTX"`, `"WJLSFZ"`, etc.
     *   - `dateOfBirth` (string, optional): Date of birth.
     *   - `gender` (string, optional): Gender. E.g., `"MALE"`, `"FEMALE"`.
     *   - `organizationRegistrationNumber` (string, optional): Organization registration number.

     * @return array The response data, including:
     *   - `contactId` (string): The modified contact's ID.
     *   - `resid` (number): Reseller ID.
     *   - `details` (object): Domain's modified contact object data.
     *     - `category` (string): Contact category.
     *     - `company` (string): Company name.
     *     - `firstName` (string): First name.
     *     - `lastName` (string): Last name.
     *     - `address1` (string): Address line 1.
     *     - `address2` (string): Address line 2.
     *     - `city` (string): City.
     *     - `state` (string): State.
     *     - `countryCode` (string): Country code.
     *     - `zip` (string): Zip code.
     *     - `phoneNumber` (string): Phone number in +0.0 format.
     *     - `faxNumber` (string): Fax number in +0.0 format.
     *     - `email` (string): Email address.
     *     - `customFields` (object): Custom fields as key-value pairs.
     *     - `contactType` (string): Contact type.
     *     - `dtcreate` (string): Date and time when the contact was created.
     *     - `id` (number): Contact record ID.

     * Example request:
     * ```php
     * $postField = [
     *     'contactId' => 'WNC968945T',
     *     'details' => [
     *         'category' => 'individual',
     *         'company' => 'qinetics.net',
     *         'firstName' => 'tesssss',
     *         'lastName' => 'Smith',
     *         'address1' => 'Technology Park Malaysia',
     *         'city' => 'Kuala Lumpur',
     *         'state' => 'Kuala Lumpur',
     *         'countryCode' => 'MY',
     *         'zip' => '98000',
     *         'phoneNumber' => '+60.123456789',
     *         'email' => 'john.smith@gmail.com'
     *     ]
     * ];
     * $response = $webnicSDK->contact->modifyContactAtRegistry($postField);
     * ```
     */

    public function modifyContactAtRegistry(array $postField): array
    {
        return $this->sendRequest('POST', '/modify-at-registry', [], $postField);
    }


    /**
     * Modify contact information.
     *
     * This function allows you to modify an existing contact's details such as company, name, address, phone number, email, and custom fields.
     *
     * @param array $modifyContactData The contact details to modify. It should include the following keys:
     *   - `contactId` (string, required): The ID of the contact to modify.
     *   - `details` (object, required): The contact's detailed information to be modified, including:
     *     - `category` (string, required): Contact category. Allowed values: "organization", "individual".
     *     - `company` (string, required): Company name.
     *     - `firstName` (string, required): First name.
     *     - `lastName` (string, required): Last name.
     *     - `address1` (string, required): Address line 1.
     *     - `address2` (string, optional): Address line 2.
     *     - `city` (string, required): City.
     *     - `state` (string, required): State.
     *     - `countryCode` (string, required): Country code.
     *     - `zip` (string, required): Zip code.
     *     - `phoneNumber` (string, required): Phone number in +0.0 format.
     *     - `faxNumber` (string, optional): Fax number in +0.0 format.
     *     - `email` (string, required): Email address.
     *     - `customFields` (object, optional): Custom fields as key-value pairs.
     *       - For organizations: `organizationType`, `organizationRegistrationNumber`, etc.
     *       - For individuals: `identificationNumber`, `individualType`, `dateOfBirth`, `gender`, etc.
     *
     * @return array The response data, including:
     *   - `contactId` (string): Contact ID.
     *   - `resid` (number): Reseller ID.
     *   - `details` (object): Modified contact details, same structure as provided in the request.
     *     - `category`, `company`, `firstName`, `lastName`, `address1`, `address2`, `city`, `state`, `countryCode`, `zip`, `phoneNumber`, `faxNumber`, `email`, `customFields`, etc.
     *     - `contactType` (string): Contact type.
     *     - `dtcreate` (string): Date and time when the contact was created.
     *     - `id` (number): Contact record ID.
     *
     * Example request:
     * ```php
     * $postField = [
     *     'contactId' => 'WNC969003T',
     *     'details' => [
     *         'category' => 'individual',
     *         'company' => 'qinetics.net',
     *         'firstName' => 'John',
     *         'lastName' => 'Smith',
     *         'address1' => 'Technology Park Malaysia',
     *         'city' => 'Kuala Lumpur',
     *         'state' => 'Kuala Lumpur',
     *         'countryCode' => 'MY',
     *         'zip' => '98000',
     *         'phoneNumber' => '+60.123456789',
     *         'email' => 'john.smith@gmail.com',
     *         'customFields' => [
     *             'identificationNumber' => '000101081122'
     *         ]
     *     ]
     * ];
     * $response = $webnicSDK->contact->modifyContact($postField);
     * ```
     */

    public function modifyContact(array $postField): array
    {
        return $this->sendRequest('POST', '/modify', [], $postField);
    }

    /**
     * Bulk replace contact information for domains.
     *
     * This function allows you to replace contact information for multiple domains. You can specify new contact IDs for different roles such as registrant, administrator, technical, and billing.
     *
     * @param array $postField The request body, including the following fields:
     *   - `domainList` (array, required): A list of domain names for which the contact information will be replaced.
     *   - `contactTypeMap` (object, required): A map of contact roles and the corresponding new contact IDs.
     *     - `registrant` (string, optional): New registrant contact ID.
     *     - `administrator` (string, optional): New administrator contact ID.
     *     - `technical` (string, optional): New technical contact ID.
     *     - `billing` (string, optional): New billing contact ID.
     *
     * @return array The response data, including:
     *   - `data` (string): The bulk contact replace task ID.
     *
     * Example request:
     * ```php
     * $postField = [
     *     'domainList' => ['jimtestrestreg15.com', 'jimtestrestreg16.com'],
     *     'contactTypeMap' => [
     *         'registrant' => 'WNC968542T',
     *         'administrator' => 'WNC968543T',
     *         'technical' => 'WNC968544T',
     *         'billing' => 'WNC968545T'
     *     ]
     * ];
     * $response = $webnicSDK->contact->replaceContact($postField);
     * ```
     */
    public function replaceContact(array $postField): array
    {
        return $this->sendRequest('POST', '/replace', [], $postField);
    }

    /**
     * Delete a contact which is no longer in use.
     *
     * This function allows you to delete a contact based on the given contact ID. After successful deletion, the contact will no longer be available.
     *
     * @param string $contactId The ID of the contact to be deleted.
     *
     * @return array The response data, including:
     *   - `data` (string): The ID of the deleted contact.
     *
     * Example request:
     * ```php
     * $contactId = "WN964974T";
     * $response = $webnicSDK->contact->deleteContact($contactId);
     * ```
     */
    public function deleteContact(string $contactId): array
    {
        return $this->sendRequest('DELETE', '/delete', ['contactId' => $contactId]);
    }
}
