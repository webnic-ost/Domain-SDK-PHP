# Webnic SDK

A PHP SDK for interacting with the **Webnic Domain RESTful API**. This SDK provides simple access to domain and contact operations through PHP.

---

## Features

- **Domain Management**: Register, renew, and manage domains.
- **DNS Management**: Manage DNS-related functions.
- **Contact Management**: Create and manage domain-related contacts.
- **Guzzle Integration**: Handles HTTP requests using Guzzle.
- **PSR-4 Autoloading**: Compatible with modern PHP frameworks.

---

## Installation and Setup

### Prerequisites

Ensure you have:

- **PHP** version 7.4 to 8.2
- **Composer** (PHP dependency manager)
- **Git** (for cloning the repository)

### Step-by-Step Guide

1. **Install the SDK**

Clone the SDK repository and install the necessary dependencies using Composer:

```bash
git clone https://github.com/webnic-ost/Domain-SDK-PHP.git
cd webnic-domain-sdk-php
composer install
```

2. **Move SDK Files to Your Project**

Once Composer has installed the dependencies, move the necessary files into your project directory. You will need the following:

```bash
src folder
vendor folder
composer.json
composer.lock
```

For example, move these files to a folder called webnicSDK in your project’s library folder:

```bash
mv src vendor composer.json composer.lock /path/to/your/project/lib/webnicSDK
```

3. **Require the SDK in Your Project**

In your PHP project, require the autoload.php file from the vendor folder:

```php
require_once **DIR** . '/webnicSDK/vendor/autoload.php';
```

Then, use the SDK classes in your project:

```php
use Webnic\WebnicSDK\WebnicSDK;
```

4. **Initialize the SDK**

You can now initialize the SDK by passing your Webnic credentials and API configuration:

```php
$webnicSDK = new WebnicSDK("api-username", "api-secret", [
'tokenEndpoint' => 'https://oteapi.webnic.cc/reseller/v2/api-user/token',
'baseUrl' => "https://oteapi.webnic.cc",
'apiVersion' => '/v2',
]);
```

5. **Use the SDK**

To use the SDK, create an instance and call its methods. For example, to get domain information:

```php
$sdkInstance = new WebnicSDK("api-username", "api-secret", [
'tokenEndpoint' => 'https://oteapi.webnic.cc/reseller/v2/api-user/token',
'baseUrl' => "https://oteapi.webnic.cc",
'apiVersion' => '/v2',
]);

$domainName = "testing.test";
$response = $sdkInstance->domain->getDomainInfo($domainName);
```

6. **Test the Integration**

Now, run your PHP project to ensure the SDK is properly integrated and working. If everything is set up correctly, you should be able to interact with the Webnic API and retrieve data.

The documentation is located in the documentation.html file in the root folder. It uses Doxygen for better explanations.
