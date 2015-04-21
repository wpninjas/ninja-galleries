<?php
function wpnj_mediatags_settings_api_init() {
	
	if (isset($_POST['wpnj_mediatag_base']))
	{
		update_option( 'wpnj_mediatag_base', $_POST['wpnj_mediatag_base'] );
	}
	
	if (function_exists('add_settings_field'))
	{
		// Add a new field to the Permalinks Options section to allow override of the default 'wpnj-media-tags' slug.
		add_settings_field('wpnj_mediatag_base', 'Media-Tags', 'wpnj_mediatags_setting_permalink_proc', 'permalink', 'optional');
	}
}
  
function wpnj_mediatags_setting_permalink_proc() {

	$wpnj_mediatag_base = get_option('wpnj_mediatag_base');
	if (!$wpnj_mediatag_base)
		$wpnj_mediatag_base = "wpnj-media-tags";
		
	?><input name="wpnj_mediatag_base" id="wpnj_mediatag_base" type="text" 
	value="<?php echo $wpnj_mediatag_base; ?>" class="regular-text code" /> 
	(<i>default is '<?php echo WPNJ_MEDIA_TAGS_URL_DEFAULT ?>'</i> )<br />
	<strong>Note</strong> Be careful not to use a prefix that may conflict with other WordPress standard prefixes like 'category', 'tag', a Page slug, etc<?php
} 



?>