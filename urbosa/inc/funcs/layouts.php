<?php 
/* common layouts */
function urbosaGenerateSlider(
  $class='hero-banner', 
  $sliderFieldRepeater='banners',
  $eachFunctionCaller=''){
  ?>
  <div class="<?=$class?>">
    <?php 
      $banners = get_field($sliderFieldRepeater);
      if(!empty($banners)){
        ?>
        <div class="slick-sliders">
        <?php
        for ($i=0; $i < count($banners); $i++) { 
          $banner = $banners[$i];
          ?>
          <div class="each-slider">
            <div class="wrapper">
              <div class="content-wrapper">
                <?php 
                  if(!empty($eachFunctionCaller) && function_exists($eachFunctionCaller)){
                    $eachFunctionCaller($banner);
                  }else{
                    echo "Slider: $class - No function specified";
                  }
                ?>
              </div>
            </div>
          </div>
          <?php
        }
        ?>
        </div>
        <?php
      }
    ?>
  </div>
  <?php
}


?>