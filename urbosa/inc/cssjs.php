<?php
//--------------------------------------------------//
$live = false;
//--------------------------------------------------//
/*        Main Resource Injection                   */
//--------------------------------------------------//
global $suffix; $suffix = ''; if(!$live) $suffix ='?'.time();
function theme_setup()
{
  global $suffix;
  // js
  // wp_enqueue_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?key=', array(), null, false);
  wp_enqueue_script('lib', get_template_directory_uri() . "/assets/dist/js/bundle.js", array(), null, false);   // SemanticUI/JQuery/Slick
  
  // features
  // wp_enqueue_script('parallax', get_template_directory_uri() . "/assets/lib/simpleParallax.min.js$suffix" , array('lib'), null, true);

  // essentials
  wp_enqueue_script('blocks', get_template_directory_uri() . "/assets/js/blocks.js$suffix" , array(), null, true);
  wp_enqueue_script('custom', get_template_directory_uri() . "/assets/js/custom.js$suffix" , array(), null, true);       // Any custom/override changes
  
  // css
  wp_enqueue_style('template', get_template_directory_uri() . "/assets/dist/css/template.css$suffix" , array(), null, false); // Fonts/Common elements
  wp_enqueue_style('main', get_template_directory_uri() . "/assets/dist/css/layout.css$suffix" , array(), null, false); // Main critical layout
  
  // local object
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
  
  // local object
  wp_localize_script('admin-blocks', 'websiteData', array(
    'is_admin' => is_admin(),
  ));
}
add_action( 'admin_enqueue_scripts', 'load_admin_style' );
function urbosa_login_page()
{
  global $suffix;
  ?>
  <style type="text/css">
    #login h1 a,
    .login h1 a {
      background-image: url(<?php echo get_template_directory_uri(); ?>/assets/img/sample.jpg);
      width: 200px;
      background-size: contain;
      background-repeat: no-repeat;
    }
    body.login {
      background-color: #eee;
    }
  </style>
  <script>var homeURL = '<?=home_url('/')?>';</script>
<?php
  wp_enqueue_script('lib', get_template_directory_uri() . "/assets/dist/js/bundle.js", array(), null, false);   // SemanticUI/JQuery/Slick
  wp_enqueue_script('custom', get_template_directory_uri() . "/assets/js/custom.js$suffix" , array(), null, true);       // Any custom/override changes
}
add_action('login_enqueue_scripts', 'urbosa_login_page');

//--------------------------------------------------//
/*        Resource loader optimisation              */
//--------------------------------------------------//
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
  global $suffix;
  wp_enqueue_style('dashicons','/wp-includes/css/dashicons.min.css'); 
  wp_enqueue_style('other', get_template_directory_uri() . "/assets/dist/css/style.css$suffix" , array(), null, false); // Other layouts/blocks/page specifics/elements etc
  wp_enqueue_style('custom', get_template_directory_uri() . "/assets/css/custom.css$suffix" , array(), null, false);    // Custom CSS
};
add_action( 'get_footer', 'urbosa_add_custom_to_footer' );


$minifyJS  =['urbosa']; $minifyCSS =['urbosa'];
$dir  = __DIR__.'/../assets/lib/minify';
require_once($dir.'/minify.html.php');
require_once($dir.'/minify.js.css.php');


//--------------------------------------------------//
/*                    Others                        */
//--------------------------------------------------//
//ACF Google Map API Key
function my_acf_google_map_api( $api ){
	$api['key'] = '';
	return $api;
}
add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');
enableProgressiveBG();