<?php

/** 
 * Miscellaneous feature that you may use
 */

/*******************************************************************
 *            Setting up a custom admin logo page
 *               changing logo and styling it
 *******************************************************************/
function my_login_logo()
{
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
<?php
}
add_action('login_enqueue_scripts', 'my_login_logo');

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
  }
}
add_action('admin_init', 'prevent_admin_access_role');
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
  /* 
  AJAX_CALL: HOW TO CALL FROM JS
  function callAjax(){
    var data = {
      page: 1,
      color: 'blue'
    }
    $.ajax({
      url: '/wp-admin/admin-ajax.php',
      type: 'POST',
      data: 'action=urbosa_get_post&data=' + JSON.stringify(data),
      success: function (results) {
        $('#container').html(results);
      }
    })
  }
  */
  if(isset($_POST['data'])){
    $filter = json_decode(stripslashes($_POST['data']),true);
  }
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