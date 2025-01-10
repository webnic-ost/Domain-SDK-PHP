## PHP SDK for Webnic RESTful API Integration Guide

Welcome to the SDK Documentation! Below is a step-by-step guide on how to integrate the Webnic SDK into your PHP project.

# Step 1: Install the SDK

Clone the SDK repository and install the necessary dependencies using Composer:

```bash
git clone https://github.com/webnic-ost/Domain-SDK-PHP.git
cd webnic-domain-sdk-php
composer install
```

# Step 2: Move SDK Files to Your Project

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

# Step 3: Require the SDK in Your Project

In your PHP project, require the autoload.php file from the vendor folder:

```php
require_once **DIR** . '/webnicSDK/vendor/autoload.php';
```

Then, use the SDK classes in your project:

```php
use Webnic\WebnicSDK\WebnicSDK;
```

# Step 4: Initialize the SDK

You can now initialize the SDK by passing your Webnic credentials and API configuration:

```php
$webnicSDK = new WebnicSDK("api-username", "api-secret", [
'tokenEndpoint' => 'https://oteapi.webnic.cc/reseller/v2/api-user/token',
'baseUrl' => "https://oteapi.webnic.cc",
'apiVersion' => '/v2',
]);
```

# Step 5: Use the SDK

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

# Step 6: Test the Integration

Now, run your PHP project to ensure the SDK is properly integrated and working. If everything is set up correctly, you should be able to interact with the Webnic API and retrieve data.
