<?php
define('WPNJ_MEDIA_TAGS_VERSION', "2.0");
define('WPNJ_MEDIA_TAGS_DATA_VERSION', "2.0");

define('WPNJ_MEDIA_TAGS_TAXONOMY', 'wpnj-media-tags');

define('WPNJ_ADMIN_MENU_KEY', 'wpnj-media-tags');
define('WPNJ_MEDIA_TAGS_REWRITERULES','1');

define('WPNJ_MEDIA_TAGS_URL_DEFAULT', WPNJ_MEDIA_TAGS_TAXONOMY);
$wpnj_mediatag_base = get_option('wpnj_mediatag_base');
// Need to come up with validation logic here.
if (!$wpnj_mediatag_base)
	$wpnj_mediatag_base = "wpnj-media-tags";
define('WPNJ_MEDIA_TAGS_URL', $wpnj_mediatag_base);

define('WPNJ_MEDIA_TAGS_QUERYVAR', 'wpnj-wpnj-media-tag');

define('WPNJ_MEDIA_TAGS_TEMPLATE', 'mediatag.php');
define('WPNJ_MEDIA_TAGS_RSS_TEMPLATE', 'mediatags_rss.php');

?>