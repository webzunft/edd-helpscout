Easy Digital Downloads integration for HelpScout
=============

Easy Digital Downloads integration for HelpScout is a WordPress plugin that will show customer information from Easy Digital Downloads right form your HelpScout dashboard.

### Installation

#### WordPress
1. Upload the contents of `edd-helpscout.zip` to your plugins directory, which usually is `/wp-content/plugins/`.
1. Activate the `Easy Digital Downloads integration for HelpScout` plugin
1. Set your HelpScout secret in your `wp-config.php` using the `HELPSCOUT_SECRET_KEY` PHP constant (this should be a 40 character random string). 

#### HelpScout

1. Go to the [HelpScout custom app interface](https://secure.helpscout.net/apps/custom/).
1. Set the App Name to `Easy Digital Downloads` and set the Content Type to Dynamic Content.
1. Enter your WordPress Site URL. The plugin will automatically hijack HelpScout requests to this URL.
1. Enter your `HELPSCOUT_SECRET_KEY` constant value
