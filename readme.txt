=== Order Tip for WooCommerce ===
Contributors: railmedia, mhlkpf
Tags: Woocommerce, Ecommerce, Order, Tip, Donation
Requires at least: 3.0
Stable tag: 1.1.1
Tested up to: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Order Tip for WooCommerce adds a form to your cart and checkout pages where your customers will be able to add tips or donations

== Description ==

Order Tip for WooCommerce is a plugin that allows customers to add a tip or donation to a WooCommerce order. The tip is added under the form of a WooCommerce fee.

It allows the tip to be a percentage of the order total or a fixed custom amount. Cash tip is also available which marks the tip as 0 in value, but you should expect a tip on the delivery of your products or on the pickup of the order by the customer.

There is also an option for adding a custom tip which brings up a text field where the customer is able to type in a custom amount and which is subsequently added as a fixed amount to the order.

The tip can also be set to be taxed or not as per your current Tax options set in WooCommerce. It features 6 standard tip rates (5, 10, 15, 20, 25, 30) that can be extended through a filter - see below under the Developers section.

It features various configuration options in the WooCommerce Settings panel under the tab Order Tip.

The plugin's backend is translated in German, Swiss German, Spanish, French, Italian, Romanian and partially in Dutch.

= Check out a demo here: =

[Live Preview](https://order-tip-for-woocommerce.tudorache.me/)

= Check out a video on installing and using the plugin =

[youtube https://www.youtube.com/watch?v=9CskEO7oQV8]

= Important Notes =

The plugin works out of the box, with no coding skills required on basically any theme. However, it uses JavaScript for adding the tip to the order. If for some reason it doesn't work as expected, please check your browser's console for any JS errors or drop a line here in the Support tab providing a link to your website.

Websites using the Astra or Neve theme should avoid using the "After customer details position" to display the tip form. It may break the layout causing the order review sidebar to fall under the customer details one.

= Developers =

There are a couple of filters you can hook into should you need to extend or edit the core functionality:

wc_order_tip_title - takes in 1 string variable which holds the title of the form which appears before the form;

wc_order_tip_rates - takes in 1 array variable which holds the values of the predefined standard tip rates. You should return a simple array containing the values you wish to add. Eg: array( 10, 15, 30 ).

== Installation ==

1. Upload and activate plugin in your WP installation
2. Go to WooCommerce -> Settings -> Order Tip
3. Configure the plugin and save the settings
4. Check the frontend cart page and checkout page

== Screenshots ==

1. Admin settings

2. Frontend Cart Page

3. Frontend Checkout Page

4. Custom tip

5. Frontend Thank You page

6. Backend Order displaying tip
