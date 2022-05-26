# Fork of “Honeypot for Contact Form 7” to use it with “Accessibility fixes of Contact Form 7”

The plugin “Honeypot for Contact Form 7” has a dependance to Contact Form 7 (CF7) and if you are using the fork of CF7 “[Accessibility fixes of Contact Form 7](https://github.com/fizzybumblebee/contact-form-7/blob/a11y-fixes/README-a11y-fixes.md)”, this plugin will not work. Actually, it checks for the presence of this file `contact-form-7/wp-contact-form-7.php` and with the fork, the file is actually in a folder named `contact-form-7-a11y` (in order to prevent automatic update from the original plugin).

So, we need to fork this plugin too and to modify the path to the file.

## How to contribute?

This fork must stay close to the evolving changes of the original plugin and so it must stay up to date.

This repository is a fork from the original plugin. The work is in progress on the `cf7-a11y` branch in order to keep the `master` branch the same as the original.

### Using specific comment tags

To make it easier to merge changes with new updates, changes in the code is documented. Make sure to wrap the section of code you've changed with the following comment tags:

```php
/**
 * #cf7-a11y-start
 * Describe quickly in English the changes made
 */

// code

/** #cf7-a11y-end */
```

### Commenting original code

If you ever want to remove a whole chunk of code (ie. if statement, a whole function, etc.), comment the original code and in the `#cf7-a11y-start` comment tag, quickly explain *why* this has been removed, especially if it has to do with web accessibility.

For example, this could look like this (this example is not taken from the original plugin by the way):

```javascript
/**
 * #cf7-a11y-start
 * Remove role="button" : if button is needed
 * use <button> tags instead of <a> tags.
 */

// if (link) {
//    link.setAttribute('role', 'button')
// }

/** #cf7-a11y-end */
```

## How to update this fork from the original GitHub repository

Follow these instructions to update this fork from the official Honeypot for Contact Form 7:

1. Check that the `master` branch is up to date on your computer;
1. Check that the `master` branch is up to date from the forked repository. Github is telling you this information in the interface. If it's not up to date, [rebase it](https://stackoverflow.com/a/7244456):
	```
	# Add the remote, call it "upstream" (only the first time you do it):

	git remote add upstream https://github.com/whoever/whatever.git

	# Fetch all the branches of that remote into remote-tracking branches:

	git fetch upstream

	# Make sure that you're on your master branch:

	git checkout master

	# Rewrite your master branch so that any commits of yours that
	# aren't already in upstream/master are replayed on top of that
	# other branch:

	git rebase upstream/master

	# Push it

	git push
	```
1. Checkout `cf7-a11y` branch;
1. Two cases:
	1. Either the last commit in upstream/master **is** the last release commit so: rebase this branch from `master` (or merge `master` into it) and push;
	1. Or the last commit in upstream/master **is not** the last release commit so:
		1. [Find the tag from the last plugin version](https://github.com/nocean/cf7-honeypot/tags);
		1. Fetch it: `git fetch upstream refs/tags/2.1` (“2.1” is the tag name for this example);
		1. Merge this tag into `cf7-a11y` branch. Just like: `git merge 2.1` and push.
1. Test it into a local WordPress project.
1. Fix the version number of the plugin into the `honeypot.php` file:
	1. In the comment block at the top of the file, modify the "Version:" line;
	1. Keep the officiel version number of Honeypot for Contact Form 7;
	1. Add a suffix number : "-a11y.x" (where "x" is an incremental number).
1. Make a Github Release from the `cf7-a11y` branch.

:warning: **Do not merge `cf7-a11y` branch into `master`.**

## How to use this plugin fork into your WordPress website

1. Download [the last release of this fork](https://github.com/fizzybumblebee/cf7-honeypot/releases);
1. Into your `wp-content/plugins/` folder, create a new folder named `cf7-honeypot-a11y` (and not `contact-form-7-honeypot-a11y` or the fork will be replaced by the original with automatic updates because of the prefix `contact-form-7-honeypot` being the official name of the folder);
1. Extract the ZIP file of the release into this new folder (without the parent `contact-form-7-honeypot` folder);
1. Get your language for this plugin fork:
	1. Go on [the official “Translating WordPress” page for Honeypot for Contact Form 7](https://translate.wordpress.org/projects/wp-plugins/contact-form-7-honeypot/language-packs/) and download the translation files that you want;
	1. Extract the files into your `wp-content/languages/plugins` folder.

### Important note: No automatic updates

1. This forked plugin is only available on GitHub so it will not benefit from automatic updates on your WordPress website.
1. The translations will not benefit from automatic updates on your WordPress website either.

**You will need to update the forked plugin and translations manually by repeating the steps detailed above.**

If you have a GitHub account, you can **watch the GitHub repository** to be notified when a new release is available:

1. Click on the “Watch” button on the top of [the repository page](https://github.com/fizzybumblebee/cf7-honeypot);
1. Then click on “Custom”;
1. Check the “Releases” checkbox;
1. Click on the “Apply” button;
1. You will receive an email when a release is available.
