=== Help Scout integration for Easy Digital Downloads ===
Contributors: webzunft, DvanKooten, Ibericode
Tags: easy-digital-downloads,helpscout,edd,support,help scout
Requires at least: 3.8
Tested up to: 5.8
Requires PHP: 5.6
Stable tag: 2.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easy Digital Downloads integration for Help Scout. Shows purchase information right from your Help Scout interface.

== Description ==

Help Scout integration for Easy Digital Downloads is a WordPress plugin that will show customer information right from your Help Scout dashboard.

Activating the plugin and configuring the integration will add the following information to your Help Scout dashboard:

- The name of the customer and link to the profile page in EDD
- All purchased "downloads"
- Orders with status and link to payment providers

If using the Software Licensing add-on, the following information is shown as well:

- License keys. Links to the site manager in Easy Digital Downloads.
- Active sites, with a link to deactivate the license for the given site.
- Links to purchase an upgrade directly

When using Easy Digital Downloads with Recurring Payments:

- Subscriptions and their status

**How to install and configure**

Have a look at the [installation instructions](https://wordpress.org/plugins/edd-helpscout/installation/).

> Please note that this plugin requires PHP 5.3 or higher.

**More information**

- Developers; follow or contribute to the [plugin on GitHub](https://github.com/webzunft/edd-helpscout)
- Other [WordPress plugins](https://profiles.wordpress.org/webzunft/#content-plugins) by Thomas Maier

== Installation ==

To get this up an running, you'll need to configure a few things in WordPress and Help Scout.

= WordPress =

1. Upload the contents of **edd-helpscout.zip** to your plugins directory, which usually is `/wp-content/plugins/`.
1. Activate the **Help Scout integration for Easy Digital Downloads** plugin
1. Set the **HELPSCOUT_SECRET_KEY** constant in your `/wp-config.php` file. This should be a random string of 40 characters.


_Example_

Replace `your-random-string` with your own random string.

`
define( 'HELPSCOUT_SECRET_KEY', 'your-random-string' );
`

= Help Scout =

1. Go to the [Help Scout custom app interface](https://secure.helpscout.net/apps/custom/).
1. Enter the following settings with adjustments according to your site’s URL and the secret key.

**App Name:** Easy Digital Downloads<br />
**Content Type:** Dynamic Content<br />
**Callback URL:** https://YOUR-SITE.COM/edd-helpscout-api/customer_info _(I recommend using HTTPS)_ <br />
**Secret Key:** The value of your **HELPSCOUT_SECRET_KEY** constant.

= Testing the plugin locally =

You can set the plugin in some test mode.
Set `HELPSCOUT_DUMMY_DATA` to `true` and `HELPSCOUT_DUMMY_DATA_EMAIL` to an email address in `wp-config.php` to let the plugin use dummy data.
You can then call https://your-site.com/edd-helpscout-api/customer_info directly and get a reply based on the value of `HELPSCOUT_DUMMY_DATA_EMAIL`.

== Frequently Asked Questions ==

= Help Scout just shows "Invalid Signature" =

Make sure the "Secret Key" setting for your Help Scout application matches the value of your `HELPSCOUT_SECRET_KEY` constant. This key is used to authorize requests coming from HelpScout.

== Screenshots ==

1. Customer overview with Licenses area opened by default
2. Orders with status, links to the store and payment provider
3. Subscriptions with status and links to the store

== Changelog ==

= 2.2.0 =

- Feature: the toggle state for Licenses, Orders, and Subscriptons sections is now persistent, props @nosilver4u on github
- Feature: display the number of items found for each section in the heading, props @nosilver4u on github
- Improvement: show "Lifetime" label for lifetime licenses, props @nosilver4u on github
- Improvement: override section templates by hosting your own version of them under `wp-content/themes/{THEME}/edd-helpscout/`, props @Spreeuw on github
- Improvement: added the `edd_helpscout_endpoint_html_sections` and `edd_helpscout_endpoint_html` filters to customize existing section templates, props @Spreeuw on github
- Improvement: added the `edd_helpscout_order_list_item_download_details_start` and `edd_helpscout_order_list_item_download_details_end` actions to order template, props @Spreeuw on github
- Improvement: added the `edd_helpscout_default_section_toggle` and `edd_helpscout_persist_section_toggle` filters to force a specific toggle state for sections, props @Spreeuw on github
- Improvement: increased required PHP version to 5.6 to sync with WordPress core

= 2.1.1 =

- Improvement: check if emails field is empty and handle fallback to email field

= 2.1 =

- complete template refactoring, e.g., to show Customer, Licenses, Orders and Subscriptions separately, props @Spreeuw
- refactor data collection, props @Spreeuw
- support EDD 3.0 and newer add-on versions, props @ashleyfae, @Spreeuw
- edd_helpscout_customer_licenses to filter license query response, props @Spreeuw
- changed "HelpScout" to "Help Scout" where technically reasonable

= 2.0 =

The original developer Danny van Kooten stopped working on EDD Help Scout since he no longer uses it.
The development of the plugin was taken over by Thomas Maier from https://wpadvancedads.com, who continues using the plugin.
Danny left in the middle of developing a better version 2.0. I decided to build in top of that because the changes show a lot potential and fixed some issues.
Please test carefully and let me know in case something is missing or not working as expected.

**Additions**

- callback URL changed from https://your-site.com/edd-helpscout/api to https://your-site.com/edd-helpscout-api/customer_info, though both are working for now
- added name of the customer and link to the profile page in EDD since Help Scout doesn‘t show it for everyone
- set `HELPSCOUT_DUMMY_DATA` and `HELPSCOUT_DUMMY_DATA_EMAIL` constants in `wp-config.php` to let the plugin use dummy data

**Improvements**

- fetch all payments when user has multiple emails in Help Scout or in his EDD profile

**Fixes**

- Compatibility with latest EDD plugin versions.

= 1.1.1 - January 28, 2016 =

**Fixes**

- Certain characters at start of URL were being stripped off in HelpScout.

= 1.1 - September 6, 2015 =

**Fixes**

- The plugin is now listening at a later hook in the WP request lifecycle, which prevents issues with bbPress and EDD Wishlists.

**Improvements**

- Code refactoring for better separation of concerns and better overall code readability
- Better naming consistency

**Additions**

- Support for lifetime licenses in Easy Digital Downloads
- Various action hooks to output your own HTML

= 1.0.3 - February 19, 2015 =

**Fixes**

- Added protocol for links to active sites
- Querying payments by multiple emails was not working

**Additions**

- When using EDD Software Licensing, show if a license is expired.
- Added `helpscout_edd_customer_emails` hook to filter customer emails

= 1.0.2 =

**Improvements**

- The plugin used to "listen" to all requests to the site. It will now (after confirmation) only listen to requests to `/edd-hs-api/customer-data.json`.

= 1.0.1 =

**Fixed**

- Issue with nonces not working properly for the admin actions. Now using the HelpScout signature to validate requests.

**Improvements**

- Minor code & inline documentation improvements

**Additions**

- Added "renewal" label to renewals
