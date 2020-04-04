<?php
function theme_setup()
{
  // JS
  wp_enqueue_script('lib', get_template_directory_uri() . "/assets/dist/js/bundle.js", array('wp-element'), null, true);   // SemanticUI/JQuery/Slick
  wp_enqueue_script('custom', get_template_directory_uri() . "/assets/js/custom.js?" . time(), array(), null, true);       // Any custom/override changes
  // CSS
  wp_enqueue_style('main', get_template_directory_uri() . "/assets/dist/css/layout.css?" . time(), array(), null, false); // Main critical layout
  wp_enqueue_style('other', get_template_directory_uri() . "/assets/dist/css/style.css?" . time(), array(), null, false); // Other layouts/blocks/page specifics/elements etc
  wp_enqueue_style('custom', get_template_directory_uri() . "/assets/css/custom.css?" . time(), array(), null, false);    // Custom CSS
  // Enable dashicons
  wp_enqueue_style('dashicons'); 
  // Inject local js
  wp_localize_script('custom', 'websiteData', array(
    'is_search' => is_search(),
  ));
}
add_action('wp_enqueue_scripts', 'theme_setup');
function load_admin_style()
{
  wp_enqueue_script('jquery');
  wp_enqueue_style('admin-template', get_template_directory_uri() . "/assets/dist/css/template.css?" . time(), array(), null, false); // This holds resources/fonts/grids/theme/colors
  // Block specifics
  wp_enqueue_style('admin-blocks', get_template_directory_uri() . "/assets/dist/css/blocks.css?" . time(), array(), null, false);
  wp_enqueue_script('admin-blocks', get_template_directory_uri() . "/assets/js/blocks.js?" . time(), array(), null, true);
  // Inject local js
  wp_localize_script('admin-blocks', 'websiteData', array(
    'is_admin' => is_admin(),
  ));
}
add_action( 'admin_enqueue_scripts', 'load_admin_style' );
function add_style_sheet_attr($html, $handle){
  // Preload main critical layout
  if('main'==$handle){
    $tempHtml  =str_replace("rel='stylesheet'","rel='preload' as='style'",$html);
    $tempHtml .=str_replace("rel='stylesheet'","rel='stylesheet' media='print' onload=\"this.media='all'\"",$html);
    $tempHtml  = str_replace("media=''",'',$tempHtml);
    return $tempHtml;
  }
  return $html;
}
add_filter('style_loader_tag', 'add_style_sheet_attr', 10,2);

