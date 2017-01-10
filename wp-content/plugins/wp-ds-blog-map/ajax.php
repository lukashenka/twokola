<?php
require_once(preg_replace('|wp-content.*$|','', __FILE__) . 'wp-config.php');

header('Content-type: text/javascript; charset='.get_settings('blog_charset'), true);
header('Cache-control: max-age=2600000, must-revalidate', true);

function error() { die( "alert('Что-то не заработало :(')" ); }

if(!isset($_POST['action'])) { error(); }

global $dsblogmap;

switch($_POST['action']) {
case 'show_cat':
	if(!isset($_POST['id'])) error();
	$id = $_POST['id'];
	if(!is_numeric($id) || $id < 0) error();
	$element_id = 'pl_cat_'.$id;
	$results = $dsblogmap->wp_ds_blogmap_getposts($id, false, 0);
	break;
case 'show_cloud':
	$element_id = 'pl_cloud';
	$results = $dsblogmap->wp_ds_blogmap_cloud();
	break;
default:
	error();
	break;
}

// Compose JavaScript for return
$results = addcslashes($results, "\\'");

die( "document.getElementById('".$element_id."').innerHTML = '$results';" );

?>