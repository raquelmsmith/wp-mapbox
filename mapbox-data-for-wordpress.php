<?php
/*
	Plugin Name: Mapbox Data for WordPress
	Plugin URI: TBD
	Description: A plugin to manage Mapbox data in a WordPress install
	Version: 0.0.1
	Author: Raquel M Smith
	Author URI: http://raquelmsmith.com
	License: GPL2
*/

add_action( 'init', 'mapbox_data_init' );

/**
	* Register a Map Data post type.
*/

function mapbox_data_init() {
	$labels = array(
		'name'					=> _x( 'Map Data', 'post type general name', 'your-plugin-textdomain' ),
		'singular_name'			=> _x( 'Map Data Point', 'post type singular name', 'your-plugin-textdomain' ),
		'menu_name'				=> _x( 'Map Data', 'admin menu', 'your-plugin-textdomain' ),
		'name_admin_bar'		=> _x( 'Map Data Point', 'add new on admin bar', 'your-plugin-textdomain' ),
		'add_new'				=> _x( 'Add New', 'data point', 'your-plugin-textdomain' ),
		'add_new_item'			=> __( 'Add New Data Point', 'your-plugin-textdomain' ),
		'new_item'				=> __( 'New Data Point', 'your-plugin-textdomain' ),
		'edit_item'				=> __( 'Edit Data Point', 'your-plugin-textdomain' ),
		'view_item'				=> __( 'View Data Point', 'your-plugin-textdomain' ),
		'all_items'				=> __( 'All Map Data Points', 'your-plugin-textdomain' ),
		'search_items'			=> __( 'Search Map Data Points', 'your-plugin-textdomain' ),
		'parent_item_colon'		=> __( 'Parent Map Data Points:', 'your-plugin-textdomain' ),
		'not_found'				=> __( 'No Map Data Points found.', 'your-plugin-textdomain' ),
		'not_found_in_trash'	=> __( 'No Map Data Points found in Trash.', 'your-plugin-textdomain' )
	);
	$args = array(
		'labels'				=> $labels,
		'public'				=> true,
		'publicly_queryable'	=> true,
		'show_ui'				=> true,
		'show_in_menu'			=> true,
		'query_var'				=> true,
		'rewrite'				=> array( 'slug' => 'map-data' ),
		'capability_type'		=> 'post',
		'has_archive'			=> true,
		'Hierarchical'			=> false,
		'menu_icon'				=> 'dashicons-location-alt',
		'menu_position'			=> 5,
		'show_in_rest'			=> true,
		'taxonomies'			=> array('category', 'post_tag'),
		'supports'				=> array( 
			'title', 
			'editor',
			'author',
			'thumbnail',  
			'revisions' )
	);
	register_post_type( 'map_data_point', $args );
}

/**
 * Add meta boxes for Year and Location
 *
 * @param post $post The post object
 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/add_meta_boxes
 */
function map_data_point_add_meta_boxes( $post ){
	add_meta_box( 'map_data_point_year', 'Year', 'map_data_point_year_build_meta_box', 'map_data_point', 'side', 'low' );
	add_meta_box( 'map_data_point_location', 'Location', 'map_data_point_location_build_meta_box', 'map_data_point', 'normal', 'low' );
}
add_action( 'add_meta_boxes_map_data_point', 'map_data_point_add_meta_boxes' );

/**
 * Build custom field meta boxes for Year and Location
 *
 * @param post $post The post object
 */
function map_data_point_year_build_meta_box( $post ){
	wp_nonce_field( basename( __FILE__ ), 'map_data_point_meta_box_nonce' );
	$mapDataPoint_Year = get_post_meta( $post->ID, '_map_data_point_year', true );
	?>
	<input type="text" name="year" value="<?php echo $mapDataPoint_Year; ?>" /> 
	<?php
}

function map_data_point_location_build_meta_box( $post ){
	wp_nonce_field( basename( __FILE__ ), 'map_data_point_meta_box_nonce' );
	$mapDataPoint_Location = get_post_meta( $post->ID, '_map_data_point_location', true );
	?>
	<input type="text" name="location" value="<?php echo $mapDataPoint_Location; ?>" /> 
	<?php
}

/**
 * Store custom field meta box data
 *
 * @param int $post_id The post ID.
 */
function map_data_point_save_meta_boxes_data( $post_id ){
	if ( !isset( $_POST['map_data_point_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['map_data_point_meta_box_nonce'], basename( __FILE__ ) ) ){
		return;
	}
	// Check the user's permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ){
		return;
	}
	if ( isset( $_REQUEST['year'] ) ) {
		update_post_meta( $post_id, '_map_data_point_year', sanitize_text_field( $_POST['year'] ) );
	}
	if ( isset( $_REQUEST['location'] ) ) {
		update_post_meta( $post_id, '_map_data_point_location', sanitize_text_field($_POST['location'] ) );
	}
}
add_action( 'save_post_map_data_point', 'map_data_point_save_meta_boxes_data', 10, 2 );