<?php
/*
Plugin Name: Meta Keywords Generator
Plugin URI: https://www.techphernalia.com/meta-keywords-generator/
Description: This plugin helps your SEO by adding meta keywords tag to each page, post, archive (category, tag, date, day and year). Now it also allows you to specify common keywords to be included on every web page. Plugin from one of the best coder <a href="https://www.durgesh.org/" target="_blank">Durgesh Chaudhary</a>. For any support just leave your question on our <a href="https://www.techphernalia.com/meta-keywords-generator/" target="_blank">Meta Keywords Generator</a> page.<br/>Please update some settings to get more benefit <a href="options-general.php?page=techphernalia">Update settings</a>
.
Version: 1.11
Author: Durgesh Chaudhary
Author URI: https://www.durgesh.org/
*/
add_action('admin_menu', 'tp_mkg_add_page');
add_action('admin_init', 'tp_mkg_register');
add_action('wp_head','tp_act');
add_action('rightnow_end','tp_notify');

function tp_mkg_register(){
	register_setting( 'tp_mkg_options', 'tp_mkg_options', 'tp_mkg_options_validate' );
	add_settings_section('tp_mkg_main_section', 'Quick Settings', 'tp_mkg_section_main_render', 'techphernalia');
	add_settings_field('tp_mkg_compulsary', 'Compulsary Keywords', 'tp_mkg_render_fields', 'techphernalia', 'tp_mkg_main_section');
}

function tp_mkg_add_page() {
	add_options_page('Meta Keywords Generator', 'Meta Keywords Generator ', 'manage_options', 'techphernalia', 'tp_mkg_render_page');
}

function tp_mkg_render_page() {
	echo '<style>#tp_promotion span {display: none;}#tp_promotion a{background: url(https://techphernalia.com/sprite_master.png) left top no-repeat;float: left;width: 32px;height: 32px;margin-right: 10px;}#tp_promotion a.twitter_link{	background-position: left -414px;}#tp_promotion a.facebook_link{background-position: -69px -414px;}</style>';
	echo '<div class="wrap">
	<center><h2>Meta Keywords Generator Settings</h2></center>
	We are working continuously to provide you more SEO benefit and here comes the first in this series which allow you to provide some keywords which you want to be available on each and every web page served to your user. Currently we have only one setting we will have more soon.<br/><br/>Feel free to <b>request features</b> on our <a href="https://www.techphernalia.com/" target="_blank">blog</a> or on <a href="https://www.techphernalia.com/meta-keywords-generator/" target="_blank">plugin page</a>.
	<form action="options.php" method="post">';
	settings_fields('tp_mkg_options');
	do_settings_sections('techphernalia');
	echo '<input name="Submit" class="button-primary" type="submit" value="';
	esc_attr_e('Save Changes');
	echo'" />
	</form></div>';
	echo '<br/><br/><div id="tp_promotion"><a target="_blank" href="https://www.techphernalia.com/feed/" title="Subscribe to Our RSS feed" class="rss_link"><span>Subscribe to RSS feed</span></a>
<a href="https://twitter.com/techphernalia" target="_blank" title="Follow us on Twitter" class="twitter_link"><span>Follow us on Twitter</span></a><a href="https://facebook.com/techphernalia" target="_blank" title="Visit us on Facebook" class="facebook_link"><span>Visit us on Facebook</span></a>
</div>';
}


function tp_mkg_section_main_render() {
echo '<b>Compulsary Keywords</b> : Comma separated keywords which should appear on all the web page displayed.';
}

function tp_mkg_render_fields() {
	$options = get_option('tp_mkg_options');
	echo "<textarea id='tp_mkg_compulsary' name='tp_mkg_options[tp_mkg_compulsary]' cols='80' rows='10' maxlength='500' style='max-width:660px;max-height:164px;' >{$options['tp_mkg_compulsary']}</textarea>";
}
function tp_mkg_options_validate($input) {
	$temp = trim($input['tp_mkg_compulsary']);
	$temp = str_replace(";","",$temp);
	$newinput['tp_mkg_compulsary'] = trim(str_replace("\"","",$temp));
	return $newinput;
}

function tp_notify () {
	echo '<p>SEO provided by <strong><a href="https://www.techphernalia.com/meta-keywords-generator/" target="_blank">Meta Keywords Generator</a></strong> from <a href="https://www.techphernalia.com/" target="_blank">techphernalia.com</a></p>';
}

function tp_parse ($str) {
	$str = str_replace("\"","'",$str);
	$done = str_replace(", "," ",$str);
	$done = str_replace(" ",", ",$done);
	if (strpos($str," ")) return $str.", ".$done;
	else return $str;
}

function tp_act () {
	$name = get_option("blogname");
	$desc = get_option("blogdescription");
	
	if (is_tag()) $title = single_tag_title('',false);
	if (is_category()) $title = single_cat_title('',false);
	if (is_single() || is_page()) {
		$add = "";
		$postid = get_query_var("p");
		$post = get_post($postid);
		$title = single_post_title('',false);
		$catlist = get_the_category($post->ID);
		if (is_array($catlist)) { foreach ($catlist as $catlist) {	$add .= ", ".$catlist->name;	}}
		$taglist = get_the_tags($post->ID);
		if (is_array($taglist)) { foreach ($taglist as $taglist) {	$add .= ", ".$taglist->name; }}
		$description = substr(strip_tags($post->post_content),0,200);
	}
	$tp_mkg_options=get_option("tp_mkg_options");
	echo '<!-- SEO by Meta Keywords Generator : techphernalia.com v1.11 start-->
';
	if (!is_home()) {
		echo '<meta name="keywords" content="'.tp_parse($title).', '.tp_parse($name).$add.", ".$tp_mkg_options["tp_mkg_compulsary"].'" />
<meta name="description" content="'.str_replace("\"","'",strip_shortcodes( $description )).'" />
';
	} else {
		echo '<meta name="keywords" content="'.tp_parse($desc).', '.$name.", ".$tp_mkg_options["tp_mkg_compulsary"].'" />
<meta name="description" content="'.str_replace("\"","'",strip_shortcodes( $desc )).'" />
';
	}
	echo '<!-- SEO by Meta Keywords Generator : techphernalia.com v1.1 end-->
';
}
?>