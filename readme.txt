=== DPS PxPay for WP eCommerce ===
Contributors: webaware
Plugin Name: DPS PxPay for WP eCommerce
Plugin URI: http://shop.webaware.com.au/downloads/dps-pxpay-for-wp-ecommerce/
Author URI: http://webaware.com.au/
Donate link: https://shop.webaware.com.au/donations/?donation_for=DPS+PxPay+for+WP+eCommerce
Tags: dps, payment express, pxpay, wp ecommerce
Requires at least: 4.3
Tested up to: 4.6
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Integrate DPS PxPay with the WP eCommerce online shop

== Description ==

DPS PxPay for WP eCommerce adds a credit card payment gateway for [DPS Payment Express PxPay](https://www.paymentexpress.com/merchant-ecommerce-pxpay) to the [WP eCommerce](https://wordpress.org/plugins/wp-e-commerce/) shopping cart plugin.

= Sponsorships =

* creation of this plugin was generously sponsored by Nick Sundberg.

Thanks for sponsoring new features on DPS PxPay for WP eCommerce!

= Requirements: =

* Install the [WP eCommerce](https://wordpress.org/plugins/wp-e-commerce/) shopping cart plugin
* Create an account with DPS for [PxPay](https://sec.paymentexpress.com/pxmi/apply)

== Installation ==

After uploading and activating this plugin, you need to configure it.

1. Navigate to `Settings > Store > Payments` in the admin menu
2. Activate the DPS Payment Express PxPay payment gateway and click the Update button
3. Edit the DPS Payment Express PxPay payment gateway settings
4. Set your DPS PxPay user ID and key
5. Set your Sandbox account details if using a sandbox
6. Optionally, map some fields from the checkout to PxPay fields so that they get passed to the gateway

== Frequently Asked Questions ==

= What is DPS PxPay? =

DPS PxPay is a hosted Credit Card payment gateway. DPS Payment Express is one of Australasia's leading online payments solutions providers.

= What is the difference between Normal and Sandbox (testing) mode? =

You can store two pairs of User ID and User Key credentials. When you first signup for a PxPay account with DPS you will likely be issued development or testing credentials. Later, when you want to go live with your site, you will need to request a new User ID and User Key from DPS. Sandbox mode enables you to switch between your live and test credentials. If you only have testing credentials, both your User ID and Test ID and User Key and Test Key should be identical. In this instance, Sandbox mode can be switched either On or Off.

= Where do I find the DPS PxPay transaction number? =

The transaction number and the bank authcode are shown under Billing Details when you view the sales log for a purchase in the WordPress admin.

= Can I do recurring payments? =

Not yet.

=  Where can I find dummy Credit Card details for testing purposes? =

[Visit this page](https://www.paymentexpress.com/support-merchant-frequently-asked-questions-testing-details)

= I get an SSL error when my form attempts to connect with DPS =

This is a common problem in local testing environments. Please [read this post](https://snippets.webaware.com.au/howto/stop-turning-off-curlopt_ssl_verifypeer-and-fix-your-php-config/) for more information.

= Can I use this plugin on any shared-hosting environment? =

The plugin will run in shared hosting environments, but requires PHP 5 with the following modules enabled (talk to your host). Both are typically available because they are enabled by default in PHP 5, but may be disabled on some shared hosts.

* XMLWriter
* SimpleXML

== Screenshots ==

1. WP eCommerce payments settings
2. WP eCommerce Sales Log with transaction ID and authcode

== Filter hooks ==

Developers can use these filter hooks to modify some invoice properties. Check the source code for details.

* `dps_pxpay_wpsc_invoice_ref` for modifying the invoice reference
* `dps_pxpay_wpsc_invoice_txndata1` for setting the TxnData1 field
* `dps_pxpay_wpsc_invoice_txndata2` for setting the TxnData2 field
* `dps_pxpay_wpsc_invoice_txndata3` for setting the TxnData3 field
* `dps_pxpay_wpsc_code_description` for modifying the error messages returned from DPS Payment Express on failed access attempts

== Contributions ==

* [Fork me on GitHub](https://github.com/webaware/dps-pxpay-for-wp-ecommerce)

== Changelog ==

### 1.0.0, 2016-08-20

* initial release
