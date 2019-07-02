<?php 

// Adding menu location
function wpb_custom_new_menu() {
  register_nav_menu('main-menu',__( 'My main menu' ));
  add_theme_support( 'post-thumbnails' ); // enable feature image
}
add_action( 'init', 'wpb_custom_new_menu' );

// Adding footer widgets
add_action('widgets_init', 'widget_theme_feature');
function widget_theme_feature(){
  // Adding a widget area
  register_sidebar(
    array(
      'name' => 'Footer section',
      'id' => 'footer_section',
    )
  );
}
// Register phone number to the general settings
function register_my_setting() {
  add_settings_field( // To display the input field
    'phone-number-id',
    'Phone number',
    'myprefix_setting_callback_function',
    'general',
    'default',
    array( 'phone_number_name' )
  );
  register_setting('general','phone_number_name', 'esc_attr'); // Register the actual field ID
} 

// Print input field to
function myprefix_setting_callback_function($args){
  $option = get_option($args[0]);
  echo '<input type="text" id="'. $args[0] .'" name="'. $args[0] .'" value="' . $option . '" />';
}
add_action( 'admin_init', 'register_my_setting' );