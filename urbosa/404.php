<?php get_header(); ?>

    <?php

      if(function_exists('get_field')){

        $post404 = get_field('404_page', 'options');
  
        if($post404){
  
          echo $post404->post_content;
  
        } else {
          ?>
          404 <br/> Page not found.
          <?php
        }

      } else{
        ?>
        This template needs ACF plugin to work.
        <?php
      }

    ?>

<?php get_footer(); ?>
