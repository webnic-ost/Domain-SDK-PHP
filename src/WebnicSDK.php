<?php

namespace Webnic\WebnicSDK;

use Webnic\WebnicSDK\Core\ApiConnector;
use Webnic\WebnicSDK\Services\Domain\{
    Action,
    Contact,
    Domain,
    DomainHost,
    DomainDnssec,
    DomainTransfer,
    PendingOrder,
    DomainProduct,
    Registrant,
    DomainActionLog,
    RegistryProgram,
    DomainBroker,
    SecondhandDomain
};
use Webnic\WebnicSDK\Services\DNS\{
    DNSSubscription,
    DNSSubscriptionWhitelabel,
    DNSZone,
    DNSZoneDNSSEC,
    DNSZoneForwarding,
    DNSZoneRecord,
    DNSZoneRecordTemplate
};

class WebnicSDK extends ApiConnector
{
    // Array to hold service instances
    protected array $services = [];

    public function __construct(string $clientId, string $clientSecret, array $config)
    {
        parent::__construct($clientId, $clientSecret, $config);

        // Define the services and their corresponding classes
        $serviceDomainClasses = [
            'action' => Action::class,
            'contact' => Contact::class,
            'domain' => Domain::class,
            'domainHost' => DomainHost::class,
            'domainDnssec' => DomainDnssec::class,
            'domainTransfer' => DomainTransfer::class,
            'pendingOrder' => PendingOrder::class,
            'domainProduct' => DomainProduct::class,
            'registrant' => Registrant::class,
            'domainActionLog' => DomainActionLog::class,
            'registryProgram' => RegistryProgram::class,
            'domainBroker' => DomainBroker::class,
            'secondhandDomain' => SecondhandDomain::class,
        ];
        $serviceDNSClasses = [
            'DNSSubscription' => DNSSubscription::class,
            'DNSSubscriptionWhitelabel' => DNSSubscriptionWhitelabel::class,
            'DNSZone' => DNSZone::class,
            'DNSZoneDNSSEC' => DNSZoneDNSSEC::class,
            'DNSZoneForwarding' => DNSZoneForwarding::class,
            'DNSZoneRecord' => DNSZoneRecord::class,
            'DNSZoneRecordTemplate' => DNSZoneRecordTemplate::class,
        ];

        // Initialize each service dynamically
        foreach ($serviceDomainClasses as $name => $class) {
            $this->services[$name] = new $class($clientId, $clientSecret, $config);
        }
        foreach ($serviceDNSClasses as $name => $class) {
            $this->services[$name] = new $class($clientId, $clientSecret, $config);
        }
    }

    // Optional: Create a method to access services
    public function __get($name)
    {
        return $this->services[$name] ?? null;
    }

    public function getAccountBalance()
    {
        $options = [];

        // Set query parameters if provided
        if (!empty($queryParams)) {
            $options['query'] = $queryParams;
        }

        $response = $this->request("GET", "{$this->baseUrl}/reseller/v2/balance", $options);
        return $response;
    }

    public function sendAsyncRequests(array $requests): array
    {
        return $this->asyncRequests($requests);
    }
}
