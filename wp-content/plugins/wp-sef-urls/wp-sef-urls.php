<?php

/*
Plugin Name: WP SEF Urls
Plugin URI: http://cyber-notes.net
Description: Search Engine Friendly urls for Wordpress
Version: 0.1
Author: Santiaga
Author URI: http://cyber-notes.net
License: GPLv2 or later
*/

/*

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/

if(is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX)) {
	add_action('admin_menu','wp_sefurls_options');

	function wp_sefurls_options() {
		/* Add new submenu */
		add_options_page('WP SEF urls','WP SEF urls','manage_options','wp_sefurls','sefurls_options');
		/* Add options */
		add_option('wp_sefurls_date_structure','/archives/%year%/%monthnum%/%day%/');
		add_option('wp_sefurls_author_structure','/users/%author%.html');
		add_option('wp_sefurls_page_structure','%pagename%.html');
		add_option('wp_sefurls_category_slug','');
		update_option('permalink_structure','/%category%/%postname%.html');
		update_option('tag_base','tags');
	}

	function sefurls_options() {
		/* Start Options Form */
		echo "
			<h3>WP SEF URL`s Settings:</h3>\n
			<form method=\"post\" action=\"options.php\" id=\"options\">\n
			";
		wp_nonce_field('update-options');

		/* Options */
		echo "
			<br><label>Post permalink structure:</label><input type=\"text\" size=\"30\" name=\"permalink_structure\" value=\"".get_option('permalink_structure')."\" /> Default: /%category%/%postname%.html<br>\n
			<br><label>Category base:</label><input type=\"text\" size=\"30\" name=\"wp_sefurls_category_slug\" value=\"".get_option('wp_sefurls_category_slug')."\" /> Default: none<br>\n
			<br><label>Tag base:</label><input type=\"text\" size=\"30\" name=\"tag_base\" value=\"".get_option('tag_base')."\" /> Default: tags<br>\n
			<br><label>Author permalink structure:</label><input type=\"text\" size=\"30\" name=\"wp_sefurls_author_structure\" value=\"".get_option('wp_sefurls_author_structure')."\" /> Default: /users/%author%.html<br>\n
			<br><label>Archives permalink structure:</label><input type=\"text\" size=\"30\" name=\"wp_sefurls_date_structure\" value=\"".get_option('wp_sefurls_date_structure')."\" /> Default: /archives/%year%/%monthnum%/%day%/<br>\n
			<br><label>Page permalink structure:</label><input type=\"text\" size=\"30\" name=\"wp_sefurls_page_structure\" value=\"".get_option('wp_sefurls_page_structure')."\" /> Default: %pagename%.html<br>\n
			";

		/* End Options Form */
		echo "
			<br>\n
			<input type=\"hidden\" name=\"action\" value=\"update\" />\n
			<input type=\"hidden\" name=\"page_options\" value=\"wp_sefurls_date_structure,wp_sefurls_author_structure,wp_sefurls_page_structure,permalink_structure,tag_base,wp_sefurls_category_slug\" />\n
			<input type=\"submit\" class=\"button-primary\" name=\"submit\" value=\"Save Changes\">\n
			</form>\n
			<br>\n
			";
	}
	
}

/* Modify default links */
add_action('init','sefulr_links_rewrite');
function sefulr_links_rewrite() {
	global $wp_rewrite;
	$wp_rewrite->date_structure=get_option('wp_sefurls_date_structure'); // date permalink
	$wp_rewrite->author_structure=get_option('wp_sefurls_author_structure'); // author permalink
	$wp_rewrite->page_structure=get_option('wp_sefurls_page_structure'); //page permalink
	flush_rewrite_rules(false);
	//var_dump($wp_rewrite);
}

/* Remove categoru base */
add_filter('user_trailingslashit','remcat_function');
function remcat_function($link) {
	$category_slug=(get_option('wp_sefurls_category_slug')=="")?"/":"/".get_option('wp_sefurls_category_slug')."/";
	return str_replace("/category/",$category_slug,$link);
}

/* add slash to the end of url */
// variants - single, single_trackback, single_feed, single_paged, feed, category, page, year, month, day, paged, post_type_archive
add_filter('user_trailingslashit','sefurl_slash',10,2);
function sefurl_slash($url,$type) {
	if('category'==$type) return trailingslashit($url);
	if('year'==$type) return trailingslashit($url);
	if('month'==$type) return trailingslashit($url);
	if('day'==$type) return trailingslashit($url);
	if('paged'==$type) return trailingslashit($url);
	return $url;
}

?>