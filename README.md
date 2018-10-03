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

### Finding your Paypal Express Credentials

1) Log in to your PayPal Seller Account
2) In the top menu bar click "Profile" and choose "Profile and Settings"
3) Click "My Selling Preferences"
4) Click "API Access" (click "update")
5) You want the the NVP/SOAP API Integration (Classic) section
6) Then go to "Manage API credentials"
7) If you don't have any credentials already then generate some.

Matching the different bits of info up to the fields within the gateway setup can be tricky as sometimes the labels change. This table should help.

| Gateway label         | Sandbox Account/Account					|
| ----------------------|---------------------------------------------------------------|
| API Username		| Sandbox Account/Account					|
| API Password		| Client ID							|
| API Signature		| Secret (click 'Hide' to show it - totally logical)		|
| Solution Type		| Mark							        |
| Landing Page		| Determines the type of form the user gets at PayPal	        |

In the gateway settings there is also a dropdown for "Solution Type". We *think* it may be the difference between personal and business PayPal accounts. In my case “Mark” worked, “Solo” didn’t.

"Landing Page" controls the type of form that is shown when the customer gets directed to PayPal. 
Selecting "Billing" will show a set of Credit Card fields with an option to login to PayPal (from memory this is a bit hidden).
Selecting "Login" presents a PayPal login form without any credit card fields.

Brand Name, Header Image URL, Logo Image URL, and Border Colour are all customisation options for your landing page. Use the full URL to your image assets, including the domain.

### Important
If you're going to use the PayPal Express payment gateway you are required to change the default value of ```tokenParam``` in your
[Craft config](https://docs.craftcms.com/api/v3/craft-config-generalconfig.html#$tokenParam-detail)

Choose any different token name other than ```token```, for example you could put ```craftToken```. Otherwise redirects from PayPal will fail.
