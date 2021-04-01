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
function _add_feature($feature, $option=''){
  switch($feature){
    case 'google-map':
      if(function_exists('get_field')){
        $option = get_field('google_map_api_key', 'options');
        if(!empty($option)){
          if(!is_admin()){
            wp_enqueue_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?key='.$option, array(), null, false);
          }else{
            add_filter('acf/fields/google_map/api', function($api) use (&$option){
                $api['key'] = $option;
                return $api;
            });
          }
        }
      }
    break;
    case 'parallax':
        wp_enqueue_script('parallax', get_template_directory_uri() . "/assets/lib/simpleParallax.min.js" , array('lib'), null, true);
    break;
    case 'lightbox':
        wp_enqueue_script('simplelightbox', get_template_directory_uri() . "/assets/lib/simpleLightBox/simpleLightBox.min.js" , array(), null, true);
      break;
      case 'debug':
        wp_enqueue_style('debug', get_template_directory_uri() . "/assets/css/debug.css" , array(), null, false);    // Custom CSS
      break;
      case 'font-awesome':
        wp_enqueue_script('fontawesome', "https://use.fontawesome.com/releases/v5.3.1/js/all.js" , array(), null, true);
      break;
      case 'dashicons':
        add_action( 'get_footer', '_add_dashicons_footer');
      break;
      case 'aos':
        wp_enqueue_script('aosjs', "https://unpkg.com/aos@2.3.1/dist/aos.js" , array(), null, false);       
        wp_enqueue_style('aoscss', "https://unpkg.com/aos@2.3.1/dist/aos.css");
        break;
      case 'anime': 
        wp_enqueue_script('animejs', get_template_directory_uri() . "/assets/lib/anime.min.js" , array('lib'), null, true);       
        break;
    default;
  }
}
function _add_dashicons_footer(){
  wp_enqueue_style('dashicons','/wp-includes/css/dashicons.min.css'); 

}
function add_feature($features,$option=''){
  if(is_array($features)){
    foreach ($features as $tmpFeature) {
      $feature = $tmpFeature;
      $option = '';

      if(is_array($feature) && count($feature)){
        $feature = $feature[0];
        if(isset($feature[1])) $option = $feature[1];
      }
      _add_feature($feature, $option);

    }
  }else{
    _add_feature($features, $option);
  }
}

function add_style_sheet_attr($html, $handle){
    switch($handle){
      case 'main':
      case 'template':
      case 'other':
        
        // Preload main critical layout
        // $tempHtml  =str_replace("rel='stylesheet'","rel='preload' as='style'",$html);
        // $tempHtml .=str_replace("rel='stylesheet'","rel='stylesheet' media='print' onload=\"this.media='all'\"",$html);
        // $tempHtml  = str_replace("media=''",'',$tempHtml);

        return $html;
      break;
      case 'dashicons':
      case 'custom':
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
  function urbosa_add_custom_to_footer() {
    $suffix = get_theme_suffix();
    wp_enqueue_style('simplelightbox', get_template_directory_uri() . "/assets/lib/simpleLightBox/simpleLightBox.min.css$suffix" , array(), null, false);    // Custom CSS
  };
  add_action( 'get_footer', 'urbosa_add_custom_to_footer' );


  function urbosa_wp_head(){
    ?>

    <?php
  }
  add_action('wp_head', 'urbosa_wp_head');
//--------------------------------------------------//
/*                    Others                        */
//--------------------------------------------------//

$minifyJS  =['urbosa']; $minifyCSS =['urbosa'];
$dir  = __DIR__.'/../../assets/lib/minify';
require_once($dir.'/minify.html.php');
require_once($dir.'/minify.js.css.php');

?>