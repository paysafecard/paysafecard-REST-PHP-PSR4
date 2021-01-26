# paysafecard PSR-4 PHP SDK for REST
A PSR-4 SDK implementation for accessing the paysafecard REST API, with the ability to handle payments, payouts and
refunds.

This SDK a drop-in replacement for the legacy, non-PSR-4 PHP SDK. It only requires to changes some classes, the methods
further exactly the same.

## Payments
Payments are handled through `paysafecard\paysafecardSDK\Payments`. See the doctype for further documentation. There is
a working implementation of all methods in the `examples/payments` folder, with a sample integration.

## Payouts
Payouts are handled through `paysafecard\paysafecardSDK\Payouts`. See the doctype for further documentation. There is
a working implementation of all methods in the `examples/payouts` folder, with a sample integration.

## Refunds
Refunds are handled through `paysafecard\paysafecardSDK\Refunds`. See the doctype for further documentation. There is
a working implementation of all methods in the `examples/refunds` folder, with a sample integration.