<?php

//===============================================
if (!function_exists('widget_theme_feature')) {
  // Adding footer widgets
  function widget_theme_feature()
  {
    // Adding a widget area
    register_sidebar(
      array(
        'name' => 'Footer section',
        'id' => 'footer_section',
      )
    );
     // This will enable excerpt on a page type.
    add_post_type_support( 'page', 'excerpt' );
  }
  add_action('widgets_init', 'widget_theme_feature');
}
//===============================================
if (!function_exists('register_my_setting')) {
  // Register phone number to the general settings
  function register_my_setting()
  {
    add_settings_field( // To display the input field
      'phone-number-id',
      'Phone number',
      'myprefix_setting_callback_function',
      'general',
      'default',
      array('phone_number_name')
    );
    register_setting('general', 'phone_number_name', 'esc_attr'); // Register the actual field ID
  }
  //===============================================
  // Print input field to
  function myprefix_setting_callback_function($args)
  {
    $option = get_option($args[0]);
    echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" />';
  }
  add_action('admin_init', 'register_my_setting');
}
//===============================================
function urbosa_theme_setup()
{
  add_theme_support('align-wide');
  add_theme_support('post-thumbnails'); // enable feature image
  add_theme_support('title-tag' );
  add_theme_support('woocommerce' );
  add_image_size( 'urbosa_size', 1500, 1500 );
  register_nav_menu('main-menu', __('My main menu'));
}
add_action('after_setup_theme', 'urbosa_theme_setup');
function urbosa_menu_by_location($themeLocation = 'main-menu'){
  wp_nav_menu(array('theme_location' => $themeLocation,'container_class'=>'nav-container')); 
}
//===============================================
//  How to search using rest api with JS
/**
 * Use this EP: /wp-json/wp/v2/search?search=sample
 * reference: https://developer.wordpress.org/rest-api/reference/search-results/
 */
if(function_exists('acf_add_options_page')){
  acf_add_options_page();
}
//===============================================
// Adds reusable blocks menu to the admin
function be_reusable_blocks_admin_menu() {
  add_menu_page( 'Reusable Blocks', 'Reusable Blocks', 'edit_posts', 'edit.php?post_type=wp_block', '', 'dashicons-editor-table', 22 );
}
add_action( 'admin_menu', 'be_reusable_blocks_admin_menu' );