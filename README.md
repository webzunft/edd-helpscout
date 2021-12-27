EDD integration for Help Scout
=============

> **Changed Callback URL**
>
> As of version 2.0, the Callback URL in Help Scout should be `https://your-site.com/edd-helpscout-api/customer_info`.


Easy Digital Downloads integration for Help Scout is a WordPress plugin that will show customer information right from your Help Scout dashboard.

Activating the plugin and configuring the integration will add the following information to your Help Scout dashboard:

- The name of the customer and link to the profile page in EDD
- All payments by the customer (email address must match)
- A link to resent purchase receipts
- All purchased "downloads"
- The used payment method. Links to the transaction in PayPal or Stripe.

If using the Software Licensing add-on, the following information is shown as well:

- License keys. Links to the Site Manager in Easy Digital Downloads.
- Active sites, with a link to deactivate the license for the given site.


## Installation

To get this up an running, you'll need to configure a few things in WordPress and Help Scout.

#### WordPress

1. Upload the contents of **edd-helpscout.zip** to your plugins directory, which usually is `/wp-content/plugins/`.
1. Activate the **Help Scout integration for Easy Digital Downloads** plugin
1. Set the **HELPSCOUT_SECRET_KEY** constant in your `/wp-config.php` file. This should be a random string of 40 characters.

_Example_

```php
define( 'HELPSCOUT_SECRET_KEY', 'your-random-string-of-fourty-characters!' );
```

#### Help Scout

1. Go to the [Help Scout custom app interface](https://secure.helpscout.net/apps/custom/).
1. Enter the following settings.

| Setting     	| Value						                               	|
|--------------	|-------------------------------------------------------	|
| App Name     	| Easy Digital Downloads                                	|
| Content Type 	| Dynamic Content                                       	|
| Callback URL 	| https://your-site.com/edd-helpscout-api/customer_info 	|
| Secret Key   	| The value of your **HELPSCOUT_SECRET_KEY** constant.  	|

#### Testing the plugin locally.

You can set the plugin in some test mode.
Set `HELPSCOUT_DUMMY_DATA` to `true` and `HELPSCOUT_DUMMY_DATA_EMAIL` to an email address in `wp-config.php` to let the plugin use dummy data.
You can then call https://your-site.com/edd-helpscout-api/customer_info directly and get a reply based on the value of `HELPSCOUT_DUMMY_DATA_EMAIL`.
