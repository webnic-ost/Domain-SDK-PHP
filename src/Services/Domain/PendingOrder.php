<?php

namespace Webnic\WebnicSDK\Services\Domain;

use Webnic\WebnicSDK\Core\ApiConnector;


class PendingOrder extends ApiConnector
{

    public function __construct(string $clientId, string $clientSecret, array $config)
    {
        parent::__construct($clientId, $clientSecret, $config);

        $this->serviceUrl = '/domain' . $this->apiVersion;
    }

    /**
     * Get pending order info.
     *
     * This function retrieves detailed information about a specific pending order using its ID.
     *
     * @param string $id The ID of the pending order to retrieve.
     *
     * @return array The API response containing details of the pending order:
     *   - `ext` (string): The extension of the domain related to the pending order.
     *   - `dtcreate` (string): The date and time when the pending order was created.
     *   - `domain` (string): The domain name associated with the pending order.
     *   - `dtmodify` (string): The date and time when the pending order was last modified.
     *   - `details` (object): Detailed information about the pending order.
     *   - `comment` (string): Additional comments or remarks about the pending order.
     *   - `type` (string): The type of the pending order.
     *   - `resid` (number): The reseller ID associated with the pending order.
     *   - `status` (string): The current status of the pending order.
     *
     * Example request:
     * ```php
     * $id = '12345';
     * $response = $webnicSDK->domainTransfer->getPendingOrderInfo($id);
     * ```
     */

    public function getPendingOrderInfo(string $id): array
    {
        return $this->sendRequest('GET', "/order/info", ['id' => $id]);
    }
}
