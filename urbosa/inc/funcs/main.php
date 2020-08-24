<?php

/** 
 * Main driver of the theme 
 */

function urbosa_login_page()
{
  $img = urbosa_custom_logo();
  if(empty($img)){
    $img = get_template_directory_uri().'/assets/img/placeholder.jpg';
  }
  
  ?>
  <style type="text/css">
    #login h1 a,
    .login h1 a {
      background-image: url(<?=$img?>);
      width: 200px;
      background-size: contain;
      background-repeat: no-repeat;
    }
    body.login {
      background-color: #eee;
    }
  </style>
  <script>
    var homeURL = '<?=home_url('/')?>';
    window.onload = function (){
        document.querySelector('#login h1 a').href = homeURL;
    }
  </script>
<?php



}
add_action('login_enqueue_scripts', 'urbosa_login_page');


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
//===============================================
// Add option page
  if(function_exists('acf_add_options_sub_page')){
    acf_add_options_page(array(
      'page_title'=>'Theme Options',
      'menu_title'=>'Theme Options',
      'menu_slug'=>'urbosa_theme_option',
      'capability' => 'edit_posts',
      'parent_slug' => '#urbosa_resources',
      'icon_url' => '',
      'post_id' => 'options', // to which post it applies to default to global options
      'update_button' => __('Update Theme Options', 'acf'),
      'position' => ''
    ));
  }
// Adds reusable blocks menu to the admin
function urbosa_theme_menu() {
  global $submenu; 
  add_menu_page( 'Theme Resources', 'Theme Resources', 'edit_posts', '#urbosa_resources', '', 'dashicons-welcome-widgets-menus', 58 );
  add_submenu_page( '#urbosa_resources', 'Reusable Blocks', 'Reusable Blocks', 'edit_posts','reusable_block','edit.php?post_type=wp_block',58 );
  $submenu['#urbosa_resources'][count($submenu['#urbosa_resources'])-1][2] = 'edit.php?post_type=wp_block';
}
add_action( 'admin_menu', 'urbosa_theme_menu' );
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

    // disable admin bar when on dev mode
    if(!$themeStatus && !is_admin()){
      add_filter('show_admin_bar', '__return_false');
      add_action( 'wp_print_styles', 'deregister_dashicons' );
      function deregister_dashicons()    {  wp_deregister_style( 'dashicons' ); }
    }

    if(class_exists('Urbosa_ACF_Import_Export')){
      $urbosaACF = new Urbosa_ACF_Import_Export();
      $urbosaACF->init();
      $urbosaACF->process($actioned);
    }


    if(class_exists('Custom_Type')){

      $slider = new Custom_Type('theme_slider');
      $slider->set_label('Slider');
      $slider->set_icon('dashicons-image-flip-horizontal');
      $slider->set_menu_under('#urbosa_resources');
      $slider->set_support(array('title'));
      $slider->disable_editor();
      $slider->init();
    }
}

function urbosa_setup_options(){
  global $post;
  // Use template_redirect instead of "init" to run every page load
  if(!is_admin()){

    if(is_page() || is_single()){

      if(function_exists('get_field')){

        $post404    = get_field('404_page', 'options');
        $searchPage = get_field('search_page', 'options');

        // is 404 do not index
        if($post404 && $post->ID == $post404->ID){
    
          add_action('wp_head', 'urbosa_404_do_not_follow', 0);
          
        }
        
        // is search page do not index
        if($searchPage && $post->ID == $searchPage->ID){

          add_action('wp_head', 'urbosa_404_do_not_follow', 0);

        }
      }
    }
  }
}
function urbosa_404_do_not_follow(){
  ?>
  <meta name="robots" content="noindex,nofollow" />
  <?
}
add_action( "template_redirect", "urbosa_setup_options" );