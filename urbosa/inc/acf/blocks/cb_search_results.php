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
?>

<div class="urbosa-block <?=$className?>">
  <?php 
    if(!is_admin()){
      echo 'hey';
    }else{
      ?>
      <div class="no-resource-set">
        Search results block is not available in the admin.
      </div>
      <?php
    }
  ?>
</div>