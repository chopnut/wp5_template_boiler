<?php


//--------------------------------------------------//
/*        Resources and Admin Logo                  */
//--------------------------------------------------//

function theme_setup()
{
  $suffix = get_theme_suffix();

  wp_enqueue_script('lib', get_template_directory_uri() . "/assets/dist/js/bundle.js", array(), null, false);  
  wp_enqueue_style('template', get_template_directory_uri() . "/assets/dist/css/template.css$suffix"); 
  wp_enqueue_style('main', get_template_directory_uri() . "/assets/dist/css/layout.css$suffix");
  wp_enqueue_style('other', get_template_directory_uri() . "/assets/dist/css/style.css$suffix"); // fonts/pages/single/blocks

  
  add_feature(array('parallax','font-awesome','google-map','lightbox'));
  // add_feature('debug');

  wp_enqueue_script('blocks', get_template_directory_uri() . "/assets/js/blocks.js$suffix" , array(), null, true);
  wp_enqueue_script('custom', get_template_directory_uri() . "/assets/js/custom.js$suffix" , array('lib'), null, true);       

  // local object
  wp_localize_script('custom', 'websiteData', array( 
      'is_search' => is_search(),
      'stylesheet_directory_uri' => get_stylesheet_directory_uri()
    )
  );
}
add_action('wp_enqueue_scripts', 'theme_setup');
function load_admin_style()
{
  $suffix = get_theme_suffix();
  $screen = get_current_screen();

  if($screen->is_block_editor){
    wp_enqueue_style ('admin-template', get_template_directory_uri() . "/assets/dist/css/template.css$suffix" , array(), null, false); // This holds resources/fonts/grids/theme/colors
    wp_enqueue_style ('admin-blocks', get_template_directory_uri() . "/assets/dist/css/blocks.css$suffix" , array(), null, false);
  }
  wp_enqueue_style ('admin-style', get_template_directory_uri() . "/assets/dist/css/admin.css$suffix" , array(), null,false);
  wp_enqueue_script('admin-blocks', get_template_directory_uri() . "/assets/js/blocks.js$suffix" , array('jquery'), null, true);
  wp_enqueue_script('admin-helper', get_template_directory_uri() . "/assets/lib/helper.js$suffix" , array('jquery'), null, true);
  wp_enqueue_script('admin-js', get_template_directory_uri() . "/assets/js/admin.js$suffix" , array('jquery'), null, true);
  
  // local object
  wp_localize_script('admin-js', 'websiteData', array( 'is_admin' => is_admin(), 'is_theme_live'=> get_option('urbosa_theme_status')));
  add_feature('google-map');

}
add_action( 'admin_enqueue_scripts', 'load_admin_style' );








