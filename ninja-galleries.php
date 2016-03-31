<?php
/*
Plugin Name: Ninja Galleries
Plugin URI: http://wpninjas.net/plugins/ninja-galleries/
Description: Ninja Galleries lets you easily create image galleries by tagging your images and then assigning those tags to a gallery page.
Version: 1.0.20
Author: Kevin Stover
Author URI: http://www.wpninjas.net
*/

require_once("wpnj-media-tags/media_tags.php");
function wpnj_gallery_post_display($content){
	global $post;
	$this_type = get_post_type($post->ID);
	if($this_type == "wpnj_gallery"){
		$wpnj_img_size = "thumbnail";
		$test = wpnj_mediatags_load_master();
		if($test){
			foreach($test as $val){
				$wpnj_gal_field = "wpnj_gallery_" . $val->slug;
				$meta = get_post_meta($post->ID, $wpnj_gal_field, true);
				if($meta){
					if($wpnj_selected_tags == ""){
						$wpnj_selected_tags = $val->slug;
					}else{
						$wpnj_selected_tags .= ',' . $val->slug;
					}
				}

			}
			$media_items = wpnj_get_attachments_by_media_tags('media_tags=' . $wpnj_selected_tags);
			if($media_items){
				$content .= '<hr />';
				foreach($media_items as $media_item){
					//print_r($media_item);
					$page_uri = get_page_uri($post->ID);
					$image_src = wp_get_attachment_image_src($media_item->ID, $wpnj_img_size);
					$full_src = wp_get_attachment_image_src($media_item->ID, "full");
					$content .= '<dl class="gallery-item"><dt class="gallery-icon"><a title="'.$media_item->post_excerpt.'" rel="lightbox[ '.$page_uri.' ]" href="'.$full_src[0].'"><img src="'.$image_src[0].'"></a></dt></dl>';
					//$content .= '<img src="'.$image_src[0].'">';
				}
			}
		}
	}
	return $content;
}

add_filter("the_content", "wpnj_gallery_post_display");

function wpnj_gallery_build_tax(){
	register_taxonomy( 
	'wpnj_gallery_cat', 
	'wpnj_gallery', 
	array( 
		'hierarchical' => true,
		'label' => 'Gallery Category',
		'query_var' => true, 
		'rewrite' => true ) );  
}

add_action('init', 'wpnj_gallery_build_tax');

function wpnj_gallery_display_list($args){
	if($args['cat']){
		$cats_array = explode("," , $args['cat']);
		foreach($cats_array as $cat_title){
			$full_term  = get_term_by('name', $cat_title, 'wpnj_gallery_cat');
			//if($slugs == ""){
				//$slugs = $full_term->slug;
			//}else{
				//$slugs .= ','.$full_term->slug;
			//}
		$test = get_terms('wpnj_gallery_cat', 'slug='.$full_term->slug);
		//print_r($test);
		//echo $test[0]->slug;
		echo '<h2>'.$test[0]->name.'</h2>';
		$items = array(
			'post_type' => 'wpnj_gallery',
			'posts_per_page' => 10,
			'taxonomy' => 'wpnj_gallery_cat',
			'term' => $test[0]->slug
		);
		$loop = new WP_Query($items);
		echo '<div class="whole-gallery">';
		while ( $loop->have_posts() ){
			$loop->the_post();
			echo '<dl class="gallery-item"><dt class="gallery-icon"><a href="';
			the_permalink();
			echo '">';
			if(function_exists('the_post_thumbnail')){
					the_post_thumbnail('thumbnail');
			}
			echo '</a></dt><dd>';
			the_title();
			echo '</dd></dl>';
		}
		echo '</div>';
		}
	}elseif($args['gallery']){
		$items = array(
			'post_type' => 'wpnj_gallery',
			'name' => $args['gallery']
		);
		$loop = new WP_Query($items);
		echo '<div class="whole-gallery">';
		while ( $loop->have_posts() ){
			$loop->the_post();
			echo '<dl class="gallery-item"><dt class="gallery-icon"><a href="';
			the_permalink();
			echo '">';
			if(function_exists('the_post_thumbnail')){
					the_post_thumbnail('thumbnail');
			}
			echo '</a></dt><dd>';
			the_title();
			echo '</dd></dl>';
		}
		echo '</div>';
	}else{
		$test = get_terms('wpnj_gallery_cat');
		foreach($test as $val){
			echo '<h2>'.$val->name.'</h2>';
			$items = array(
				'post_type' => 'wpnj_gallery',
				'posts_per_page' => 10,
				'taxonomy' => 'wpnj_gallery_cat',
				'term' => $val->slug
			);
			$loop = new WP_Query($items);
			echo '<div class="whole-gallery">';
			while ( $loop->have_posts() ){
				$loop->the_post();
				echo '<dl class="gallery-item"><dt class="gallery-icon"><a href="';
				the_permalink();
				echo '">';
				if(function_exists('the_post_thumbnail')){
					the_post_thumbnail('thumbnail');
				}
				echo '</a></dt><dd>';
				the_title();
				echo '</dd></dl>';
			}
			echo '</div>';
		}
	}
	//Reset Query
	wp_reset_query();
}

add_shortcode('wpnj_gal_list', 'wpnj_gallery_display_list');

//Custom Post Type Stuff:
add_action('init', 'wpnj_gallery_custom_post_type');
function wpnj_gallery_custom_post_type() 
{
  $labels = array(
    'name' => _x('Galleries', 'post type general name'),
    'singular_name' => _x('Gallery', 'post type singular name'),
    'add_new' => _x('Add New', 'wpnj_gallery'),
    'add_new_item' => __('Add New Gallery'),
    'edit_item' => __('Edit Gallery'),
    'new_item' => __('New Gallery'),
    'view_item' => __('View Gallery'),
    'search_items' => __('Search Galleries'),
    'not_found' =>  __('No Galleries found'),
    'not_found_in_trash' => __('No Galleries found in Trash'), 
    'parent_item_colon' => ''
  );
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
	'_builtin' => false, // It's a custom post type, not built in!
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'hierarchical' => false,
    'menu_position' => null,
	'rewrite' => array("slug" => "gallery"), // Permalinks format
	'menu_icon' => get_stylesheet_directory_uri() . '/images/icon-gallery.png',
    'supports' => array('title','editor','thumbnail'),
	'taxonomies' => array('') // this is IMPORTANT
  ); 
  register_post_type('wpnj_gallery',$args);
 }



//add filter to insure the text Book, or book, is displayed when user updates a book 
add_filter('post_updated_messages', 'wpnj_gallery_updated_messages');
function wpnj_gallery_updated_messages( $messages ) {

  $messages['wpnj_gallery'] = array(
    0 => '', // Unused. Messages start at index 1.
    1 => sprintf( __('Gallery updated. <a href="%s">View Gallery</a>'), esc_url( get_permalink($post_ID) ) ),
    2 => __('Custom field updated.'),
    3 => __('Custom field deleted.'),
    4 => __('Gallery updated.'),
    /* translators: %s: date and time of the revision */
    5 => isset($_GET['revision']) ? sprintf( __('Gallery restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    6 => sprintf( __('Gallery published. <a href="%s">View Gallery</a>'), esc_url( get_permalink($post_ID) ) ),
    7 => __('Book saved.'),
    8 => sprintf( __('Gallery submitted. <a target="_blank" href="%s">Preview Gallery</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    9 => sprintf( __('Gallery scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Gallery</a>'),
      // translators: Publish box date format, see http://php.net/date
      date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
    10 => sprintf( __('Gallery draft updated. <a target="_blank" href="%s">Preview Gallery</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
  );

  return $messages;
}

//display contextual help for Books
add_action( 'contextual_help', 'add_help_text', 10, 3 );

function add_help_text($contextual_help, $screen_id, $screen) { 
  //$contextual_help .= var_dump($screen); // use this to help determine $screen->id
  if ('wpnj_gallery' == $screen->id ) {
    $contextual_help =
      '<p>' . __('Things to remember when adding or editing a Gallery:') . '</p>' .
      '<ul>' .
      '<li>' . __('Specify the correct genre such as Mystery, or Historic.') . '</li>' .
      '<li>' . __('Specify the correct writer of the book.  Remember that the Author module refers to you, the author of this book review.') . '</li>' .
      '</ul>' .
      '<p>' . __('If you want to schedule the book review to be published in the future:') . '</p>' .
      '<ul>' .
      '<li>' . __('Under the Publish module, click on the Edit link next to Publish.') . '</li>' .
      '<li>' . __('Change the date to the date to actual publish this article, then click on Ok.') . '</li>' .
      '</ul>' .
      '<p><strong>' . __('For more information:') . '</strong></p>' .
      '<p>' . __('<a href="http://codex.wordpress.org/Posts_Edit_SubPanel" target="_blank">Edit Posts Documentation</a>') . '</p>' .
      '<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>' ;
  } elseif ( 'edit-book' == $screen->id ) {
    $contextual_help = 
      '<p>' . __('This is the help screen displaying the table of books blah blah blah.') . '</p>' ;
  }
  return $contextual_help;
}

add_filter("manage_edit-wpnj_gallery_columns", "wpnj_gallery_edit_columns");
add_action("manage_posts_custom_column",  "wpnj_gallery_custom_columns");

function wpnj_gallery_edit_columns($columns){
		$columns = array(
			"cb" => "<input type=\"checkbox\" />",
			"thumbnail" => "Thumbnail",
			"title" => "Gallery",
			"description" => "Description",
			"tags" => "Tags",
		);

		return $columns;
}

function wpnj_gallery_custom_columns($column){
		global $post;
		switch ($column)
		{
			case "thumbnail":
				$custom = get_post_custom();
				if(function_exists('the_post_thumbnail')){
					echo the_post_thumbnail(array('100px', '100px'));
				}
				break;
			case "description":
				$custom = get_post_custom();
				echo $post->post_content;
				break;
			case "tags":
				$custom = get_post_custom();
				echo get_the_tag_list( '', ', ' );
				break;
		}
}

add_action('admin_menu', 'mytheme_add_box2');

// Add meta box
function mytheme_add_box2() {
	global $wpnj_master_tags, $meta_box2;
	$wpnj_test = wpnj_mediatags_load_master();
	//print_r($test);
	$prefix = "wpnj_gallery_";
	$wpnj_master_tags = array();
	if($wpnj_test){
		foreach($wpnj_test as $wpnj_var){
			$new_item = array(
					'name' => $wpnj_var->name,
					'desc' => $wpnj_var->desc,
					'id' => $prefix . $wpnj_var->slug,
					'type' => 'checkbox',
					'std' => ''
			);
			$wpnj_master_tags[] = $new_item;
		}
	}

	$meta_box2 = array(
		'id' => 'gal',
		'title' => 'Add Images Tagged With:',
		'page' => 'wpnj_gallery',
		'context' => 'normal',
		'priority' => 'high',
		'fields' => $wpnj_master_tags

	);

	
	add_meta_box($meta_box2['id'], $meta_box2['title'], 'mytheme_show_box2', $meta_box2['page'], $meta_box2['context'], $meta_box2['priority']);
}

// Callback function to show fields in meta box
function mytheme_show_box2() {
	global $post, $wpnj_master_tags, $meta_box2;
		
	// Use nonce for verification
	echo '<input type="hidden" name="mytheme_meta_box2_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
	
	echo '<table class="form-table">';

	foreach ($meta_box2['fields'] as $field) {
		// get current post meta data
		$meta = get_post_meta($post->ID, $field['id'], true);
		
		echo '<tr>',
				'<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
				'<td>';
		switch ($field['type']) {
			case 'text':
				echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />',
					'<br />', $field['desc'];
				break;
			case 'textarea':
				echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>',
					'<br />', $field['desc'];
				break;
			case 'select':
				echo '<select name="', $field['id'], '" id="', $field['id'], '">';
				foreach ($field['options'] as $option) {
					echo '<option', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
				}
				echo '</select>';
				break;
			case 'radio':
				foreach ($field['options'] as $option) {
					echo '<input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'];
				}
				break;
			case 'checkbox':
				echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
				break;
		}
		echo 	'<td>',
			'</tr>';
	}
	
	echo '</table>';
}

add_action('save_post', 'mytheme_save_data2');

// Save data from meta box
function mytheme_save_data2($post_id) {
	global $meta_box2;
	
	// verify nonce
	if (!wp_verify_nonce($_POST['mytheme_meta_box2_nonce'], basename(__FILE__))) {
		return $post_id;
	}

	// check autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $post_id;
	}

	// check permissions
	if ('page' == $_POST['post_type']) {
		if (!current_user_can('edit_page', $post_id)) {
			return $post_id;
		}
	} elseif (!current_user_can('edit_post', $post_id)) {
		return $post_id;
	}
	
	foreach ($meta_box2['fields'] as $field) {
		$old = get_post_meta($post_id, $field['id'], true);
		$new = $_POST[$field['id']];
		
		if ($new && $new != $old) {
			update_post_meta($post_id, $field['id'], $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, $field['id'], $old);
		}
	}
}

//----End Gallery Custom Post Type

include_once ( dirname(__FILE__) . "/wpnj-media-tags/media_tags.php");