=== Order Tip for WooCommerce ===
Contributors: railmedia
Tags: Woocommerce, Ecommerce, Order, Tip, Donation
Requires at least: 3.0
Stable tag: 1.5.3
Tested up to: 6.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Order Tip for WooCommerce adds a form to your cart and checkout pages where your customers will be able to add tips or donations

== Description ==

Order Tip for WooCommerce is a plugin that allows customers to add a tip or donation to a WooCommerce order. The tip is added under the form of a WooCommerce fee.

It allows the tip to be a percentage of the order total or a fixed custom amount. Cash tip is also available which marks the tip as 0 in value, but you should expect a tip on the delivery of your products or on the pickup of the order by the customer.

There is also an option for adding a custom tip which brings up a text field where the customer is able to type in a custom amount and which is subsequently added as a fixed amount to the order.

The tip can also be set to be taxed or not as per your current Tax options set in WooCommerce. It features 6 standard tip rates (5, 10, 15, 20, 25, 30) that can be extended through a filter - see below under the Developers section.

It features various configuration options in the WooCommerce Settings panel under the tab Order Tip.

The plugin's backend is translated in German, Swiss German, Spanish, French, Italian, Romanian.

Dutch language support was added, thanks to Roel Mehlkopf (@mhlkpf).

= Check out a demo here: =

[Live Preview](https://order-tip-for-woocommerce.tudorache.me/)

= Check out a video on installing and using the plugin =

[youtube https://www.youtube.com/watch?v=9CskEO7oQV8]

= Important Notes =

The plugin works out of the box, with no coding skills required on basically any theme. However, it uses JavaScript for adding the tip to the order. If for some reason it doesn't work as expected, please check your browser's console for any JS errors or drop a line here in the Support tab providing a link to your website.

Websites using the Astra or Neve theme should avoid using the "After customer details position" to display the tip form. It may break the layout causing the order review sidebar to fall under the customer details one.

= Developers =

There are a couple of filters you can hook into should you need to extend or edit the core functionality:

* wc_order_tip_title - takes in 1 string variable which holds the title of the form which appears before the form;
* wc_order_tip_rates - takes in 1 array variable which holds the values of the predefined standard tip rates. You should return a simple array containing the values you wish to add. Eg: array( 10, 15, 30 );

And a few other filters for changing various strings dynamically, from a different plugin or the active theme:

* wc_order_tip_title - changes the tip form title;
* wc_order_tip_cash_label - changes the Cash tip button label;
* wc_order_tip_custom_label - changes the Custom tip button label;
* wc_order_tip_custom_enter_tip_placeholder - changes the Custom tip field placeholder;
* wc_order_tip_display_form - prevents the tip form from being displayed on the page.

And one filter for the backend:

* wc_order_tip_reports_date_time_format - allows changing the date format of the reports order created date/time. The format needs to comply with the PHP date format. See more [here](https://www.php.net/manual/en/function.date.php)

CSS classes and IDs that allow customization:

* #wooot_order_tip_form - main form container
* #wooot_order_tip_form button.woo_order_tip - regular tip buttons
* #wooot_order_tip_form button#woo_order_tip_cash - cash tip button
* #wooot_order_tip_form button#woo_order_tip_custom - custom tip button
* #wooot_order_tip_form p.woo_order_tip_custom_text_field - row for the custom tip input box
* #wooot_order_tip_form input.woo_order_tip_custom_text - custom tip input box
* #wooot_order_tip_form button.woo_order_tip_apply - tip apply button
* #wooot_order_tip_form button.woo_order_tip_remove - tip remove button

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

== Changelog ==

= 1.5.3 =
*Released 24 May 2025*

* Fixed issue that triggered multiple tip reports to be duplicated
* Small other UI changes

= 1.5.2 =
*Released 4 February 2025*

* Added default options. Some options require default values for the correct workings of the plugin. Added *wc_order_tip_session_type* for the time being

= 1.5.1 =
*Released 2 February 2025*

* Added new option to select the type of session you would like to use for tip storage. On certain hosting platforms, such as Dreamhost, the usage of the PHPSESSID session, destroys the page cache and therefore, the shop should use WooCommerce only session, while on other hosters, you can use the PHP session as well.
* Other small fixes and compatibility checks

= 1.5.0 =
*Released 26 October 2024*

* Replaced WOO_Order_Tip_Admin_Reports_Views admin class and WOO_Order_Tip_Main_Views front end class with functional components
* Reports have been reengineered. In order to sustain big amounts of data, the reports are being displayed recursively, 100 at a time when filters are applied
* Export to CSV is now performed via AJAX. export_tips_to_csv(), get_tips_csv_header() and create_tips_csv_lines() methods for class WOO_Order_Tip_Admin_Reports have been removed
* Assets have been moved to their own top-level assets folder and webpack has been introduced for bundling assets into optimized chunks
* Replaced views classes with PHP included templates
* Reports converted to WC specific functions
* Added two custom JS events **wootipplaced** and **wootipremove** that can be listened on - **wootipplaced** runs after the tip is added and **wootipremove** runs after the tip is removed
* replaced gmdate() with DateTime object for dates in reports
* Various other fixed and security patches

= 1.4.2 =
*Released 24 March 2024*

* fixed alert on remove tip not working

= 1.4.1 =
*Released 22 March 2024*

* fixed wootip_export_nonce warning in admin\controllers\reports.class.php on line 380
* prevents custom tip from being added if the value is 0

= 1.4.0 =
*Released 16 March 2024*

* secured the export_tips_to_csv() method by checking the current user's capabilities and by implementing an nonce by using the wp_nonce_url() function
* the export_tips_to_csv() method has been introduced starting with version 1.1.1 of the plugin. I have updated all versions starting with 1.1.1 to reflect the same functionality as version 1.4.0 in an attempt to preserver backward compatibility
* added translator comments for placeholders in printf() and sprintf() functions
* replaced reports views class with included individual php files
* replaced date() function with gmdate() function
* escaped all displayed strings
* added versions to wp_register_script / wp_register_style / wp_enqueue_script / wp_enqueue_style functions
* revised and refactored JS files
* removed admin_body_class() function that impacted the admin body tag classes
* the plugin is now HPOS compatible
* cleared all errors and warnings in the Plugin check WP plugin

= 1.3.1 =
*Released 11 December 2023*

* Changed id="woo_order_tip_custom" to id="woo_order_tip_cash" for the Cash tip button to allow custom styling
* Added support for [WooCommerce Subscriptions](https://woo.com/products/woocommerce-subscriptions/) by allowing tips to be set as recurring
* Added a new option that allows to choose the WooCommerce Subscriptions functionality
* Added a new option labelled **Display tip total for percentage amount** that allows displaying the tip total for the percentage amount

= 1.3.0 =
*Released 27 March 2023*

* Fixed tips not displaying in Reports
* Added new feature to apply fee filter in the Reports section in order to display tips. All fees will appear in the panel so you will need to apply the corresponding fee filters to see all the related reports
* Fixed tip being taxed when Is taxable setting is set to No
* Fixed Fatal error appearing on Coupon Management page (frontend/controllers/main.class.php line 248 - Uncaught Error: Call to a member function add_fee() on null)
* Fixed Fatal error preventing navigation to Orders page
* Tested WooCommerce 7.5.1 & PHP 8.1

= 1.2.2 =
*Released 10 March 2022*

* Added type attribute to <button> tags for the tip. This fixes issues on some websites which were experimenting them especially on the checkout page
* Added wc_order_tip_display_form filter which prevents the tip form from being displayed on the page
* Tested PHP 8.1.3
* Tested WooCommerce 6.3.0

= 1.2.1 =
*Released 08 March 2022*

* Fixed plugin breaking backend of site

= 1.2.0 =
*Released 08 March 2022*

* Added possibility of adding a label for the custom field tip in order to avoid displaying the label in paranthesis such as Tip (Add a tip). Admin is able to set their own custom label
* Fixed wrong label in checkout summary problem. The value in the label was being set one step behind the current custom tip
* Fixed order fail on checkout page clearing the tip. If an order failed for any reason (credit card issue or otherwise), the tip would disappear from the order total. The issue has been fixed
* Added new options to enable/disable the alert when the tip is removed and to set a custom message if the alert is enabled
* The custom tip field used to allow only numbers and . (dot, for decimal) characters in it. Some users prefer to use the , (comma, for decimal) symbol. This feature has been added
* Since WooCommerce announces the WooCommerce -> Reports will be removed in the future, the order tips reports have been made available also in the WooCommerce -> Settings -> Order Tip tab -> Tip Reports subtab
* In the Reports filters, a new field has been added for Order Status. Selecting a specific order status from this field will display orders having said status only after pressing the Filter button
* Export to CSV was displaying the order date in the tip name column. This has been fixed
* On certain hosting plans and on various server configurations, some of the WooCommerce session functionality was failing on the __unset methods. Added a fix to detect if there is any data set in the session before unsetting it

= 1.1.2 =
*Released 07 February 2021*

* Added a fix for creating an order from the backend. The plugin was crashing the website when a new order was added manually from the backend
* Added capability for decimal tip amount
* Added a filter to allow changing the reports order creation date/time in the Reports section in the backend
* Renamed the reports Name column to Type. It refers to the type of tip
* Added the customer name in the reports

= 1.1.1 =
*Released 30 January 2021*

* Added backward compatibility with 1.0.1 to display tips in the reports for the orders placed before v. 1.1
* Added functionality for CSV exports of tip reports
* Added version 1.1 for Dutch translations
* Fixed dates not being updated when a search is performed on the Reports page and a custom date (From/To) is selected

= 1.1 =
*Released 25 January 2021*

* Added a new option for selecting more than one position of the tip form on the cart page
* Added a new option for selecting more than one position of the tip form on the checkout page
* Added a new option to change the Tip name. You can use Donation or any other name
* Added a new option to set the label of the Custom Tip button
* Added a new option to set the label of the Custom Tip Apply Tip button
* Added a new option to set the placeholder of the Custom Tip field
* Added a new option to set the label of the Custom Tip Remove Tip button
* Added a new option to set the label of the Cash Tip button label
* Added a new option to set the prompt message for when a tip is removed
* Added a shortcode [order_tip_form] that would enable displaying the tip form on any post, page, sidebar, etc.
* Added new filters to allow customization of the labels of the form's labels and placeholders. See more in the plugin's description
* Added reports under WooCommerce -> Reports -> tab Order Tip. Reports can be filtered by date range
* Change the process of applying the tip. It no longer refreshes the page. It uses the update_checkout jQuery trigger instead
* Added partial Dutch translations thanks to Roel Mehlkopf (@mhlkpf)

= 1.0.1 =
*Released 30 August 2020*

* Applied fix for calculating the tip amount

= 1.0.0 =
*Released 18 August 2020*

* First stable version
