=== Wordpress Special Characters in Usernames ===
Contributors: ClaudeSchlesser, OneAll.com
Tags: special characters, cyrillic usernames, russian usernames, arabic usernames, registration, russian, cyrillic, arabic
Requires at least: 3.0
Tested up to: 4.4.2
Stable tag: 1.2

Enables usernames containing special characters (russian, cyrillic, arabic) on your Wordpress Blog.

== Description ==

<strong>This plugin enables usernames containing special characters (russian, cyrillic, arabic) on your WordPress blog.</strong>

Per default WordPress does not allow to use special characters in usernames. Non-latin characters are silently filtered out and 
your users cannot create accounts containing cyrillic (russian) or arabic letters. This plugin is the solution to this problem.

The plugin works for users that register with their username/password as well as for users that register using our
<a href="https://wordpress.org/plugins/oa-social-login/">Social Login</a> plugin for WordPress.

<strong>Please Note</strong><br />
Special characters are encoded in the database and take a bit more space than regular characters. If users having special characters 
in their usernames still cannot register after having installed the plugin then you probably have to increase the length of the column <code>user_nicename</code> 
in the table <code>wp_users</code> in your database. 

<strong>Example:</strong><pre>
ALTER TABLE `wp_users` CHANGE `user_nicename` `user_nicename` VARCHAR(255) NOT NULL DEFAULT '';
</pre>

== Installation ==

1. Upload the plugin folder to the '/wp-content/plugins/' directory of your WordPress site,
2. Activate the plugin through the 'Plugins' menu in WordPress,

== Frequently Asked Questions ==

= Where can I report bugs, leave my feedback and get support? =

Our team answers your questions at:
http://www.oneall.com/company/contact-us/

== Changelog ==

= 1.0 =
* Initial release

= 1.1 = 
* Stable release

= 1.2 = 
* Tested with WordPress 3.6