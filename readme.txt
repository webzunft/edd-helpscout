=== Easy Digital Downloads - HelpScout integration ===
Contributors: DvanKooten
Donate link: https://dannyvankooten.com/donate/
Tags: easy-digital-downloads,helpscout,edd,support
Requires at least: 3.8
Tested up to: 4.1
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easy Digital Downloads integration for HelpScout. Show purchase information right from your HelpScout interface.

== Description ==

Easy Digital Downloads integration for HelpScout. Show purchase information right from your HelpScout interface.

== Installation ==

= Installing the plugin =
1. Upload the contents of `edd-helpscout.zip` to your plugins directory, which usually is `/wp-content/plugins/`.
1. Activate the plugin
1. Set your HelpScout secret using the `HELPSCOUT_SECRET_KEY` PHP constant

== Changelog ==

= 1.0.1 =

**Fixed**

- Issue with nonces not working properly for the admin actions. Now using the HelpScout signature to validate requests.

**Improvements**

- Minor code & inline documentation improvements

**Additions**

- Added "renewal" label to renewals

= 1.0 =
Initial release.


