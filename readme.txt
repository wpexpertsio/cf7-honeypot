=== Honeypot for Contact Form 7 ===
Tags: honeypot, antispam, anti-spam, captcha, spam, bots, form, forms, contact form 7, contactform7, contact form, cf7
Requires at least: 4.8
Tested up to: 5.7
Stable tag: trunk
Requires PHP: 5.6
Contributors: nocean, DaoByDesign
Donate link: http://www.nocean.ca/buy-us-a-coffee/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Honeypot for Contact Form 7 adds honeypot non-intrusive anti-spam functionality to CF7 forms.

== Description ==

This simple addon module to the wonderful [Contact Form 7](https://wordpress.org/plugins/contact-form-7/) (CF7) plugin adds basic honeypot anti-spam functionality to thwart spambots without the need for an ugly captcha.

The principle of a honeypot is simple -- *bots are stupid*. While some spam is hand-delivered, the vast majority is submitted by bots scripted in a specific (wide-scope) way to submit spam to the largest number of form types. In this way they somewhat blindly fill in fields, regardless of whether the field should be filled in or not. This is how a honeypot catches the bot -- it introduces an additional field in the form that if filled out will trigger the honeypot and flag the submission as spam.

= REQUIRED/RECOMMENDED PLUGINS =
You will need [Contact Form 7](https://wordpress.org/plugins/contact-form-7/) version 3.0+ at a minimum. It is recommended to use version 5.3+ of CF7, for better spam logging. For the best results, we suggest always using the latest versions of WordPress and CF7.

We highly recommend [Flamingo](https://wordpress.org/plugins/flamingo/) with CF7 and this plugin. Using Flamingo allows you to track spam submissions (via `inbound messages / spam` tab in Flamingo), showing you what got caught in the honeypot and why. Be sure to check your Honeypot settings to turn storing the honeypot on for this.

= SUPPORT / SOCIALS =
Support can be found [here](http://wordpress.org/support/plugin/contact-form-7-honeypot). Follow us on [Twitter](https://twitter.com/NoceanCA) and on [Facebook](https://www.facebook.com/nocean.ca/) for updates and news.

Visit the [Honeypot for Contact Form 7 plugin page](http://www.nocean.ca/plugins/honeypot-module-for-contact-form-7-wordpress-plugin/) for additional information or to **[buy us a coffee](http://www.nocean.ca/buy-us-a-coffee/)** to say thanks.

= PRIVACY =
This plugin does not track users, store any user data, send user data to external servers, nor does it use cookies. This is an addon plugin, and requires Contact Form 7. Please review Contact Form 7's privacy policies for more information.

= LOCALIZATION / TRANSLATION =
If you'd like to translate this plugin, please visit the plugin's [translate.wordpress.org](https://translate.wordpress.org/projects/wp-plugins/contact-form-7-honeypot) page. As of v1.10, all translation is handled there. Version 2.0 brings a bunch of new strings in need of translation, so a huge thank you to the polyglots that contribute!

= LEGACY/OLD VERSIONS =
The latest version of this plugin is designed to work with the latest version of Contact Form 7 and Wordpress. If you are using older versions of either, you're best to find the version of this plugin released around the same time as the version you're using. You can access older versions of this plugin by clicking *Advanced View* on the right of the plugin's page and scrolling to the bottom of the plugin's page. **Use at your own risk**. We strongly recommend upgrading to the latest versions whenever possible.

== Installation ==

1. Install using the Wordpress "Add Plugin" feature -- just search for "Honeypot for Contact Form 7".
1. Confirm that [Contact Form 7](https://wordpress.org/plugins/contact-form-7/) is installed and activated. Then activate this plugin.
1. Edit a form in Contact Form 7.
1. Choose "Honeypot" from the CF7 tag generator. <em>Recommended: change the honeypot element's ID.</em>
1. Insert the generated tag anywhere in your form. The added field uses inline CSS styles to hide the field from your visitors.

= Altering the Honeypot Output HTML [ADVANCED] =
While the basic settings should keep most people happy, we've added several filters for you to further customize the honeypot field. The three filters available are:

* `wpcf7_honeypot_accessibility_message` - Adjusts the default text for the (hidden) accessibility message (**can now be done on the settings page**).
* `wpcf7_honeypot_container_css` - Adjusts the CSS that is applied to the honeypot container to keep it hidden from view.
* `wpcf7_honeypot_html_output` - Adjusts the entire HTML output of the honeypot element.

For examples of the above, please see this [recipe Gist](https://gist.github.com/nocean/953b1362b63bd3ecf68c).

== Frequently Asked Questions == 

= Will this module stop all my contact form spam? =

Probably not. But it should reduce it to a level whereby you don't require any additional spam challenges (CAPTCHA, math questions, etc.).

= Are honeypots better than CAPTCHAs? =

This largely depends on the quality of the CAPTCHA. Unfortunately the more difficult a CAPTCHA is to break, the more unfriendly it is to the end user. This honeypot module was created because I don't like CAPTCHAs cluttering up my forms. My recommendation is to try this module first, and if you find that it doesn't stop enough spam, then employ more challenging anti-spam techniques.

= Can I modify the HTML this plugin outputs? =

Yep! See the **Installation** section for more details and [this Gist](https://gist.github.com/nocean/953b1362b63bd3ecf68c) for examples.

= My form is not validating with a W3C validation tool =

As of version 2.0, this shouldn't be the case any longer. However, if it is for some reason, there is a simple work around. See [here](https://wordpress.org/support/topic/w3c-validation-in-1-11-explanation-and-work-arounds/) for details.

= Does this plugin work with Flamingo? =

You bet! If the honeypot trap is triggered, an email isn't sent, but the form submission is added to the **spam** section of Flamingo so you can review what tripped things up.

= Why do you have affiliate ads on your settings page? =

I realize not everyone loves ads, but daddy's gotta pay the bills. I'm extremely grateful to the numerous users that have donated to the plugin's development over the years, and while that's awesome, I don't think donations will ever come remotely close to covering the time and effort it takes to maintain and support a plugin that now has **nearly 1.5 million downloads** and **more than 300,000 active installs**.

== Screenshots ==

1. Global Honeypot Settings
2. Honeypot CF7 Form Tag Settings

== Changelog ==
= 2.0.5 =
Improved backwards compatibility. Solves issues when plugin installed on older versions of CF7.

= 2.0.4 =
Better error checking for missing config problems.

= 2.0.3 =
General code cleanup, better adherence to WP coding standards and fixes for i18n functions.

= 2.0.2 =
Replaced text domain constant with plain string for better i18n compatability.

= 2.0.1 =
Hotfix for issue with options not being set on upgrade.

= 2.0 =
A significant update with a bunch of new things. Please see the [release notes](http://www.nocean.ca/blog/honeypot-for-contact-form-7-v2-0/).

= 1.14.1 =
Minor update to change name to comply with CF7 copyright notice.

= 1.14 =
Added do-not-store for when forms are stored in the DB (i.e. Flamingo). Improved wrapper ID masking and customization.

= 1.13 =
Additional functionality to improve spam-stopping power.

= 1.12 =
Introduces ability to force W3C compliance. See [here](https://wordpress.org/support/topic/w3c-validation-in-1-11-explanation-and-work-arounds/) for details.

= 1.11 =
Addresses accessibility concerns regarding a missing label and disables autocomplete to prevent browser autocomplete functions from filling in the field.

= 1.10 =
Updates for Function/Class changes related to CF7 4.6. Removed plugin local language support, instead use translate.wordpress.org.

= 1.9 =
Added i18n support, French language pack. Thx chris-kns

= 1.8 =
Added wpcf7_honeypot_accessibility_message and wpcf7_honeypot_container_css filters, i18n support.

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
Small update for W3C compliance. Thanks [Jeff](http://wordpress.org/support/topic/plugin-contact-form-7-honeypot-not-w3c-compliant)</a>.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==
= 2.0.5 =
Fixes some backwards compatibility issues. Recommended update.

= 2.0.4 =
This fixes some php notices about missing settings. Recommended update.

= 2.0 =
Recommended update. Much improved spam-tracking support. Requires CF7 5.0+ and WordPress 4.8+.

= 1.8 =
Recommended update for all users using CF7 3.6 and above.

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