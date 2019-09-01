 <!-- Custom user-login  function -->
 <div>
   <h2>User template functions:</h2>
   <table width="100%" border="1" align="left">
     <tr align="left">
       <th>Function</th>
       <th>Value</th>
       <th>Description</th>
     </tr>
     <tr>
       <td> is_user_logged_in()</td>
       <td><?= (is_user_logged_in() ? 'true' : 'false'); ?></td>
       <td>Check user is logged in</td>
     </tr>
     <tr>
       <td>wp_login_form($args);</td>
       <td>
         <?php
          // Login form arguments.
          $args = array(
            'echo'           => true,
            'redirect'       => home_url('/'),
            'form_id'        => 'loginform',
            'label_username' => __('Username'),
            'label_password' => __('Password'),
            'label_remember' => __('Remember Me'),
            'label_log_in'   => __('Log In'),
            'id_username'    => 'user_login',
            'id_password'    => 'user_pass',
            'id_remember'    => 'rememberme',
            'id_submit'      => 'wp-submit',
            'remember'       => true,
            'value_username' => NULL,
            'value_remember' => true
          );

          // Calling the login form.
          wp_login_form($args);
          ?>
       </td>
       <td>Display custom form</td>
     </tr>
   </table>
 </div>