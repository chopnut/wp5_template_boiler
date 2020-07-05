<?php

/** 
 * Miscellaneous feature that you may use
 */

 

/*******************************************************************
 *              Setting up custom login behaviour 
 * Scenario: If you have a custom page and login form, you will redirect
 * them to the core login php and authenticate using it. Depending on status
 * further below is functions where to take them upon successful login
 *******************************************************************/
// When login successfully
function redirect_login_page()
{
  $referrer = $_SERVER['HTTP_REFERER'];
  // If you are in the admin login go directly to wp-admin
  if (strpos($referrer, 'wp-login.php') !== false) {
    wp_redirect(home_url('/wp-admin'));
    exit;
  } else {
    if (!strstr($referrer, 'incorrect')  && !strstr($referrer, 'empty')) {
      wp_redirect($referrer);
      exit;
    }
  }
}
add_action('wp_login', 'redirect_login_page');

// When login failed
function login_failed()
{
  $referrer = $_SERVER['HTTP_REFERER'];
  wp_redirect($referrer);
  exit;
}
add_action('wp_login_failed', 'login_failed');

// When user logout
function logout_redirect()
{
  $referrer = $_SERVER['HTTP_REFERER'];
  wp_redirect($referrer);
  exit;
}
add_action('wp_logout', 'logout_redirect');

// @verify_user_pass()
// You can check $user here if there is an error authenticating
// And redirect manually as needed.

function verify_user_pass($user, $username, $password)
{
  // Pre-validating
  if(is_wp_error($user)){
    // Error
    $error_codes = join( ',', $user->get_error_codes() );
  }else{
    // Good
  }
  return $user;
}
add_filter('authenticate', 'verify_user_pass', 101, 3);

/******************************************************************
 *                        Prevent admin
 ******************************************************************/
function prevent_admin_access_role()
{
  if (current_user_can('custom_role') && is_admin()) {
    // Redirect to home
    wp_redirect(home_url('/'));
    die();
  }
}
add_action('admin_init', 'prevent_admin_access_role');
if (!function_exists('register_my_setting')) {
  
  // Register phone number to the general settings
  function register_my_setting()
  {
    //-------------------------------------------------
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
  // Print input field to
  function myprefix_setting_callback_function($args)
  {
    $option = get_option($args[0]);
    echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" />';
  }
  add_action('admin_init', 'register_my_setting');
}


/******************************************************************
 *                        Adding user role
 ******************************************************************/
add_action('after_setup_theme', 'my_add_role_function');
function my_add_role_function()
{
  $roles_set = get_option('urbosa_role_setup');
  if (!$roles_set) {
    add_role('custom_role', 'Custom Role', array(
      'read' => true,
      'edit_posts' => false,
      'delete_posts' => false,
      'upload_files' => false
    ));
    update_option('urbosa_role_setup', true);
  }
}
/******************************************************************
 *                    Rest API/Ajax call
 ******************************************************************/
/**
 * How to search using rest api with JS
 * Use this EP: /wp-json/wp/v2/search?search=sample
 * Rest Api Reference: https://developer.wordpress.org/rest-api/reference/search-results/
 */

function urbosa_get_post($data){
  if(isset($_POST['data'])){
    $allData = json_decode(stripslashes($_POST['data']),true);
    echo '<script>foundPosts=0</script>'; // always return overall total posts found
  }
  exit;
}
add_action('wp_ajax_urbosa_get_post', 'urbosa_get_post');
add_action('wp_ajax_nopriv_urbosa_get_post', 'urbosa_get_post');//for users that are not logged in.


/******************************************************************
 *                    Custom Search
 ******************************************************************/
/**
 * This is how to create custom search a simple reference of what can be done
 */
  function urbosa_custom_search( $query )
  {
    if(is_search() && $query->is_main_query() && !is_admin()){

      $orderBy  = (isset($_GET['sortBy'])?$_GET['sortBy']:'');
      $s        = strtolower((isset($_GET['s'])?$_GET['s']:''));
      $categories  = [];
      $termIDS = [];


      if(count($categories)>0) {
        foreach ($categories as $cat) {
          $termIDS[] = $cat->term_id;
        }
      }
      $query->set('tax_query', array(
        'relation' => 'OR',
        array(
          'taxonomy' => 'category',
          'field' => 'term_id',
          'terms' => $termIDS,
          'include_children' => true
        )
      ));
  
      $query->set('orderby','date');
      $query->set('post_type','post');
      $query->set('post_status','publish');
      $query->set('posts_per_page',10);

  
      switch($orderBy){
        case 'newest':
          $query->set('order','DESC');
        break;
        case 'oldest':
          $query->set('order','ASC');
  
        break;
        case 'popular':
          $query->set('orderBy', 'meta_value_num');
          $query->set('order', 'DESC');
          $query->set('meta_key','page_view_counter');
        break;
        default:
      }
  
      if(strpos($s,'author:')!==false && isset($_GET['author']) && !empty($_GET['author'])){
        $authorID = $_GET['author'];
        $query->set('s','');
        $query->set('author',$authorID);
      }else{
        $query->set('author','');
  
      }
    }
  }
  // Uncomment below to enable: Custom search
  // add_action( 'pre_get_posts', 'urbosa_custom_search' );

add_action( 'admin_bar_menu', 'urbosa_add_items', 250 );
function urbosa_add_items( $wp_admin_bar ) {
  $label        = 'DEV'; 
  $class        = '';
  $title        = 'Click here to turn LIVE mode on.';
  $url          = setQueryURL(array('d'=>1));
  $themeStatus  = get_option('urbosa_theme_status');

  if($themeStatus){
    $label        = 'LIVE'; 
    $class        = '-live';
    $title        = 'Click here to turn DEV mode on.';
    $url          = setQueryURL(array('d'=>0));
  }
  $wp_admin_bar->add_menu( array(
    'id'    => 'urbosa-theme-status'.$class,
            'parent' => 'top-secondary',
    'title' => $label,
    'href'  => $url,
    'meta'  => array(
        'title' => __($title),
    ),
  ));       
}
add_action('admin_head', 'urbosa_admin_style'); 
function urbosa_admin_style() {
    $themeStatus  = get_option('urbosa_theme_status');
    $style = '';
    if($themeStatus) $style ='display: none;';
  ?>
  <style>
    #toplevel_page_edit-post_type-acf-field-group{<?=$style?>}
    #toplevel_page_cptui_main_menu{<?=$style?>}
    /* #adminmenuback,#adminmenu{display: none!important;} */
  </style>
  <?php
}

add_action('init', 'urbosa_theme_init');
function urbosa_theme_init(){
  // Process global theme process
    $themeStatus  = get_option('urbosa_theme_status');
    if(!$themeStatus && $themeStatus!==0) add_option('urbosa_theme_status', 0);
    $actioned = false;
    if(isset($_GET['d']) && ($_GET['d']==1 || $_GET['d']==0)){
      update_option('urbosa_theme_status',$_GET['d']);
      $actioned = true;
    }
    if(class_exists('Urbosa_ACF_Import_Export')){
      $urbosaACF = new Urbosa_ACF_Import_Export();
      $urbosaACF->init();
      $urbosaACF->process($actioned);
    }


    if(class_exists('Urbosa_Custom_Type')){

      $slider = new Urbosa_Custom_Type('theme_slider');
      $slider->set_label('Slider');
      $slider->set_icon('dashicons-image-flip-horizontal');
      $slider->set_menu_under('#urbosa_resources');
      $slider->set_support(array('title'));
      $slider->disable_editor();
      $slider->init();
    }
}

