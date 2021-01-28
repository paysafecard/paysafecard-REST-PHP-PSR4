# paysafecard PSR-4 PHP SDK for REST
A official PSR-4 SDK implementation for accessing the paysafecard REST API, with the ability to handle payments, payouts
and refunds.

This SDK a drop-in replacement for the legacy, non-PSR-4 PHP SDK. It only requires changes to class names and namespace,
the methods of the classes are further backwards compatible.

API documentation is available at: https://www.paysafecard.com/fileadmin/api/

## Installation
This library makes use of composer. Load it into your project as follows:
```
composer require paysafecard/paysafecard-rest-php-psr4
```

## Integration
To use the paysafecard PHP SDK, you need to be a paysafecard partner. Request this through the paysafecard website.

Once registered, configure the examples or integrate this library in your project. When integrated, finish the 
integration test in the paysafecard service center and wait for acceptance of your environment.

### Payments
Payments are handled through the class `paysafecard\paysafecardSDK\Payments`. See the doctype for further documentation.
There is a working implementation of all methods in the `examples/payments` folder, with a sample integration.

### Payouts
Payouts are handled through the class `paysafecard\paysafecardSDK\Payouts`. See the doctype for further documentation.
There is a working implementation of all methods in the `examples/payouts` folder, with a sample integration.

### Refunds
Refunds are handled through the class `paysafecard\paysafecardSDK\Refunds`. See the doctype for further documentation.
There is a working implementation of all methods in the `examples/refunds` folder, with a sample integration.

### Examples
If you want to use the examples provided in the library, clone this project into a PHP-powered webserver and install
the dependencies:
```
composer install
```

After that, change the config file in `examples/config.php` and load up the `examples/payments/`-path in a browser.