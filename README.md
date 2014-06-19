Easy Digital Downloads integration for HelpScout
=============

Easy Digital Downloads integration for HelpScout is a WordPress plugin that will show customer information from Easy Digital Downloads right form your HelpScout dashboard.

Activating the plugin and configuring the integration will add the following information to your HelpScout dashboard:

- All payments by the customer (email addresses or first- and lastname must match)
- A link to resent purchase receipts
- All purchased "downloads"
- The payment method used. Links to the transaction in PayPal or Stripe.
- License keys, if any. Links to the Site Manager in Easy Digital Downloads.


### Installation

To get this up an running, you'll need to configure a few things in both WordPress as HelpScout.

##### WordPress
1. Upload the contents of `edd-helpscout.zip` to your plugins directory, which usually is `/wp-content/plugins/`.
1. Activate the `Easy Digital Downloads integration for HelpScout` plugin
1. Set the `HELPSCOUT_SECRET_KEY` constant in your `wp-config.php` file. This should be a random string of 40 characters.

##### HelpScout

1. Go to the [HelpScout custom app interface](https://secure.helpscout.net/apps/custom/).
1. Set the App Name to `Easy Digital Downloads` and set the **Content Type** to *Dynamic Content*.
1. Enter your WordPress Site URL as the Callback Url. The plugin will automatically hijack HelpScout requests to this URL.
1. Enter the `HELPSCOUT_SECRET_KEY` constant value in the Secret Key field.
