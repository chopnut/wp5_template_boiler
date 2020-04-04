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
  $url      = add_query_arg('login', 'success', $referrer);

  // If you are in the admin login go directly to wp-admin
  if (strpos($referrer, 'wp-login.php') !== false) {
    wp_redirect(home_url('/wp-admin'));
    exit;
  } else {
    if (!strstr($referrer, 'incorrect')  && !strstr($referrer, 'empty')) {
      wp_redirect($url);
      exit;
    }
  }
}
add_action('wp_login', 'redirect_login_page');

// When login failed
function login_failed()
{
  $referrer = $_SERVER['HTTP_REFERER'];
  $url      = add_query_arg('login', 'failed', $referrer);
  wp_redirect($url);
  exit;
}
add_action('wp_login_failed', 'login_failed');

// When user logout
function logout_redirect()
{

  $referrer = $_SERVER['HTTP_REFERER'];
  $url      = add_query_arg('login', 'logout', $referrer);
  wp_redirect($url);
  exit;
}
add_action('wp_logout', 'logout_redirect');
// Checking the fields data before loggin in
function verify_user_pass($user, $username, $password)
{
  // Pre-validating
}
add_filter('authenticate', 'verify_user_pass', 10, 3);

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
 *                           Ajax call
 ******************************************************************/
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