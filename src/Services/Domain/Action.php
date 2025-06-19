<?php

namespace Webnic\WebnicSDK\Services\Domain;

use Webnic\WebnicSDK\Core\ApiConnector;
use Webnic\WebnicSDK\Services\Domain\Contact;
use Webnic\WebnicSDK\Services\Domain\Domain;
use Webnic\WebnicSDK\Services\Domain\DomainTransfer;
use Webnic\WebnicSDK\Services\Domain\DomainProduct;
use Webnic\WebnicSDK\Services\Domain\DomainHost;
use Webnic\WebnicSDK\Services\Domain\Registrant;


class Action extends ApiConnector
{

    protected $contact;
    protected $domain;
    protected $domainTransfer;
    protected $domainProduct;
    protected $domainHost;
    protected $registrant;

    public function __construct(string $clientId, string $clientSecret, array $config)
    {
        parent::__construct($clientId, $clientSecret, $config);

        // Initialize the serviceUrl based on apiVersion
        $this->serviceUrl = '/domain' . $this->apiVersion;


        $this->contact = new Contact($clientId, $clientSecret, $config);
        $this->domain = new Domain($clientId, $clientSecret, $config);
        $this->domainTransfer = new DomainTransfer($clientId, $clientSecret, $config);
        $this->domainProduct = new DomainProduct($clientId, $clientSecret, $config);
        $this->domainHost = new DomainHost($clientId, $clientSecret, $config);
        $this->registrant = new Registrant($clientId, $clientSecret, $config);
    }

    /**
     * Searches for domain information and transfer eligibility.
     *
     * @param string $domainName The domain name to search (e.g., "example.com").
     * 
     * @return array An array with domain details and optional transfer eligibility data.
     *
     * Example response:
     * ```php
     * [
     *   "queryDomain" => [
     *     "status" => "active",
     *     "expirationDate" => "2024-10-01",
     *     "nameservers" => ["ns1.example.com", "ns2.example.com"],
     *   ],
     *   "queryTransferType" => [
     *     "transferEligibility" => "eligible",
     *     "reason" => null
     *   ]
     * ]
     * ```
     */

    public function searchDomain(string $domainName): array
    {
        $queryDomainData = $this->domain->queryDomain($domainName);

        $returnData = [
            "queryDomain" => $queryDomainData['data']
        ];

        if ($queryDomainData['http_status_code'] == '200' && $queryDomainData['code'] = '1000') {
            $queryTransferType = $this->domainTransfer->queryTransferType($domainName);

            $returnData['queryTransferType'] = $queryTransferType['data'];
        }

        return $returnData;
    }

    /**
     * Registers a domain name with the specified data, DNS settings, and contact information.
     *
     * @param array $domainData An associative array containing the domain details.
     *    Example structure:
     *    ```php
     *    [
     *      "domainName" => "example.com",
     *      "period" => 1, // Registration period in years
     *      // Other domain-specific details
     *    ]
     *    ```
     * @param array $dnsData An associative array containing DNS settings.
     *    Example structure:
     *    ```php
     *    [
     *      "nameservers" => ["ns1.example.com", "ns2.example.com"],
     *      "ipList" => ["192.168.1.1", "192.168.1.2"], // Optional
     *      "extList" => ["com", "com"],                // Optional 
     *    ]
     *    ```
     * @param array $contactIds (Optional) Predefined contact IDs.
     *    Example structure:
     *    ```php
     *    [
     *      "registrantContactId" => "12345",
     *      "administratorContactId" => "67890",
     *      "technicalContactId" => "11223",
     *      "billingContactId" => "44556"
     *    ]
     *    ```
     * @param array $contactData (Optional) Data to create new contacts if `contactIds` is not provided.
     *    Example structure:
     *    ```php
     *    [
     *      "registrant" => [...],
     *      "administrator" => [...],
     *      "technical" => [...],
     *      "billing" => [...]
     *    ]
     *    ```
     * @param string $registrantId (Optional) The ID of the registrant.
     * @param string $registrantUsername (Optional) The username to create a registrant account if `registrantId` is not provided.
     *
     * @return array The response data from the registration process.
     *
     * Example response:
     * ```php
     * [
     *   "status" => "success",
     *   "data" => [
     *     "domainName" => "example.com",
     *     "registrationDate" => "2024-01-01",
     *     "expirationDate" => "2025-01-01",
     *     "nameservers" => ["ns1.example.com", "ns2.example.com"]
     *   ]
     * ]
     * ```
     *
     * Possible error messages:
     * - "Domain Name is not available"
     * - "Extension is missing for one of the nameservers"
     * - "Name Server is not available"
     * - "Creating Contact Failed"
     * - "Create Registrant Account Failed"
     * - "Post Register Domain Failed"
     */
    public function registerDomain(
        array $domainData,
        array $dnsData,
        array $contactIds = [],
        array $contactData = [],
        string $registrantId = "",
        string $registrantUsername = ""
    ) {
        // Validate inputs early
        $validationErrors = $this->validateInputs($domainData, $dnsData, $contactIds, $contactData, $registrantId, $registrantUsername);
        if (!empty($validationErrors)) {
            return $validationErrors;
        }

        // Query domain availability
        $queryDomain = $this->domain->queryDomain($domainData['domainName']);
        if (!$queryDomain['data']['available']) {
            return [
                'code' => '2400',
                'error' => ['message' => 'Domain Name is not available'],
                'queryDomain' => $queryDomain['data'],
            ];
        }
        // Validate nameservers
        foreach ($dnsData['nameservers'] as $key => $nameserver) {
            if (empty($nameserver)) {
                continue;
            }
            $extension = $dnsData['extList'][$key] ?? null;
            if (!$extension) {
                $parts = explode('.', $nameserver);
                $extension = end($parts);
            }

            $hostCheck = $this->domainHost->checkHost($nameserver, $extension);
            if ($hostCheck['data']['available']) {
                // Attempt to get IPv4 addresses for the nameserver
                $ipList = gethostbynamel($nameserver);

                // Fallback to empty array if DNS lookup fails
                if ($ipList === false) {
                    $ipList = [];
                }

                $createHostData = array(
                    "host" => isset($nameserver) ? $nameserver : "",
                    'ipList' => $ipList,
                );

                if (isset($extension)) {
                    $createHostData['ext'] = $extension;
                }
                // Register NS 
                $createHost = $this->domainHost->createHostByExtension($createHostData);

                if ($createHost['code'] != "1000") {
                    return  $createHost;
                }
            }
        }

        // Prepare contact keys
        $contactKeys = $this->prepareContacts($contactIds, $contactData);
        if (isset($contactKeys['error'])) {
            return $contactKeys;
        }

        // Prepare registrant key
        $registrantKey = $this->prepareRegistrant($registrantId, $registrantUsername);
        if (isset($registrantKey['error'])) {
            return $registrantKey;
        }

        // Register domain
        $domainData = array_merge($domainData, $contactKeys, [
            'nameservers' => $dnsData['nameservers'],
            'registrantUserId' => $registrantKey,
        ]);


        $registerDomain = $this->domain->registerDomain($domainData);
        if ($registerDomain['code'] === '2400') {
            return [
                'code' => '2400',
                "error" => ['message' => 'Post Register Domain Failed'],
                'registerDomain' => $registerDomain,
            ];
        }
        $registerDomain['domainData'] = $domainData;

        return $registerDomain;
    }

    // Validate inputs
    private function validateInputs($domainData, $dnsData, $contactIds, $contactData, $registrantId, $registrantUsername)
    {
        if (empty($domainData['domainName'])) {
            return [
                'code' => '2400',
                'error' => ['message' => 'Parameter Error: domainName is required'],
            ];
        }

        if (empty($dnsData['nameservers']) || empty($dnsData['ext'])) {
            return [
                'code' => '2400',
                'error' => ['message' => 'Parameter Error: nameservers and ext are required'],
            ];
        }

        if (empty($contactIds) && empty($contactData)) {
            return [
                'code' => '2400',
                'error' => ['message' => 'Parameter Error: Either contactIds or contactData must be provided'],
            ];
        }

        if (empty($registrantId) && empty($registrantUsername)) {
            return [
                'code' => '2400',
                'error' => ['message' => 'Parameter Error: Either registrantId or registrantUsername must be provided'],
            ];
        }

        return [];
    }

    // Prepare contact information
    private function prepareContacts(array $contactIds, array $contactData): array
    {
        if (!empty($contactIds)) {
            $requiredKeys = [
                'registrantContactId',
                'administratorContactId',
                'technicalContactId',
                'billingContactId',
            ];

            foreach ($requiredKeys as $key) {
                if (!array_key_exists($key, $contactIds)) {
                    return [
                        'code' => '2400',
                        'error' => ['message' => "Missing required Id: $key"],
                    ];
                }
            }
            return $contactIds;
        }

        // Create new contacts if contactIds are not provided
        $createContact = $this->contact->createContact($contactData);
        if ($createContact['code'] === '2400') {
            return [
                'code' => '2400',
                'error' => ['message' => "Creating contact failed"],
                'createContact' => $createContact,
            ];
        }

        $contactKeys = [];
        foreach ($createContact['data'] as $contact) {
            $contactKeys[$contact['contactType'] . 'ContactId'] = $contact['contactId'];
        }

        // Check for missing contact IDs and copy registrantContactId if necessary
        if (!isset($contactKeys['administratorContactId'])) {
            $contactKeys['administratorContactId'] = $contactKeys['registrantContactId'];
        }
        if (!isset($contactKeys['technicalContactId'])) {
            $contactKeys['technicalContactId'] = $contactKeys['registrantContactId'];
        }
        if (!isset($contactKeys['billingContactId'])) {
            $contactKeys['billingContactId'] = $contactKeys['registrantContactId'];
        }

        return $contactKeys;
    }

    // Prepare registrant information
    private function prepareRegistrant(string $registrantId, string $registrantUsername)
    {
        if (!empty($registrantId)) {
            return $registrantId;
        }

        $createRegistrant = $this->registrant->createAccount($registrantUsername);
        if ($createRegistrant['code'] === '2400') {
            return [
                'code' => '2400',
                'error' => ['message' => 'Create Registrant Account Failed'],
                'details' => $createRegistrant,
            ];
        }

        return $createRegistrant['data']['registrantUserId'];
    }
    /**
     * Retrieves detailed information about a domain, including contact details.
     *
     * This function fetches domain information based on the provided domain name. It can also return 
     * contact information related to the domain, such as registrant, admin, technical, and billing contacts.
     * The function allows you to control the level of contact information returned, either by including 
     * it entirely or only retrieving contact details when necessary.
     *
     * @param string $domainName     The domain name for which information is being retrieved (e.g., 'example.com').
     * @param bool $contactInfo      Optional. If set to `true`, the function will include detailed contact information.
     *                              Default is `false`.
     * @param bool $onlyContact      Optional. If set to `true`, the function will return only the contact information,
     *                              excluding domain-related data. Default is `false`.
     *
     * @return array An associative array containing the domain information and contact details if requested.
     *               The array may include the following keys:
     *               - 'domainInfo': Basic domain information (such as registration status).
     *               - 'registrant': Registrant contact information.
     *               - 'admin': Admin contact information.
     *               - 'technical': Technical contact information.
     *               - 'billing': Billing contact information.
     *               - 'contactId': IDs for the various contact types (registrant, admin, technical, billing).
     *
     * @throws \Exception If the domain information cannot be retrieved or an error occurs during the request.
     */
    public function infoDomain(string $domainName, bool $contactInfo = false, bool $onlyContact = false)
    {
        $domainInfo = $this->domain->getDomainInfo($domainName);

        if ($domainInfo['code'] != "1000") {
            return  $domainInfo;
        }

        $returnData = [
            "domainInfo" => $domainInfo['data']
        ];

        if (!$contactInfo || $domainInfo['code'] != "1000") {
            return  $domainInfo;
        }

        $contactId = $domainInfo['data']['contactId'];

        $registrant = $this->contact->queryContactInfo($contactId['registrant'])['data'];
        $admin = $this->contact->queryContactInfo($contactId['admin'])['data'];
        $technical = $this->contact->queryContactInfo($contactId['technical'])['data'];
        $billing = $this->contact->queryContactInfo($contactId['billing'])['data'];

        if ($onlyContact) {
            return [
                'code' => "1000",
                'contactId' => $contactId,
                'registrant' => $registrant,
                'admin' => $admin,
                'technical' => $technical,
                'billing' => $billing,
            ];
        }

        $returnData['code'] = "1000";
        $returnData['registrant'] = $registrant;
        $returnData['admin'] = $admin;
        $returnData['technical'] = $technical;
        $returnData['billing'] = $billing;


        return $returnData;
    }

    /**
     * Renews a domain for a specified term.
     *
     * This function allows you to renew a domain for a specified period (term). You can also specify the 
     * domain type (e.g., standard, premium) to tailor the renewal request. If the domain type is not provided,
     * the renewal will proceed with the default settings.
     *
     * @param string $domainName   The domain name to be renewed (e.g., 'example.com').
     * @param string $term         The term (duration) for which to renew the domain (e.g., '1', '2', '5' years).
     * @param string $domainType   Optional. The type of domain to renew (e.g., 'premium', 'standard'). If not provided, 
     *                             the default domain type will be used.
     *
     * @return mixed The result of the domain renewal process. This will typically be a response from the API 
     *               indicating success or failure of the renewal.
     *
     * @throws \Exception If the renewal request fails or the parameters are invalid.
     */
    public function renewDomain(string $domainName, string $term, string $domainType = '')
    {
        $renewData = [
            'domainName' => $domainName,
            'term' => $term,
        ];

        if ($domainType) {
            $renewData['domainType'] = $domainType;
        }

        return $this->domain->renewDomain($renewData);
    }

    /**
     * Transfers a domain to a new registrar or reseller.
     *
     * This function handles the transfer of a domain, either through a registrar transfer or a reseller transfer.
     * It first checks the transfer type for the domain, and based on the transfer type, it submits the appropriate
     * transfer request (registrar or reseller). The function also accepts optional parameters for registrant user ID,
     * authorization info, contact IDs, and nameservers.
     *
     * @param string $domainName         The domain name to be transferred (e.g., 'example.com').
     * @param string $registrantUserId   Optional. The user ID of the registrant. If not provided, the default will be used.
     * @param string $authInfo           Optional. The authorization code for the domain transfer.
     * @param array $contactIds          Optional. An associative array of contact IDs for the transfer process:
     *                                  - 'registrantContactId'
     *                                  - 'administratorContactId'
     *                                  - 'technicalContactId'
     *                                  - 'billingContactId'
     * @param array $nameservers         Optional. A list of nameservers to associate with the domain during transfer.
     *
     * @return array An associative array containing the result of the domain transfer request. 
     *               This may include a message, data, and status codes related to the transfer.
     *
     * @throws \Exception If the transfer request fails or an error occurs during the transfer process.
     */
    public function transferDomain(
        string $domainName,
        string $registrantUserId = '',
        string $authInfo = '',
        array $contactIds = [],
        array $nameservers = []
    ) {
        $transferTypeResponse = $this->domainTransfer->queryTransferType($domainName);

        if ($transferTypeResponse['code'] != '1000') {
            return $transferTypeResponse;
        }

        $transferType = $transferTypeResponse['data']['transferType'];

        if ($transferType == 'registrar_transfer') {
            $domainData = $this->domain->queryDomain($domainName);

            $transferData = [
                'domainName' => $domainName,
                'registrantUserId' => $registrantUserId,
                'authInfo' => $authInfo,
                'registrantContactId' => $contactIds['registrantContactId'] ?? '',
                'administratorContactId' => $contactIds['administratorContactId'] ?? '',
                'technicalContactId' => $contactIds['technicalContactId'] ?? '',
                'billingContactId' => $contactIds['billingContactId'] ?? '',
                'nameservers' => $nameservers,
            ];

            if (!empty($domainData['data']['premium'])) {
                $transferData['domainType'] = 'premium';
            }

            return $this->domainTransfer->submitRegistrarTransferIn($transferData);
        }

        // For 'reseller_transfer' or other types
        return $this->domainTransfer->submitResellerTransfer([
            'domainName' => $domainName,
            'registrantUserId' => $registrantUserId,
        ]);
    }

    /**
     * Checks pricing details for domain extensions.
     *
     * Depending on the specified type, this function fetches either the standard
     * pricing details or the promotional pricing details for the requested TLDs.
     *
     * @param string $priceType The type of price to retrieve. Accepted values:
     *                          - `standard`: Fetches standard pricing details.
     *                          - `promo`: Fetches promotional pricing details.
     * @param array $filtersData An array of filters to refine the pricing search.
     *                           - For standard pricing: Use `filters` and `pagination` keys.
     *                           - For promo pricing: Use `transtype` and `ext` keys.
     *
     * @return array The pricing details for the requested TLDs.
     *               For standard pricing: TLD price details.
     *               For promo pricing: Price details with effective period.
     */
    public function checkPrice(string $priceType, array $filtersData): array
    {
        if ($priceType === 'standard') {
            // Fetch standard pricing details
            return $this->domainProduct->getExtensionsPrice($filtersData);
        } elseif ($priceType === 'promo') {
            // Fetch promotional pricing details
            return $this->domainProduct->getExtensionsPromoPricing($filtersData);
        } else {
            throw new InvalidArgumentException("Invalid price type specified. Accepted values are 'standard' or 'promo'.");
        }
    }
}
