<?php

namespace Webnic\WebnicSDK\Services\Domain;

use Webnic\WebnicSDK\Core\ApiConnector;


class RegistryProgram extends ApiConnector
{

    public function __construct(string $clientId, string $clientSecret, array $config)
    {
        parent::__construct($clientId, $clientSecret, $config);

        $this->serviceUrl = '/domain' . $this->apiVersion;
    }

    /**
     * Allows holders of eligible English .hk domains to apply for a free equivalent CDN.香港 domain, or vice versa.
     *
     * This function enables domain holders to bundle an eligible English .hk domain with a free equivalent CDN.香港 domain (or the reverse),
     * subject to availability. Both domains must be registered under the same WebNIC reseller account and will be renewed and transferred together.
     * The bundling process is offered on a first-come, first-serve basis.
     *
     * @param string $domainName The domain name eligible for bundling (e.g., an English .hk domain).
     * @param string $bundleDomainName The corresponding bundle domain name (e.g., the equivalent CDN.香港 domain).
     *
     * @return array The API response containing the result of the bundling request:
     *   - `status` (string): The status of the bundling request, such as "success" or "error". 
     *   - `message` (string): Additional comments or feedback regarding the bundling request.
     *
     * Example request:
     * ```php
     * $domainName = 'example.hk';
     * $bundleDomainName = 'example.香港';
     * $response = $webnicSDK->domainTransfer->bundleHKDomain($domainName, $bundleDomainName);
     * ```
     */

    public function bundleHKDomain(string $domainName, string $bundleDomainName): array
    {
        return $this->sendRequest('POST', '/registry-program/bundle-hk-domain', ['domainName' => $domainName, "bundleDomainName" => $bundleDomainName]);
    }

    /**
     * Verifies if a newly established company in Taiwan qualifies for the TWNIC Free Registration Program.
     *
     * This function checks whether a company is eligible for the TWNIC Free Registration Program, which offers a free
     * .tw or .台灣 domain registration for one year. To qualify, the company must meet specific criteria as defined by TWNIC.
     * The check is based on the provided UBN ID, company name, and domain name.
     *
     * @param string $ubnId The UBN ID (Unified Business Number) of the company. This is a required identifier for businesses in Taiwan.
     * @param string $companyName The name of the company applying for the domain registration.
     * @param string $domainName The domain name the company intends to register (e.g., "company.tw" or "company.台灣").
     *
     * @return array The API response containing the eligibility result:
     *   - `data` (string): The eligibility result message, which indicates whether the company qualifies for the TWNIC Free Registration Program.
     *
     * Example request:
     * ```php
     * $ubnId = '1234567890';
     * $companyName = 'Example Company';
     * $domainName = 'example.tw';
     * $response = $webnicSDK->domainTransfer->checkFreeTWDomainEligible($ubnId, $companyName, $domainName);
     * ```
     */

    public function checkFreeTWDomainEligible(string $ubnId, string $companyName, string $domainName): array
    {
        return $this->sendRequest('POST', '/registry-program/check-free-tw-domain-eligibility', ['ubnId' => $ubnId, "companyName" => $companyName, "domainName" => $domainName]);
    }
}
