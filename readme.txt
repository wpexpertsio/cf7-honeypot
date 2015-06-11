=== Contact Form 7 Honeypot ===
Tags: honeypot, antispam, captcha, spam, form, forms, contact form 7, contactform7, contact form, cf7, cforms, Contact Forms 7, Contact Forms, contacts
Requires at least: 3.5
Tested up to: 4.2
Stable tag: 1.7
Contributors: DaoByDesign
Donate link: http://www.daobydesign.com/buy-us-a-coffee/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Contact Form 7 Honeypot - Adds honeypot anti-spam functionality to CF7 forms.

== Description ==

This simple addition to the wonderful <a href="http://wordpress.org/extend/plugins/contact-form-7/">Contact Form 7</a> (CF7) plugin adds basic honeypot anti-spam functionality to thwart spambots without the need for an ugly captcha.

The principle of a honeypot is simple -- <em>bots are stupid</em>. While some spam is hand-delivered, the vast majority is submitted by bots scripted in a specific (wide-scope) way to submit spam to the largest number of form types. In this way they somewhat blindly fill in fields, regardless of whether the field should be filled in or not. This is how a honeypot catches the bot -- it introduces an additional field in the form that if filled out will cause the form not to validate.

Follow us on [Twitter](http://www.twitter.com/daobydesign) and on [Facebook](http://www.facebook.com/daobydesign) for updates and news.

= IMPORTANT NOTE: =
If you are using CF7 3.6+, use the latest version of this plugin. If you are using an older version of CF7, you will need to use [CF7 Honeypot v1.3](http://downloads.wordpress.org/plugin/contact-form-7-honeypot.1.3.zip).

<strong>Support can be found [here](http://wordpress.org/support/plugin/contact-form-7-honeypot).</strong>

Visit the [Contact Form 7 Honeypot plugin page](http://www.daobydesign.com/free-plugins/honeypot-module-for-contact-form-7-wordpress-plugin) for installation & additional information.

== Installation ==

1. Install using Wordpress' "Add Plugin" feature -- just search for "Contact Form 7 Honeypot"
1. Activate the plugin
1. Edit a form in Contact Form 7
1. Choose "Honeypot" from the Generate Tag dropdown. <em>Recommended: change the honeypot element's ID.</em>
1. Insert the generated tag anywhere in your form. The added field uses inline CSS styles to hide the field from your visitors.

= Installation & Usage Video =
[youtube https://www.youtube.com/watch?v=yD2lBrU0gA0]
For the more visually-minded, here is a [short video showing how to install and use CF7 Honeypot](https://www.youtube.com/watch?v=yD2lBrU0gA0) from the fine folks at RoseApple Media.

= Altering the Honeypot Output HTML [ADVANCED] =
Should you wish to, you can change the outputted Honeypot HTML by using the **wpcf7_honeypot_html_output** filter.

Ex:
`<?php function my_honeypot_override( $html, $args ) {
    // [DO STUFF HERE]
    return $html;
}
add_filter('wpcf7_honeypot_html_output', 'my_honeypot_override', 10, 2 ); ?>`

== Frequently Asked Questions == 

= Will this module stop all my contact form spam? =

* Probably not. But it should reduce it to a level whereby you don't require any additional spam challenges (CAPTCHA, math questions, etc.).

= Are honeypots better than CAPTCHAs? =

* This largely depends on the quality of the CAPTCHA. Unfortunately the more difficult a CAPTCHA is to break, the more user-unfriendly it is. This honeypot module was created because we don't like CAPTCHA's cluttering up our forms. Our recommendation is to try this module first, and if you find that it doesn't stop enough spam, then employ more challenging anti-spam techniques.

= Can I modify the HTML this plugin outputs? =

* Yep! New in version 1.5 of the plugin you're able to adjust the HTML by hooking the output filter for the plugin. See the **Installation** section for more details.

== Changelog ==
= 1.7 =
Provides backwards compatibility for pre-CF7 4.2, introduces ability to remove accessibility message.

= 1.6.4 =
Quick fix release to fix PHP error introduced in 1.6.3.

= 1.6.3 =
Updates to accommodate changes to the CF7 editor user interface.

= 1.6.2 =
Small change to accommodate validation changes made in CF7 4.1.

= 1.6.1 =
Small change to accommodate changes made in CF7 3.9.

= 1.6 =
Quite a lot of code clean-up. This shouldn't result in any changes to the regular output, but it's worth checking your forms after updating. Also, you'll note that you now have the ability to add a custom CLASS and ID attributes when generating the Honeypot shortcode (in the CF7 form editor).

= 1.5 =
Added filter hook for greater extensibility. See installation section for more details.

= 1.4 =
Update to make compatible with WordPress 3.8 and CF7 3.6. Solves problem of unrendered honeypot shortcode appearing on contact forms.

= 1.3 =
Update to improve outputted HTML for better standards compliance when the same form appears multiple times on the same page.

= 1.2 =
Small update to add better i18n and WPML compatibility.

= 1.1 =
Small update for W3C compliance. Thanks <a href="http://wordpress.org/support/topic/plugin-contact-form-7-honeypot-not-w3c-compliant">Jeff</a>.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==
= 1.7 =
Recommended update for all users using CF7 3.6 and above.

= 1.6.3 =
Must update if running CF7 4.2 or above. If using less than CF7 4.2, use the v1.6.2 of this plugin.

= 1.6.2 =
Must update if running CF7 4.1 or above. Update also compatible with CF7 3.6 and above. If using less than CF7 3.6, use the v1.3 of this plugin.

= 1.6.1 =
Must update if running CF7 3.9 or above. Update also compatible with CF7 3.6 and above. If using less than CF7 3.6, use the v1.3 of this plugin.

= 1.6 =
New custom "class" and "id" attributes. Upgrade recommended if you are using CF7 3.6+, otherwise use v1.3 of this plugin.

= 1.5 =
Includes "showing shortcode" fix from version 1.4 and also includes new filter hook. Upgrade recommended.

= 1.4 =
Solves problem of unrendered honeypot shortcode appearing on contact forms. Upgrade immediately.