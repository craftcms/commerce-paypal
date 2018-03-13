PayPal payment gateways plugin for Craft Commerce 2
=======================

This plugin provides [PayPal](https://www.paypal.com/) integrations for [Craft Commerce](https://craftcommerce.com/).

It provides PayPal Pro, PayPal Express Checkout and PayPal REST gateways. Credit card payments with the REST gateway are supported only in the UK and US.

## Requirements

This plugin requires Craft Commerce 2.0.0-alpha.5 or later.


## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require craftcms/commerce-paypal

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for PayPal.

## Setup

To add a PayPal payment gateway, go to Commerce → Settings → Gateways, create a new gateway, and set the gateway type to either “PayPal Pro”, “PayPal REST” or “PayPal Express”.

### Important
If you're going to use the PayPal Express payment gateway you are required to change the default value of ```tokenParam``` in your
[Craft config](https://docs.craftcms.com/api/v3/craft-config-generalconfig.html#$tokenParam-detail)

Choose any different token name other than ```token```, for example you could put ```craftToken```. Otherwise redirects from PayPal will fail.
