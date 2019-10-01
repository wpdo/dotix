=== Dotix ===
Contributors: WPDO
Tags: Credit/point system for WooCommerce, Ticket system for WooCommerce
Requires at least: 4.0
Tested up to: 5.2
Stable tag: 1.2.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

Connect your app with WooCommerce credits. Append additional credits for each product in WooCommerce.

== Usage ==

Redirect to your app link on order paid page, carring on the order key.

Let's assume the order_key=`wc_order_xQhmRjJ7`, your app URL is `https://app.EXAMPLE.com/refill`, your WordPress URL is `https://EXAMPLE.com/`

1. On the order Thank You page, show a button `<a href="https://app.EXAMPLE.com/refill?order_key=wc_order_xQhmRjJ7">Go back to app.EXAMPLE</a>`.

2. In `https://app.EXAMPLE.com/refill`, call `https://EXAMPLE.com/wp-json/dotix/v1/order/wc_order_xQhmRjJ7` with `$_POST[ 'num' ] = 'max'`, assume the consumed credits is 42 in returned JSON.

3. Add 42 credits into the corresponding user account.


== REST APIs ==

1) Show balance:
	Method: GET
	URL: `https://EXAMPLE.com/wp-json/dotix/v1/order/wc_order_xQhmRjJ7`
	Return: `{"order_id":45,"status":"completed","balance":"142"}`
	// NOTE: only the order with status=`completed` can be consumed credits.

2) Consume 100 credits:
	Method: `POST`
	URL: `https://EXAMPLE.com/wp-json/dotix/v1/order/wc_order_xQhmRjJ7`
	Data: `[ 'num' => 100 ]`
	Return: `{"order_id":45,"consumed":"100","balance":"42"}`

3) Consume all credits:
	Method: POST
	URL: https://EXAMPLE.com/wp-json/dotix/v1/order/wc_order_xQhmRjJ7
	Data: `[ 'num' => 'max' ]`
	Return: `{"order_id":45,"consumed":"42","balance":"0"}`


== Order Status ==

If an order contains only items that have valid credit in product detail, once the order is paid, the status will change to `completed` automatically instead of `processing`.

The reason to do this is because some unpaid getways (Bank Wire, Cheque, Cash on delivery) will have status `processing` once the order is placed while not paid. Thus we can't use `processing` to detect if the order is paid or not.

To make the credits in the orders with the unpaid gateways available, please update the order status to `completed`.


== Error Code ==

HTTP status: `442`
Error code: `wrong_hash`
Description: The order key doesn't match any order.

HTTP status: `409`
Error code: `wong_status`
Description: The order isn't in processing/completed status, maybe not paid yet?

HTTP status: `409`
Error code: `lack_of_param`
Description: Need to specify the ammount to consume. Either numeric or a fixed string `max`.

HTTP status: `409`
Error code: `lack_of_bal`
Description: Not enough balance left in this order.

== Description ==

This plugin will enable the connection between your app and WooCommerce orders for digital credits usage. It can be used to check the remaining credits in one order, or consume them.

== Changelog ==

= 1.2.1 - Oct 1 2019 =
* Compatibility when products are removed.

= 1.2 - Oct 1 2019 =
* Settings for barcode on/off and size.
* Settings for credit name.

= 1.1.1 - Sep 26 2019 =
* [GUI] Minor style changes.

= 1.1 - Aug 23 2019 =
* [App] Support DotixApp connection.

= 1.0 - Aug 20 2019 =
* 🎉 Initial Release.