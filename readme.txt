=== Social Project Donation Management ===
Contributors: wpmaster
Donate link: https://base-campus.com/toiro/support/
Tags: donation,social project,payment,shopping,credit card,payment request,e-commerce
Requires at least: 4.9
Tested up to: 7.4
Requires PHP: 5.6
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin is a beta version which is expected further improvement.
You can try it out in your environment at your own risk.

== Description ==
This plugin provides additional features to the payment form by PAY.JP with simple shortcode.
The features are designed for donation to an organization that has multiple projects. 
The features are shown as below.
1. A form for donors to enter personal informations and to choose a project to make a donation before the payment.
2. An admin page for donation management.

Note:
The supported currency is only JPY so far.

Example of Shortcode:

	[simple-payjp-payment amount=50 form-id="id-string" name='no' result-ok="https://example.tokyo/?page_id=7" result-ng="https://example.tokyo/?page_id=8" ]

 * amount (mandatory*): price in JPY
 * plan-id (mandatory*): subscription plan ID
 * form-id (mandatory): any ID of the form
 * name: show/hide name field ('yes' => show (default), 'no' => hide)
 * result-ok: page url to redirect after payment succeeded if you want to customize success message
 * result-ng: page url to redirect after payment failed if you want to customize failure message
 * prorate: disabled/enabled prorated for subscription payment ('no' => not prorated (default), 'yes' => prorated)

(*) 'amount' is mandatory for single payment. 'plan-id' is mandatory for subscription payment. 'amount' and 'plan-id' should be exclusive.

You can confirm these information of each payments in descripton property of Charge record on PAY.JP admin panel.

Only one shortcode can be placed in a page.
For more information, please refer Simple PAY.JP Payment's readme since Social Project Donation Management uses

== Installation ==
Plugin "Social Project Donation with PAY.JP" must be installed prior to the installation.
If "Simple PAY.JP Payment" plugin is installed in your environment, deactivate it beforehand.

1. From the WP admin panel, click “Plugins” -> “Add new”.
2. In the browser input box, type “Social Project Donation Management”.
3. Select the “Social Project Donation Management” plugin and click “Install”.
4. Activate the plugin.

OR…

1. Unpack the download package.
2. Upload all files to the /wp-content/plugins/ directory.
3. Activate this plugin in \"Plugin\" menu.

== Frequently Asked Questions ==

= A question that someone might have =

An answer to that question.

= What about foo bar? =

Answer to foo bar dilemma.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

== Upgrade Notice ==

== Arbitrary section ==
