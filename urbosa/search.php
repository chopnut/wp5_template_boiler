<?php get_header(); ?>

<?php

  if(function_exists('get_field')){


    $searchPage = get_field('search_page', 'options');
  
    if($searchPage){
  
      echo $searchPage->post_content;
  
    } else {
  
      ?>
      
      Search page is not set up yet.
  
      <?php
    }

  } else {

    ?>
    This template needs ACF plugin to work.
    <?php
    
  }

?>

<?php get_footer(); ?>