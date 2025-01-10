<?php

namespace Webnic\WebnicSDK\Services\Domain;

use Webnic\WebnicSDK\Core\ApiConnector;


class SecondhandDomain extends ApiConnector
{



    public function __construct(string $clientId, string $clientSecret, array $config)
    {
        parent::__construct($clientId, $clientSecret, $config);

        // Initialize the serviceUrl based on apiVersion
        $this->serviceUrl = '/domain' . $this->apiVersion;
    }

    /**
     * Inserts a new secondhand domain with the specified details.
     *
     * @param array $secondhandDomainData An array containing the details of the secondhand domain to insert.
     * 
     * Example Input:
     * ```php
     * [
     *     {
     *         "domainName": "jimtestmodifyns.com", // The name of the secondhand domain to insert.
     *         "regid": "WNC968080T", // The registrant ID for the domain.
     *         "admid": "WNC968081T", // The administrator ID for the domain.
     *         "tecid": "WNC968082T", // The technical contact ID for the domain.
     *         "bilid": "WNC968083T", // The billing contact ID for the domain.
     *         "nameservers": [ // An array of nameservers associated with the domain.
     *             "ns3.web.cc",
     *             "ns5.web.cc"
     *         ]
     *     }
     * ]
     * ```
     * 
     * @return array The response from the API after attempting to insert the secondhand domain.
     */
    public function insert(array $secondhandDomainData): array
    {
        $body = $secondhandDomainData;
        return $this->sendRequest('POST', '/secondhand-domain/insert', [], $body);
    }
}
