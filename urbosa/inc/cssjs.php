<?php
/* ----------------------------- */
$live = false;
/* ----------------------------- */


global $suffix; $suffix = ''; if(!$live) $suffix ='?'.time();
function theme_setup()
{
  global $suffix;
  // JS
  wp_enqueue_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?key=', array(), null, false);
  wp_enqueue_script('lib', get_template_directory_uri() . "/assets/dist/js/bundle.js", array(), null, false);   // SemanticUI/JQuery/Slick
  wp_enqueue_script('custom', get_template_directory_uri() . "/assets/js/custom.js$suffix" , array(), null, true);       // Any custom/override changes
  wp_enqueue_script('blocks', get_template_directory_uri() . "/assets/js/blocks.js$suffix" , array(), null, true);
  
  // CSS
  wp_enqueue_style('main', get_template_directory_uri() . "/assets/dist/css/layout.css$suffix" , array(), null, false); // Main critical layout
  wp_enqueue_style('other', get_template_directory_uri() . "/assets/dist/css/style.css$suffix" , array(), null, false); // Other layouts/blocks/page specifics/elements etc
  wp_enqueue_style('custom', get_template_directory_uri() . "/assets/css/custom.css$suffix" , array(), null, false);    // Custom CSS
  
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
  global $suffix;
  wp_enqueue_script('jQuery');
  wp_enqueue_style('admin-template', get_template_directory_uri() . "/assets/dist/css/template.css$suffix" , array(), null, false); // This holds resources/fonts/grids/theme/colors
  wp_enqueue_style('admin-blocks', get_template_directory_uri() . "/assets/dist/css/blocks.css$suffix" , array(), null, false);
  wp_enqueue_script('admin-blocks', get_template_directory_uri() . "/assets/js/blocks.js$suffix" , array('jQuery'), null, true);
  
  // Inject local js
  wp_localize_script('admin-blocks', 'websiteData', array(
    'is_admin' => is_admin(),
  ));
}
add_action( 'admin_enqueue_scripts', 'load_admin_style' );
function add_style_sheet_attr($html, $handle){
  // Preload main critical layout
  if($handle == ('main' || 'other' || 'dashicons')){
    $tempHtml  =str_replace("rel='stylesheet'","rel='preload' as='style'",$html);
    $tempHtml .=str_replace("rel='stylesheet'","rel='stylesheet' media='print' onload=\"this.media='all'\"",$html);
    $tempHtml  = str_replace("media=''",'',$tempHtml);
    return $tempHtml;
  }
  return $html;
}
add_filter('style_loader_tag', 'add_style_sheet_attr', 10,2);
function defer_js($url,$handle){
  if(is_user_logged_in() || 
    strpos($url, '.js') === false || 
    strpos($url, 'jquery') || 
    $handle==('lib')
    ){
    return $url;
  }
  return str_replace(' src', ' async defer src', $url);
}
add_filter('script_loader_tag', 'defer_js', 11, 2);
//ACF Google Map API Key
function my_acf_google_map_api( $api ){
	$api['key'] = '';
	return $api;
}
add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');


/* Optimisation */
$minifyJS  =['urbosa'];
$minifyCSS =['urbosa'];
require_once('lib/minify.html.php');
require_once('lib/minify.js.css.php');

