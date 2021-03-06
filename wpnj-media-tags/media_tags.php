<?php
include_once ( dirname(__FILE__) . "/mediatags_config.php");
include_once ( dirname(__FILE__) . "/mediatags_admin.php" );
include_once ( dirname(__FILE__) . "/mediatags_rewrite.php");
include_once ( dirname(__FILE__) . "/mediatags_template_functions.php");
include_once ( dirname(__FILE__) . "/mediatags_shortcodes.php");
include_once ( dirname(__FILE__) . "/mediatags_settings.php");
include_once ( dirname(__FILE__) . "/mediatags_thirdparty.php");
include_once ( dirname(__FILE__) . "/mediatags_feed.php");
include_once ( dirname(__FILE__) . "/mediatags_export_import.php");

class wpnjMediaTags {

	var $plugindir_url;
	
	function wpnjMediaTags()
	{
		global $wp_version;
		
		$plugindir_node 						= dirname(plugin_basename(__FILE__));	
		$this->plugindir_url 					= get_bloginfo('wpurl') . "/wp-content/plugins/". $plugindir_node;
	
		add_filter('attachment_fields_to_edit', 'wpnj_mediatags_show_fields_to_edit', 11, 2);
		add_filter('attachment_fields_to_save', 'wpnj_meditags_process_attachment_fields_to_save', 11, 2);
		add_filter( 'manage_media_columns', 'wpnj_mediatags_library_column_header' );
		add_action( 'manage_media_custom_column', 'wpnj_mediatags_library_column_row', 10, 2 );

		add_action('delete_attachment', 'wpnj_mediatags_delete_attachment_proc');

		add_action('admin_head', array(&$this,'admin_head_proc'));
		add_action('wp_head', 'wpnj_add_mediatags_alternate_link');
		
		add_action( 'init', array(&$this, 'init') );
		add_action( 'admin_init', array(&$this, 'admin_init') );
		
		add_filter('query_vars', 'wpnj_mediatags_addQueryVar');
		add_action('parse_query','wpnj_mediatags_parseQuery');

		add_filter('media_upload_tabs', 'wpnj_mediatag_upload_tab');
		add_action('media_upload_mediatags', 'wpnj_media_upload_mediatags');

		// Handle Export/Import interaction
		add_action('export_wp', 'wpnj_mediatags_wp_export_metadata');
		add_action('import_post_meta', 'wpnj_mediatags_wp_import_metadata', 10, 3);

		// This MAY not be needed. This was a safety catch for the non-Permalink URLs.
		add_filter('term_link', 'wpnj_mediatags_term_link', 20, 2);

		if (function_exists('add_shortcode'))
			add_shortcode('wpnj-media-tags', 'wpnj_mediatags_shortcode_handler');

		// Add our sub-panel to the Media section. But only if WP 2.7 or higher!
		if ($wp_version >= "2.7")
		{
			add_action('admin_menu', 'wpnj_mediatags_admin_panels');
		}

		// Support for the Google Sitemap XML plugin
		add_action("sm_buildmap", 'wpnj_mediatags_google_sitemap_pages');				
	}

	function init() {
		
		$this->register_taxonomy();
		wpnj_mediatags_init_rewrite();
			
		// Checks ths plugin version again the legacy data
		if ((isset($_REQUEST['activate'])) && ($_REQUEST['activate'] == true))
		{
			$this->wpnj_mediatags_activate_plugin();
		}
		
		if ((isset($_REQUEST['page']))
		 && ($_REQUEST['page'] == WPNJ_ADMIN_MENU_KEY))
		{
			wpnj_mediatags_process_actions();
		}				
	}

	function admin_init()
	{
		wp_enqueue_script('jquery-form'); 
		if (function_exists('wpnj_mediatags_settings_api_init'))
			wpnj_mediatags_settings_api_init();		
	}
		
	function register_taxonomy() {
		// Add new taxonomy, make it hierarchical (like categories)
		  $labels = array(
		    'name' => _x( 'Media-Tags', 'taxonomy general name' ),
		    'singular_name' => _x( 'Media-Tag', 'taxonomy singular name' ),
		    'search_items' =>  __( 'Search Media-Tags' ),
		    'all_items' => __( 'All Media-Tags' ),
		    'parent_item' => __( 'Parent Media-Tag' ),
		    'parent_item_colon' => __( 'Parent Media-Tag:' ),
		    'edit_item' => __( 'Edit Media-Tag' ), 
		    'update_item' => __( 'Update Media-Tag' ),
		    'add_new_item' => __( 'Add New Media-Tag' ),
		    'new_item_name' => __( 'New Media-Tag Name' ),
		  );

		register_taxonomy(WPNJ_MEDIA_TAGS_TAXONOMY,WPNJ_MEDIA_TAGS_TAXONOMY,array(
		    'hierarchical' => false,
		    'labels' => $labels,
		    'query_var' => true,
		    'rewrite' => array( 'slug' => 'WPNJ_MEDIA_TAGS_TAXONOMY' )
		  ));

	}

	function admin_head_proc()
	{
		?>
		<link rel="stylesheet" href="<?php echo $this->plugindir_url ?>/mediatags_style_admin.css" 
			type="text/css" media="screen" />
			
		<?php if ((isset($_REQUEST['page']))
		 	&& ($_REQUEST['page'] == WPNJ_ADMIN_MENU_KEY))
		{	?><script type="text/javascript" src="<?php echo $this->plugindir_url ?>/mediatags_inline_edit.js"></script><?php }
		
		?>
		<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready(function(){

			jQuery('div#wpnj-media-tags-list-used').show();
			jQuery('div#wpnj-media-tags-list-common').hide();
			jQuery('div#wpnj-media-tags-list-uncommon').hide();

			jQuery("a#wpnj-media-tags-show-hide-used").click(function () {
				jQuery("div#wpnj-media-tags-list-used").slideToggle('slow');
				jQuery(this).text(jQuery(this).text() == 'Show Media Tags for this attachment' ? 'Media Tags for this attachment' : 'Show Media Tags for this attachment');
				return false;
			});

			jQuery("a#wpnj-media-tags-show-hide-common").click(function () {
				jQuery("div#wpnj-media-tags-list-common").slideToggle('slow');
				jQuery(this).text(jQuery(this).text() == 'Show Common Media Tags' ? 'Hide Common Media Tags' : 'Show Common Media Tags');
				return false;
			});

			jQuery("a#wpnj-media-tags-show-hide-uncommon").click(function () {
				jQuery("div#wpnj-media-tags-list-uncommon").slideToggle('slow');
				jQuery(this).text(jQuery(this).text() == 'Show Uncommon Media Tags' ? 'Hide Uncommon Media Tags' : 'Show Uncommon Media Tags');
				return false;
			});


/*

$("li").toggle(
      function () {
        $(this).css({"list-style-type":"disc", "color":"blue"});
      },
      function () {
        $(this).css({"list-style-type":"disc", "color":"red"});
      },
      function () {
        $(this).css({"list-style-type":"", "color":""});
      }
    );


			jQuery('a#wpnj-media-tags-show-hide-common').click(function () {	
				jQuery('div#wpnj-media-tags-list-common').toggle(
					function () {
						jQuery('a#wpnj-media-tags-show-hide-common').text('Hide');
						jQuery('div#wpnj-media-tags-list-common').show();
					},
					function () {
						jQuery('div#wpnj-media-tags-list-common a').text('Show');
						jQuery('div#wpnj-media-tags-list-common').hide();
					}
				);
				return false;
			});
			*/
		});
		//]]>
		</script>
		
		<?php
		
	}
		
	function wpnj_mediatags_activate_plugin()
	{
		// First see if we need to convert the data. This really only applied to pre-Taxonomy versions
		include_once ( dirname (__FILE__) . '/mediatags_legacy_convert.php' );
		wpnj_mediatags_plugin_version_check();

		wpnj_mediatags_reconcile_counts();
	}
	
	
	// Still support the original legacy version of the function. 
	// Force use of the post_parent parameter. Users wanting to search globally across all media tags should
	// switch to using the wpnj_get_attachments_by_media_tags() function.
	function wpnj_get_media_by_tag($args='')
	{
		global $post;
		
		$r = wp_parse_args( $args, $defaults );
		if (!isset($r['post_parent']))
		{
			if ($post)
				$r['post_parent'] = $post->ID;
			else
				return;
		}	
		return $this->wpnj_get_attachments_by_media_tags($args);
	}
	
	function wpnj_get_attachments_by_media_tags($args='')
	{
		global $post;

		$defaults = array(
			'call_source' => '',
			'display_item_callback' => 'wpnj_default_item_callback',
			'media_tags' => '', 
			'media_types' => null,
			'numberposts' => '-1',
			'orderby' => 'menu_order',			
			'order' => 'DESC',
			'offset' => '0',
			'post_type'	=> '',
			'return_type' => '',
			'search_by' => 'slug',
			'size' => 'medium',
			'tags_compare' => 'OR',
			'nopaging'	=> ''
		);
		$r = wp_parse_args( $args, $defaults );
		
		if ((!$r['media_tags']) || (strlen($r['media_tags']) == 0))
			return;
		
//		if ((!$r['post_parent']) || (strlen($r['post_parent']) == 0))
//		{
//			if ($post)
//				$r['post_parent'] = $post->ID;
//			else
//				return;
//		}
		
		// Future support for multiple post_parents --- Coming Soon!
//		if (strlen($r['post_parent']))
//		{
//			if (!is_array($r['post_parent']))
//			{
//				$r['post_parent'] = (array) $r['post_parent'];				
//			}			
//		}
//		echo "post_parent<pre>"; print_r($r['post_parent']); echo "</pre>";

		// Force 'OR' on compare if searching by name (not slug). This is because the name search will return multiple
		// values per each 'media_tags' searched item.
		if ($r['search_by'] != 'slug')
			$r['tags_compare'] = 'OR';

		// First split the comma-seperated wpnj-media-tags list into an array
		$r['media_tags_array'] = split(',', $r['media_tags']);
		if ($r['media_tags_array'])
		{
			foreach($r['media_tags_array'] as $idx => $val)
			{
				$r['media_tags_array'][$idx] = sanitize_title_with_dashes($val);
			}
		}

		// Next split the comma-seperated media-types list into an array
		if ($r['media_types'])
		{
			$r['media_types_array'] = split(',', $r['media_types']);
			if ($r['media_types_array'])
			{
				foreach($r['media_types_array'] as $idx => $val)
				{
					$r['media_types_array'][$idx] = sanitize_title_with_dashes($val);
				}
			}
		}
		//echo "r<pre>"; print_r($r); echo "</pre>";
		
		// Next lookup each term in the terms table. 
		$search_terms_array = array();
		if ($r['media_tags_array'])
		{
			foreach($r['media_tags_array'] as $search_term)
			{
				$get_terms_args['hide_empty'] = 0;

				if ($r['search_by'] != "slug")
					$get_terms_args['search'] = $search_term;
				else
					$get_terms_args['slug'] = $search_term;
					
				$terms_item = get_terms( WPNJ_MEDIA_TAGS_TAXONOMY, $get_terms_args );
				if ($terms_item)
					$search_terms_array[$search_term] = $terms_item;
			}
		}
		
		//echo "search_terms_array<pre>"; print_r($search_terms_array); echo "</pre>";
		
		$objects_ids_array = array();
		if (count($search_terms_array))
		{
			foreach($search_terms_array as $search_term_items)
			{
				if ($search_term_items) {
					foreach($search_term_items as $search_term_item)
					{				
						$objects_ids = get_objects_in_term($search_term_item->term_id, WPNJ_MEDIA_TAGS_TAXONOMY);
						if ($objects_ids)
							$objects_ids_array[$search_term_item->slug] = $objects_ids;
						else
							$objects_ids_array[$search_term_item->slug] = array();
					}
				}
			}
		}
		
		if (count($objects_ids_array) > 1)
		{
			foreach($objects_ids_array as $idx_ids => $object_ids_item)
			{
				if ((!isset($array_unique_ids)) && ($idx_ids == 0))
				{
					$array_unique_ids = $object_ids_item;
				}
				if (strtoupper($r['tags_compare']) == strtoupper("AND"))
				{
					$array_unique_ids = array_unique(array_intersect($array_unique_ids, $object_ids_item));
				}
				else
				{
					$array_unique_ids = array_unique(array_merge($array_unique_ids, $object_ids_item));
				}
			}			
			sort($array_unique_ids);
		}
		else if (count($objects_ids_array) == 1)		
		{
			foreach($objects_ids_array as $idx_ids => $object_ids_item)
			{
				$array_unique_ids = $object_ids_item;
				break;
			}
		}
				
		$object_ids_str = "";
		if ($array_unique_ids)
		{
			$object_ids_str = implode(',', $array_unique_ids); 
		}

		if ($object_ids_str)
		{
			$query_array = array(
				'post_type'			=> 'attachment',
				'numberposts'		=> 	-1
			);
			
			if ((isset($r['post_parent'])) && (intval($r['post_parent']) > 0))
				$query_array['post_parent'] = $r['post_parent'];
			if ((isset($r['nopaging'])) && (strlen($r['nopaging']))) 
				$query_array['nopaging'] = $r['nopaging'];
			if ((isset($r['post_type'])) && (strlen($r['post_type']))) 
				$query_array['post_type'] = $r['post_type'];

			//echo "query_array<pre>"; print_r($query_array); echo "</pre>";
			$attachment_posts = get_posts($query_array);

			$attachment_posts_ids = array();
			if ($attachment_posts)
			{
				foreach($attachment_posts as $attachment_post)
				{
					$attachment_posts_ids[] = $attachment_post->ID;
				}
			}

			$result = array_intersect($array_unique_ids, $attachment_posts_ids);
			if ($result)
			{				
				$get_post_args['post_type'] 	= "attachment";
				$get_post_args['numberposts'] 	= $r['numberposts'];
				$get_post_args['offset']		= $r['offset'];
				$get_post_args['orderby']		= $r['orderby'];
				$get_post_args['order']			= $r['order'];
				$get_post_args['include']		= implode(',', $result);

				$attachment_posts = get_posts($get_post_args);
				
				// Now that we have the list of all matching posts we need to filter by the media type is provided
				if ((isset($r['media_types_array'])) && (count($r['media_types_array'])))
				{
					foreach($attachment_posts as $attachment_idx => $attachment_post)
					{
						$ret_mime_match = wp_match_mime_types($r['media_types_array'], $attachment_post->post_mime_type);
						//echo "ret_mime_match<pre>"; print_r($ret_mime_match); echo "</pre>";
						if (!$ret_mime_match)
							unset($attachment_posts[$attachment_idx]);
					}
				}

				// If the calling system doesn't want the whole list.
				if (($r['offset'] > 0) || ($r['numberposts'] > 0))
					$attachment_posts = array_slice($attachment_posts, $r['offset'], $r['numberposts']);
				
				if ($r['return_type'] === "li")
				{
					$attachment_posts_list = "";
					foreach($attachment_posts as $attachment_idx => $attachment_post)
					{
						if ((strlen($r['display_item_callback']))
						 && (function_exists($r['display_item_callback'])))
							$attachment_posts_list .= call_user_func($r['display_item_callback'], $attachment_post, $r['size']);
					}
					return $attachment_posts_list;
				}
				else
					return $attachment_posts;
			}

		}
	}
}
$wpnjmediatags = new wpnjMediaTags();

// Can't to the below. The init here effect the init function within the mediatags class. 
/*
add_action('init','init_media_tags');
function init_media_tags() {
	global $wpnjmediatags;
	$wpnjmediatags = new MediaTags();
}
*/
?>