EDD integration for HelpScout
=============

Easy Digital Downloads integration for HelpScout is a WordPress plugin that will show customer information right from your HelpScout dashboard.

Activating the plugin and configuring the integration will add the following information to your HelpScout dashboard:

- All payments by the customer (email address must match)
- A link to resent purchase receipts
- All purchased "downloads"
- The used payment method. Links to the transaction in PayPal or Stripe.

If using the Software Licensing add-on, the following information is shown as well:

- License keys. Links to the Site Manager in Easy Digital Downloads.
- Active sites, with a link to deactivate the license for the given site.


### Installation

To get this up an running, you'll need to configure a few things in WordPress and HelpScout.

#### WordPress

1. Upload the contents of **edd-helpscout.zip** to your plugins directory, which usually is `/wp-content/plugins/`.
1. Activate the **HelpScout integration for Easy Digital Downloads** plugin
1. Set the **HELPSCOUT_SECRET_KEY** constant in your `/wp-config.php` file. This should be a random string of 40 characters.


_Example_

`
define( 'HELPSCOUT_SECRET_KEY', 'ueCQWKbZ48BT6UGmCFbaqXtbLaDZu1v6rnBLZjKD' );
`

#### HelpScout

1. Go to the [HelpScout custom app interface](https://secure.helpscout.net/apps/custom/).
1. Enter the following settings.

**App Name:** Easy Digital Downloads<br />
**Content Type:** Dynamic Content<br />
**Callback URL:** https://your-site.com/edd-hs-api/customer-data.json _(I recommend using HTTPS)_ <br />
**Secret Key:** The value of your **HELPSCOUT_SECRET_KEY** constant.