<?php

namespace Webnic\WebnicSDK\Services\Domain;

use Webnic\WebnicSDK\Core\ApiConnector;


class DomainActionLog extends ApiConnector
{



    public function __construct(string $clientId, string $clientSecret, array $config)
    {
        parent::__construct($clientId, $clientSecret, $config);

        // Initialize the serviceUrl based on apiVersion
        $this->serviceUrl = '/domain' .  $this->apiVersion;
    }

    /**
     * Get domain action log info.
     *
     * This function allows you to retrieve the domain action log details based on the provided trace ID. The log contains information about domain actions such as modification, restoration, transfer, etc.
     *
     * @param string $traceId The trace ID associated with the domain action log.
     *
     * @return array The response data, including:
     *   - `traceId` (string): Reference trace ID for the domain action log.
     *   - `action` (string): Action performed in the domain action log. Possible values include "DOM_RERTRF_IN_DONE", "DOM_RERTRF_REQ", "DOM_RERTRF_REQ_RESTRF", etc.
     *   - `domain` (string): Domain associated with the action.
     *   - `ext` (string): Extension of the domain.
     *   - `response` (object): Response details for the action.
     *   - `dtcreate` (string): Date and time when the domain log was created.
     *   - `beforeContent` (object): Content before the action.
     *   - `afterContent` (object): Content after the action.
     *
     * Example request:
     * ```php
     * $traceId = "111111111111111";
     * $response = $webnicSDK->domainActionLog->getInfo($traceId);
     * ```
     */
    public function getInfo(string $traceId): array
    {
        return $this->sendRequest('GET', '/log/info', ['traceId' => $traceId]);
    }
}
