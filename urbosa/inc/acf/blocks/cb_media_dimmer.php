<?php 
//--------------------------------------------------------------------------
// Create class attribute allowing for custom "className" and "align" values.
// This will check alignment as well
$className = basename(__FILE__, '.php'); 
if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}
if( !empty($block['align']) ) {
    $className .= ' align' . $block['align'];
}
//--------------------------------------------------------------------------

$image = get_field('initial_image');

?>


<div class="urbosa-block <?=$className?>">
  <?php 
  
    if(!empty($image)){
      $blockID = $block['id'];
      $caption = $image['description'];
      $alt     = $image['alt'];
      $imgSrc  = $image['url'];
      $link    = get_field('link');
      $isDimmer = get_field('is_dimmer');
      $attr     = "";
      $youtubeID = getYoutubeIdFromUrl($link);
      if($isDimmer){
        $content = "<img src='$imgSrc' alt='$alt' />";
        $ytSrc = 'https://www.youtube.com/embed/'.$youtubeID.'?autoplay=1&controls=0&html5=1&loop=1&mute=1&rel=0';
        
        if($youtubeID){
          $content = '<div class="video-container">
              <iframe src="'.$ytSrc.'" width="100%" height="100%" frameborder="0" allowfullscreen></iframe>
            </div>';
        }
        
        ?>
        <div class="ui page dimmer" id="dimmer_<?=$blockID?>">
          <div class="content">
            <div class="wrapper">
              <?=$content?>
            </div>
          </div>
        </div>
        <?php
      }

      if(is_admin()){
        $attr = "src='$imgSrc'";
        $link = "javascript:;";
      }
      ?>
      <a href="<?=$link?>" 
        class="media-dimmer-trigger"
        data-dimmer-id="dimmer_<?=$blockID?>"
      >
        <figure>
          <div class="image">
            <img data-src="<?=$imgSrc?>" alt="<?=$alt?>" class="urbosa-lazy-load" <?=$attr?>/>
          </div>
          <figcaption>
            <?=$caption?>
          </figcaption>
        </figure>
      </a>
      <?php

    }else{
      cb_no_resource_set('Media Dimmer', 'No items yet.');

    }
  ?>
</div>