<?php 
//--------------------------------------------------//
/*        Resource loader optimisation              */
//--------------------------------------------------//
function is_theme_live(){
    return get_option('urbosa_theme_status');
}
function get_theme_suffix(){
    $live = get_option('urbosa_theme_status');
    $suffix = ''; if(!$live) $suffix ='?'.time();
    return $suffix;
}
function add_feature($feature, $option=''){

    switch($feature){
        case 'googlemap':
            wp_enqueue_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?key='.$option, array(), null, false);
            add_filter('acf/fields/google_map/api', function($api) use (&$option){
                $api['key'] = $option;
                return $api;
            });
        break;
        case 'parallax':
            wp_enqueue_script('parallax', get_template_directory_uri() . "/assets/lib/simpleParallax.min.js" , array('lib'), null, true);
        break;
        case 'lightbox':
            wp_enqueue_script('simplelightbox', get_template_directory_uri() . "/assets/lib/simpleLightBox/simpleLightBox.min.js" , array(), null, true);
        break;
        case 'progressive':
            enableProgressiveBG();
        break;
        case 'debug_me':
            wp_enqueue_style('debug', get_template_directory_uri() . "/assets/css/debug.css" , array(), null, false);    // Custom CSS

        break;
        default;
    }
}

function add_style_sheet_attr($html, $handle){
    switch($handle){
      case 'main':
        // Preload main critical layout
        $tempHtml  =str_replace("rel='stylesheet'","rel='preload' as='style'",$html);
        $tempHtml .=str_replace("rel='stylesheet'","rel='stylesheet' media='print' onload=\"this.media='all'\"",$html);
        $tempHtml  = str_replace("media=''",'',$tempHtml);
        return $tempHtml;
      case 'dashicons':
      case 'other':
      case 'custom':
      case 'template':
        // Async other CSS
        $tempHtml  = str_replace("rel='stylesheet'","rel='stylesheet' media='print' onload=\"this.media='all'\"",$html);
        $tempHtml  = str_replace("media=''",'',$tempHtml);
        return $tempHtml;
      break;
    }
    return $html;
  }
  add_filter('style_loader_tag', 'add_style_sheet_attr', 10,2);
  function defer_js($url,$handle){
    if(is_user_logged_in() || 
      strpos($url, '.js') === false || 
      strpos($url, 'jquery') || 
      $handle=='lib'
      ){
      return $url;
    }
    if($handle=='custom') return str_replace(' src', ' defer src', $url);
    return str_replace(' src', ' async defer src', $url);
  }
  add_filter('script_loader_tag', 'defer_js', 11, 2);
  function deregister_dashicons()    {  wp_deregister_style( 'dashicons' ); }
  add_action( 'wp_print_styles', 'deregister_dashicons' );
  function urbosa_add_custom_to_footer() {
    $suffix = get_theme_suffix();
    wp_enqueue_style('dashicons','/wp-includes/css/dashicons.min.css'); 
    wp_enqueue_style('other', get_template_directory_uri() . "/assets/dist/css/style.css$suffix" , array(), null, false); // Other layouts/blocks/page specifics/elements etc
    wp_enqueue_style('custom', get_template_directory_uri() . "/assets/css/custom.css$suffix" , array(), null, false);    // Custom CSS
    wp_enqueue_style('simplelightbox', get_template_directory_uri() . "/assets/lib/simpleLightBox/simpleLightBox.min.css$suffix" , array(), null, false);    // Custom CSS
  };
  add_action( 'get_footer', 'urbosa_add_custom_to_footer' );

//--------------------------------------------------//
/*                    Others                        */
//--------------------------------------------------//

$minifyJS  =['urbosa']; $minifyCSS =['urbosa'];
$dir  = __DIR__.'/../../assets/lib/minify';
require_once($dir.'/minify.html.php');
require_once($dir.'/minify.js.css.php');

?>