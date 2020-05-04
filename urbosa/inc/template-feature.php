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
  add_image_size( 'urbosa_size', 2000, 2000 );
  register_nav_menu('main-menu', __('My main menu'));
}
add_action('after_setup_theme', 'urbosa_theme_setup');
function urbosa_menu_by_location($themeLocation = 'main-menu', $menuClass='menu'){
  wp_nav_menu(array('theme_location' => $themeLocation,'menu_class'=>$menuClass)); 
}

//===============================================
// Add option page
if(function_exists('acf_add_options_page')){
  acf_add_options_page();
}
//===============================================
// Adds reusable blocks menu to the admin
function be_reusable_blocks_admin_menu() {
  add_menu_page( 'Reusable Blocks', 'Reusable Blocks', 'edit_posts', 'edit.php?post_type=wp_block', '', 'dashicons-editor-table', 22 );
}
add_action( 'admin_menu', 'be_reusable_blocks_admin_menu' );
//===============================================
// Enable exact search by phrase by setting $_GET['exact']
function urbosa_exact_search($search, $wp_query){
  global $wpdb;
  if(empty($search)) return $search;
  if(isset($_GET['exact']) && $_GET['exact']==1){
    $q = $wp_query->query_vars;
    $search = $searchand = '';

    foreach((array)$q['search_terms'] as $term) :
        $term = esc_sql(like_escape($term));
        $search.= "{$searchand}($wpdb->posts.post_title REGEXP '[[:<:]]{$term}[[:>:]]') OR ($wpdb->posts.post_content REGEXP '[[:<:]]{$term}[[:>:]]')";
        $searchand = ' AND ';
    endforeach;

    if(!empty($search)) :
        $search = " AND ({$search}) ";
        if(!is_user_logged_in())
            $search .= " AND ($wpdb->posts.post_password = '')";
    endif;
    return $search;
  }
  return $search;
}
// Enable exact search feature: Uncomment below
// add_filter('posts_search', 'urbosa_exact_search', 20, 2);