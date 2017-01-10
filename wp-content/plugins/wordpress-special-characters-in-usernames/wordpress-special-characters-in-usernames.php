<?php
/*
Plugin Name: Wordpress Special Characters in Usernames
Plugin URI: http://www.oneall.com/
Description: Enables usernames containing special characters (russian, cyrillic, arabic) on your Wordpress Blog
Version: 1.2
Author: Claude Schlesser
Author URI: http://www.oneall.com/
License: GPL2
*/


/**
 * Overrides the Wordpress sanitize_user filter to allow special characters
 */
function wscu_sanitize_user ($username, $raw_username, $strict)
{
	//Strip HTML Tags
	$username = wp_strip_all_tags ($raw_username);

	//Remove Accents
	$username = remove_accents ($username);

	//Kill octets
	$username = preg_replace ('|%([a-fA-F0-9][a-fA-F0-9])|', '', $username);

	//Kill entities
	$username = preg_replace ('/&.+?;/', '', $username);

	//If strict, reduce to ASCII, Cyrillic and Arabic characters for max portability.
	if ($strict)
	{
		//Read settings
		$settings = get_option ('wscu_settings');

		//Replace
		$username = preg_replace ('|[^a-z\p{Arabic}\p{Cyrillic}0-9 _.\-@]|iu', '', $username);
	}

	//Remove Whitespaces
	$username = trim ($username);

	// Consolidate contiguous Whitespaces
	$username = preg_replace ('|\s+|', ' ', $username);

	//Done
	return $username;
}

add_filter ('sanitize_user', 'wscu_sanitize_user', 10, 3);