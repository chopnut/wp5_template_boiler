<?php

//===============================================
if (!function_exists('widget_theme_feature')) {
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
function urbosa_theme_setup()
{
  add_theme_support('align-wide');
  add_theme_support('post-thumbnails'); // enable feature image
  add_theme_support('title-tag' );
  add_theme_support('woocommerce' );

  $logoDefaults = array(
    'height'      => 100,
    'width'       => 400,
    'flex-height' => true,
    'flex-width'  => true,
    
    );
  add_theme_support( 'custom-logo', $logoDefaults );
  add_image_size( 'urbosa_size', 2000, 2000 );
  register_nav_menu('main', __('My main menu'));
}
add_action('after_setup_theme', 'urbosa_theme_setup');
function urbosa_menu_by_location($themeLocation = 'main', $containerClass=''){
  wp_nav_menu(array('theme_location' => $themeLocation,'container_class'=>$containerClass)); 
}
