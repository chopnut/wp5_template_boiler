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
//-------------------------------------------------------------------------
// Functions

if(!function_exists('__cb_theme_slider_video')){
  
  function __cb_theme_slider_video($src,$videoType,$deviceMode,$settingMobileHeightRatio, $is_lightbox,$mobile_poster=''){

    if($videoType == 'file'){

      $desktopSrc  = $src['url'];
      $desktopType = $src['mime_type']; 


      $videoClass ='';
      if(empty($settingMobileHeightRatio)){
        $videoClass = 'full';
      }

      __cb_theme_slider_video_file($deviceMode,$videoClass, $desktopSrc,$desktopType,$mobile_poster); 

    }else{

      __cb_theme_slider_video_embed($deviceMode, $src,  $is_lightbox,$mobile_poster);
      
    }
  }
}
if(!function_exists('__cb_theme_slider_video_file')){
  
  function __cb_theme_slider_video_file($mode,$videoClass,$desktopSrc,$type, $mobile_poster){

    // __cb_theme_slider_video_mobile_poster($mobile_poster);

    $attr = '';
    if($mobile_poster) {

      $poster = $mobile_poster['sizes']['large'];
      $attr = "poster='$poster'";

    }
    
    ?>
    <div class="video-wrapper <?=$mode?> ">

      <div class="html5-video-container">

        <video loop autoplay muted class="<?=$videoClass?>" <?=$attr?> src="<?=$desktopSrc?>"  type="<?=$type?>"></video>

      </div>

    </div>
    <?php
  }
}
if(!function_exists('__cb_theme_slider_video_embed')){
  
  function __cb_theme_slider_video_embed($mode,$embedCode,$is_lightbox, $mobile_poster){

    $yt = youtubeEmbed($embedCode,'background'); 

    if($is_lightbox){
      ?>
      <a class="youtube-lightbox <?=$mode?>" href="<?=$yt['src']?>"   ></a>
      <?php
    }
    __cb_theme_slider_video_mobile_poster($mobile_poster);
    ?>
    <div class="youtube-wrapper <?=$mode?> ">
      <?php 
        echo $yt['render'];
      ?>
    </div>
    <?php
  }
}
if(!function_exists('__cb_theme_slider_video_mobile_poster')){
  
  function __cb_theme_slider_video_mobile_poster($mobile_poster){

    if($mobile_poster){
      
      $lowRes = $mobile_poster['sizes']['progressive_landscape'];
      $hiRes  = $mobile_poster['url'];

      $containerAttr = "data-high='$hiRes' ";
      $containerAttr.= " style='background-image: url($lowRes);'"

      ?>
        <div class="image-wrapper progressive mobile poster" <?=$containerAttr?>></div>
      <?php
    }
  }
}
//-------------------------------------------------------------------------
// Acf fields
$theme_slider           = get_field('selected_slider');
$sliders                = get_field('sliders',$theme_slider);
$slider_properties      = get_field('slider_properties',$theme_slider);
$slider_feature         = get_field('feature', $theme_slider);

$proportion             = get_field('container_proportion');
$mobile_height_ratio    = get_field('mobile_height_ratio');
$desktopRatio           = get_field('ratio');

$theme_slider_id = 'theme_slider_'.$block['id'];

//-------------------------------------------------------------------------
// Slider
$show_arrows = $slider_properties['show_arrows'];
$arrow_icon  = $slider_properties['arrow_icon'];
$show_navigation  = $slider_properties['show_navigation'];
$auto_play  = $slider_properties['auto_play'];
$auto_play_speed  = $slider_properties['auto_play_speed'];

$is_parallax    = false;
$is_progressive = false;
$is_lazy        = false;
$is_lightbox    = false;



if(is_array($slider_feature)){
  $is_parallax    = (array_search('parallax', $slider_feature) !== false);
  $is_progressive = (array_search('progressive', $slider_feature)!== false);
  $is_lazy        = (array_search('lazyload', $slider_feature)!== false);
  $is_lightbox    = (array_search('lightbox', $slider_feature)!== false);
}

?>
<style>
  #<?=$theme_slider_id;?> .content-holder::before{
    <?php 
      if($proportion=='override'){
        echo "padding-bottom: $desktopRatio;";
      }
    ?>
  }
  @media (max-width: 768px){
    #<?=$theme_slider_id;?> .content-holder::before{
    <?php 
      if(!empty($mobile_height_ratio)){
        echo "padding-bottom: $mobile_height_ratio;";
      }
    ?>
  }
  }
</style>
<script>
  <?php 
    $lightBoxCollection = $theme_slider_id. '_lightbox_collection';
    $lightboxInstance   = $theme_slider_id. '_lightbox_instance';
    $lightboxFunc       = $theme_slider_id. '_lightbox_func';
    $fixImageClones     = $theme_slider_id. '_fix_image_clones';

    if($is_lightbox){
      ?>
      var <?=$lightBoxCollection?> = [];
      <?php
    }
  ?>
</script>
<div class="urbosa-block <?=$className?> <?=$proportion?>  <?=(is_admin()?'admin':'')?>" id="<?=$theme_slider_id?>">

      <?php 
        $true = false;

        if(is_array($sliders) && count($sliders)>0){


          ?>
          <div class="slick-sliders" id="<?=$theme_slider_id?>_slick">
          <?php 
              $ctr = 0;
              foreach ($sliders as $slider) {

                // Prep-vars

                $background_type        = $slider['background_type'];
                $image                  = $slider['image'];
                
                $desktop_video_type      = $slider['desktop_video']['desktop_video_type'];
                $desktop_video_file      = $slider['desktop_video']['desktop_video_src_file'];
                $desktop_video_embed     = $slider['desktop_video']['desktop_video_embed'];

                $mobile_video_type      = $slider['mobile_video']['mobile_video_type'];
                $mobile_video_file      = $slider['mobile_video']['mobile_video_src_file'];
                $mobile_video_embed     = $slider['mobile_video']['mobile_video_embed'];
     
                $mobile_poster          = $slider['mobile_poster'];

                $content                = $slider['content'];
                $content_position       = $slider['content_position'];

                // If one or the other is missing assign them to any to make sure there is value

                $final_verdict = array();
                $final_verdict['desktop'] = array(
                  'type' => $desktop_video_type,
                  'src'=> ($desktop_video_type=='file'?$desktop_video_file:$desktop_video_embed),
                );
    
                $final_verdict['mobile'] = array(
                  'type' => $mobile_video_type,
                  'src'=> ($mobile_video_type=='file'?$mobile_video_file:$mobile_video_embed),
                );

                if(empty($final_verdict['desktop']['src'])){
                  $final_verdict['desktop']['type'] = $final_verdict['mobile']['type'];
                  $final_verdict['desktop']['src'] = $final_verdict['mobile']['src'];
                }
                if(empty($final_verdict['mobile']['src'])){
                  $final_verdict['mobile']['type'] = $final_verdict['desktop']['type'];
                  $final_verdict['mobile']['src'] = $final_verdict['desktop']['src'];
                }

                // Style


                ?>
              <div class="each-slider ">
                
                <div class="wrapper">
                  <div class="content-holder ratio <?=$proportion?>">
                    


                <div class="actual-content">

                <?php
                
                //------------------- content ------------------------
                if(!empty($content)){
                  ?>
                  <div class="content-wrapper <?=$content_position?>">
                    <div class="the_content"><?=wpautop( $content )?></div>
                  </div>
                  <?php
                }

                //------------------- resource -----------------------
                if($background_type =='video'){

                  $desktopVideoSrc        = $final_verdict['desktop']['src'];
                  $desktopVideoType    = $final_verdict['desktop']['type'];

                  $mobileVideoSrc         = $final_verdict['mobile']['src'];
                  $mobileVideoType     = $final_verdict['mobile']['type'];


                  if(!empty($desktopVideoSrc) && !empty($mobileVideoSrc)){

   
                    __cb_theme_slider_video($mobileVideoSrc, $mobileVideoType, 'mobile', $mobile_height_ratio, $is_lightbox,$mobile_poster);
                    __cb_theme_slider_video($desktopVideoSrc, $desktopVideoType, 'desktop', $mobile_height_ratio, $is_lightbox,$mobile_poster);



                  } else {


                    cb_no_resource_set('Theme slider block', 'No video set for this slide.');

                  }

                  
                //-----------------------------------------------------------
                }else{ // Image

         
                  $lowRes = $image['sizes']['progressive_landscape'];
                  $hiRes  = $image['url'];
                  $alt    = $image['alt'];
                
                  $innerContent  = $containerClass = $containerAttr = "";
                  $containerStyle= "background-image: url($hiRes)";

                  if($image){
                    

                    $imageID = $image['ID'].'_id'.$ctr;
                    
                    if($is_progressive && !is_admin()){
                      
                      $containerClass .= 'progressive '.$theme_slider_id;
                      $containerAttr .= "data-high='$hiRes' ";
                      $containerStyle= "background-image: url($lowRes)";

                    }

                    if($is_parallax && !is_admin()){

                      $innerContent = "<img class='parallax {class} $theme_slider_id' {progressive} alt='$alt'/>";

                      if(!$is_lazy) {

                        $innerContent = "<img class='parallax {class} $theme_slider_id' {progressive} alt='$alt' src='$hiRes' />";

                      }

                      if($is_progressive){

                        $innerContent = str_replace('{progressive}',$containerAttr, $innerContent);
                        $innerContent = str_replace('{class}','progressive', $innerContent);
                        $containerAttr = $containerClass = "";

                      }else{

                        $innerContent = str_replace('{progressive}','', $innerContent);
                        $innerContent = str_replace('{class}','', $innerContent);
                      }

                      $containerStyle = "";
                    }

                    if($is_lazy && !is_admin()){
   
                      $innerContent = "<img data-src='$hiRes' class='{parallax} urbosa-lazy-load $imageID'alt='$alt' data-id='$imageID'/>";
                      
                      $containerClass = $containerStyle = "";
                      if($is_parallax){
                        $innerContent = str_replace('{parallax}','parallax', $innerContent);

                      }else{

                        $innerContent = str_replace('{parallax}','', $innerContent);
                      }
                    }


                    if($is_lightbox && !is_admin()){
                      
                      ?>
                      <script><?=$lightBoxCollection?>.push('<?=$hiRes?>')</script>
                      <?php
                      $containerClass .= ' light-box';
                      $containerAttr  .= " onclick=\"$lightboxFunc($ctr)\"";
                      
                    }
                    

                  ?>
                    <div class="image-wrapper <?=$imageID?> <?=$containerClass?>"  <?=$containerAttr?> style="<?=$containerStyle?>" data-id="<?=$imageID?>" >
                      <?=$innerContent?>
                    </div>

                  <?php

                  } else{
                    cb_no_resource_set('Theme slider block', 'No image set for this slide.');
                  }
                }

                ?>
                      </div><!--actual content-->


                    </div><!-- ratio -->
                  </div><!-- wrapper -->



                </div>
                <?php


                // Show only one to admin
                if(is_admin( )){ break; }
                $ctr++;
              }
              
          ?>
          </div>
          <?php
        } else {

          cb_no_resource_set('Theme slider block', 'No slides has been set up yet.');

        }
      ?>


 
</div>
<script>
  <?php 
    // Trigger lightbox only when this is true
    $triggerLightBoxVar = $lightboxInstance.'_trigger_lightbox';
  ?>
  function <?=$fixImageClones?>(){

    // Copy original to the clones for image types, so it doesnt blur anymore //progressive only

    $actual = $('#<?=$theme_slider_id?>_slick .slick-slide:not(.slick-cloned) .image-wrapper.enhanced:not(.copied)')
    $clones = $('#<?=$theme_slider_id?>_slick .slick-cloned .image-wrapper')
    
    if($actual.length){
      copyClonesAttributes($actual,$clones);
      $actual.addClass('.copied');
    }

  }
  var  <?=$triggerLightBoxVar?>= true;

  jQuery(document).ready(function($){
    <?php 

      if($is_progressive && !is_admin()){
        ?>
    
        initProgressive('<?=$theme_slider_id?>');
        <?php

      }else if(!is_admin()){
        ?>
        initProgressive();
        <?php
      }
      if($is_parallax && !is_admin()){
        ?>
      
      initSimpleParallax();
        <?php
      }

      if($is_lightbox && !is_admin()){
        ?>

        if($.fn.simpleLightbox){
          $('.youtube-lightbox').simpleLightbox()
        var <?=$lightboxInstance?> = new SimpleLightbox({
          items: <?=$lightBoxCollection?>
        })
        window.<?=$lightboxFunc?> = function(pos){
          if(<?=$triggerLightBoxVar?>){

            SimpleLightbox.defaults = {
                elementClass: '',
                elementLoadingClass: 'slbLoading',
                htmlClass: 'slbActive',
                closeBtnClass: '',
                nextBtnClass: '',
                prevBtnClass: '',
                loadingTextClass: '',
                // customize / localize controls captions
                closeBtnCaption: 'Close',
                nextBtnCaption: 'Next',
                prevBtnCaption: 'Previous',
                loadingCaption: 'Loading...',
                bindToItems: true, // set click event handler to trigger lightbox on provided $items
                closeOnOverlayClick: true,
                closeOnEscapeKey: true,
                nextOnImageClick: true,
                showCaptions: true,
                captionAttribute: 'title', // choose data source for library to glean image caption from
                urlAttribute: 'href', // where to expect large image
                startAt: pos, // start gallery at custom index
                loadingTimeout: 100, // time after loading element will appear
                appendTarget: 'body', // append elsewhere if needed
                beforeSetContent: null, // convenient hooks for extending library behavoiur
                beforeClose: null,
                beforeDestroy: null,
                videoRegex: new RegExp(/youtube.com|vimeo.com/) // regex which tests load url for iframe content
            };

           SimpleLightbox.open(<?=$lightboxInstance?>)
          }
        }
        
      } else{
        console.log('Warning: Lightbox is disabled in the template.');
      }

        <?php
      }
      
    ?>

    var slickOptions = {
      slideToShow: 1,
      rows: 0
    }

    <?php 
      if($show_arrows && $arrow_icon){
        ?>
        slickOptions['nextArrow'] = '<img src="<?=$arrow_icon?>" class="slick-next" width="50" height="50" />';
        slickOptions['prevArrow'] = '<img src="<?=$arrow_icon?>" class="slick-prev" width="50" height="50" />';
        <?php
      }else if($show_arrows){
        ?>
        slickOptions['nextArrow'] = '<span class="slick-arrow slick-next">&rsaquo;</span>';
        slickOptions['prevArrow'] = '<span class="slick arrow slick-prev">&rsaquo;</span>';
        <?php
      }else{
        ?>
        slickOptions['nextArrow'] = false;
        slickOptions['prevArrow'] = false;
        <?php
      }

      if($show_navigation){
        ?>
        slickOptions['dots'] = true;
        <?php
      }
      if($auto_play){
        ?>
        slickOptions['autoplay'] = true;
        <?php
        
      }
      if($auto_play_speed){
        ?>
        slickOptions['autoplaySpeed'] = <?=$auto_play_speed?>;
        <?php
      }


      
      if(!is_admin()){
        ?>
        $('#<?=$theme_slider_id?>_slick').on('beforeChange',function(slick,currentSlide,nextSlide){

        
          <?=$triggerLightBoxVar?> = false;
          $('.youtube-lightbox').map(function(index, item){
            $(item).replaceWith($(item).clone()); // replace removes all event listeners to the element
          })
         
        });
        $('#<?=$theme_slider_id?>_slick').on('afterChange',function(slick){
          <?=$triggerLightBoxVar?> = true;

          <?=$fixImageClones?>();

          <?php 
            if($is_lightbox){
              ?>
              if($.fn.simpleLightbox){

                $('.youtube-lightbox').simpleLightbox()
              }

              <?php
            }
            
          ?>


        });
        $('#<?=$theme_slider_id?>_slick').on('init',function(slick){

          <?=$fixImageClones?>();


        })

        $('#<?=$theme_slider_id?>_slick').slick(slickOptions);

        <?php
      }
    ?>
    

    

  });
</script>

<?php 



?>