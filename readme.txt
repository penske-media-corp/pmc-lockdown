=== PMC Lockdown ===
Contributors: pmcdotcom, mintindeed, IAmWilliamWallace, sachinkraj
Tags: maintenance
Requires at least: 3.5
Tested up to: 3.9.1
Stable tag: 0.9.5

Ability to enter maintenance lockdown mode: force-logout all non-administrators, prevent logins from non-administrators, and disable commenting.

== Description ==

Sometimes you don't need (or want) to shut down the entire frontend of the site during maintenance.  Unlike the default WordPress maintenance mode, this plugin allows your site to stay running.

* Any non-Administrator will be logged out
* Comments will be closed (disabled) on all posts

This plugin does not interact with, prevent, or override WordPress's maintenance mode.  It's complementary.

Github: https://github.com/Penske-Media-Corp/pmc-lockdown

WordPress.org plugin repository: http://wordpress.org/extend/plugins/pmc-lockdown/

Image by [Chris Randall](http://www.flickr.com/photos/chrisrandall/4608136274/); some rights reserved.  See link for details.


== Installation ==
Like all other plugins, you need to follow some basic steps to install this plugin.

1. Deploy `pmc_lockdown.php` to the `/wp-content/plugins/` directory. 

2. Go to your plugins section under WordPress admin. You will see "PMC Lockdown" in the plugins list. Click on the activate.

3. You will see a new menu item will be added under Settings section. Click on "PMC Lockdown", from the left menu under Settings or click on the "Settings" link below the plugin name in WordPress Plugins page.

== Frequently Asked Questions ==

None yet!

== Changelog ==

= 1.0.0 =
* WordPress compatibility bug fixes. Last tested 3.9.1
* Plugin installation method changed to support later version of WordPress
* Installation instrcutions updated

= 0.9.5 =
* Updating installation instructions to note settings location.  Props convissor
* Adding maintenance message to login screen.  Props convissor

= 0.9.4 =
* Fixing potential bug, before a user is logged out making sure explicitly that PMC_LOCKDOWN is defined.

= 0.9.3 =
* Initial release
