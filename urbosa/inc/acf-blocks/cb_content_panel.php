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
// Acf fields
$jumpID                = get_field('jump_id');

$imageLeft             = get_field('left_image');
$imageLeftMargin       = get_field('left_image_margin');
$imageLeftBottomMargin = get_field('left_image_bottom_margin');
$imageLeftZIndex       = get_field('left_z_index');

$imageRight             = get_field('right_image');
$imageRightMargin       = get_field('right_image_margin');
$imageRightBottomMargin = get_field('right_image_bottom_margin');
$imageRightZIndex       = get_field('right_z_index');

//-------------------------------------------------------------------------
$blockStyle = '';
$wrapperStyle = '';
$leftStyle = '';
$rightStyle = '';

if($imageLeft){ $leftStyle .= "background-image: url(".$imageLeft['url'].");"; }
if($imageLeftMargin){ $leftStyle .= "left: $imageLeftMargin;"; }
if($imageLeftBottomMargin){ $leftStyle .= "bottom: ;$imageLeftBottomMargin"; }
if($imageLeftZIndex){ $leftStyle .= "z-index: $imageLeftZIndex;"; }

if($imageRight){  $rightStyle .= "background-image: url(".$imageRight['url'].");";}
if($imageRightMargin){ $rightStyle .= "right: ".$imageRightMargin.";"; }
if($imageRightBottomMargin){ $rightStyle .= "bottom: ;$imageRightBottomMargin"; }
if($imageRightZIndex){ $rightStyle .= "z-index: $imageRightZIndex;"; }

?>
<div class="urbosa-block <?=$className?> ratio <?=$jumpID?>" style="<?=$blockStyle?>">
  <div class="wrapper">
    <div class="content-holder">
      <div class="content-wrapper <?=$columnType?>" style="<?=$wrapperStyle?>">
        <div class="urbosa container">
          { Main Content here }
        </div>
      </div>
      <div class="image-left"  style="<?=$leftStyle?>"></div>
      <div class="image-right" style="<?=$rightStyle?>"></div>
    </div>
  </div>
</div>