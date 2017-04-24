<?php
/**
 * Plugin Name: WP Functions
 * Plugin URI:
 * Description: WP Functions Plugin
 * Version:     0.0.01
 * Author:      WP Functions
 * Author URI:  https://wpfunctions.org
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


// Textdomain
function wpf_load_textdomain() {
  load_plugin_textdomain( 'wpfunctions', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'after_setup_theme', 'wpf_load_textdomain' );


// Flush Rewrite Rules
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

function wpf_flush_rewrites() {
  wpf_register_post_type_function();
	flush_rewrite_rules();
}

// Show only functions of the author and hide the other functions of other authors
function wpf_posts_for_current_author() {
  global $user_ID;
  if ( current_user_can( 'edit_others_pages' ) ) return;
  if ( ! isset( $_GET[ 'author' ] ) ) {
    wp_redirect( add_query_arg( 'author', $user_ID ) );
    exit;
  }
}
add_action( 'load-edit.php', 'wpf_posts_for_current_author' );

function test_cap_author() {
  $test = get_role( 'author' );
  var_dump( $test );
}

// Register post type 'function'
if ( ! function_exists('wpf_register_post_type_function') ) {
  function wpf_register_post_type_function() {
  	$labels = array(
  		'name'                  => _x( 'Functions', 'Post Type General Name', 'wpfunctions' ),
  		'singular_name'         => _x( 'Function', 'Post Type Singular Name', 'wpfunctions' ),
  		'menu_name'             => __( 'Functions', 'wpfunctions' ),
  		'name_admin_bar'        => __( 'Function', 'wpfunctions' ),
  		'archives'              => __( 'Function Archives', 'wpfunctions' ),
  		'attributes'            => __( 'Function Attributes', 'wpfunctions' ),
  		'parent_item_colon'     => __( 'Parent function:', 'wpfunctions' ),
  		'all_items'             => __( 'All Functions', 'wpfunctions' ),
  		'add_new_item'          => __( 'Add New Function', 'wpfunctions' ),
  		'add_new'               => __( 'Add New', 'wpfunctions' ),
  		'new_item'              => __( 'New Function', 'wpfunctions' ),
  		'edit_item'             => __( 'Edit Function', 'wpfunctions' ),
  		'update_item'           => __( 'Update Function', 'wpfunctions' ),
  		'view_item'             => __( 'View Function', 'wpfunctions' ),
  		'view_items'            => __( 'View Functions', 'wpfunctions' ),
  		'search_items'          => __( 'Search Function', 'wpfunctions' ),
  		'not_found'             => __( 'Not found', 'wpfunctions' ),
  		'not_found_in_trash'    => __( 'Not found in Trash', 'wpfunctions' ),
  		'featured_image'        => __( 'Featured Image', 'wpfunctions' ),
  		'set_featured_image'    => __( 'Set featured image', 'wpfunctions' ),
  		'remove_featured_image' => __( 'Remove featured image', 'wpfunctions' ),
  		'use_featured_image'    => __( 'Use as featured image', 'wpfunctions' ),
  		'insert_into_item'      => __( 'Insert into function', 'wpfunctions' ),
  		'uploaded_to_this_item' => __( 'Uploaded to this function', 'wpfunctions' ),
  		'items_list'            => __( 'Functins list', 'wpfunctions' ),
  		'items_list_navigation' => __( 'Functions list navigation', 'wpfunctions' ),
  		'filter_items_list'     => __( 'Filter functions list', 'wpfunctions' ),
  	);
  	$args = array(
  		'label'                 => __( 'Function', 'wpfunctions' ),
  		'description'           => __( 'Functions', 'wpfunctions' ),
  		'labels'                => $labels,
  		'supports'              => array( 'title', 'excerpt', 'author', ),
  		'hierarchical'          => false,
  		'public'                => true,
  		'show_ui'               => true,
  		'show_in_menu'          => true,
      'menu_icon'             => 'dashicons-text',
  		'menu_position'         => 5,
  		'show_in_admin_bar'     => true,
  		'show_in_nav_menus'     => true,
  		'can_export'            => true,
  		'has_archive'           => true,
  		'exclude_from_search'   => false,
  		'publicly_queryable'    => true,
  		'query_var'             => 'function',
  		'capability_type'       => 'post',
  	);
  	register_post_type( 'function', $args );
  }
  add_action( 'init', 'wpf_register_post_type_function', 0 );
}

function wpf_add_metabox_function( $post ) {
  add_meta_box(
    'function-meta-box',
    __( 'Function Settings', 'wpfunctions' ),
    'wpf_callback_function_meta_box',
    'function',
    'normal',
    'default'
  );
}
add_action( 'add_meta_boxes_function', 'wpf_add_metabox_function' );

function wpf_callback_function_meta_box() {
  global $post;
  $values = get_post_custom( $post->ID );
  //var_dump( $values );
  $text = isset( $values['function_code'] ) ? $values['function_code'][0] : '';
  //var_dump( $text );
  //var_dump( $test );
  ?>
  <?php // TODO: This is a temporary code highlighter ?>
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.9.0/styles/atom-one-dark.min.css">
  <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.9.0/highlight.min.js"></script>
  <script>hljs.initHighlightingOnLoad();</script>
  <label for="function_code">Code</label>
  <textarea name="function_code" id="function_code" rows="10" style="width: 100%"><?php echo $text; ?></textarea>
  <h3>Test Highlight Code Coloring</h3>
  <pre><code class="php">
    <?php echo $text; ?>
  </code></pre>
<?php }

function wpf_save_function( $post_id ) {
  //var_dump( $_POST['function_code'] );
  if( isset( $_POST['function_code'] ) )
    update_post_meta( $post_id, 'function_code', $_POST['function_code'] );
}
add_action( 'save_post', 'wpf_save_function' );

function wpf_wp_seek_list_functions() {
  $json_url = 'https://api.wpseek.com/1.1/wordpress/functions.json';
  //$json_url = plugin_dir_url( __FILE__ ) . 'functions.json';
  //var_dump( plugins_url() . '/wp-functions/' . $json_url );
  $json = file_get_contents( $json_url );
  $functions = json_decode( $json, true );
  //var_dump( $functions['items'] );
  echo '<h3>' . __( 'Used Functions', 'wpfunctions' ) . '</h3>';
  foreach ( $functions['items'] as $function ) {
    // $function_info_url = 'https://api.wpseek.com/1.1/wordpress/function/info/' . $function . '.json';
    // $function_info_json = file_get_contents( $function_info_url );
    // $function_info = json_decode( $function_info_json, true );
    // $function_url = $function_info['url'];
    //var_dump( $function_url );
    echo  '<a href="' . $fuction_url . '">' . $function . '</a>' . '<br/>';
  }
}
