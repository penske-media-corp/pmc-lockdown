=== PMC Lockdown ===
Contributors: pmcdotcom, mintindeed, IAmWilliamWallace
Tags: maintenance
Requires at least: 3.2
Tested up to: 4.1.1
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

Nothing goes in the plugins directory.

1. Deploy `pmc_lockdown.php` to the `/wp-content/mu-plugins/` directory. Simply placing this file activates it. To deactivate it, move it from that location.

2. The user interface for turning the "Maintenance Lockdown" mode on and off is in the Settings | Privacy section of your site's adminstration system.

== Frequently Asked Questions ==

None yet!

== Changelog ==

= 0.9.5 =
* Updating installation instructions to note settings location.  Props convissor
* Adding maintenance message to login screen.  Props convissor

= 0.9.4 =
* Fixing potential bug, before a user is logged out making sure explicitly that PMC_LOCKDOWN is defined.

= 0.9.3 =
* Initial release
