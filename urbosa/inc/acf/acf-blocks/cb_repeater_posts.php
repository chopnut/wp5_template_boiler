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

$repeaterType = get_field('repeater_type');

$manual       = get_field('manual_objects');
$autoOption   = get_field('auto_post_options');

?>
<div class="urbosa-block <?=$className?>">
  <?php 
    $posts =[];

    if($repeaterType == 'auto'){ 

      $metaArray= [];
      $orderBy = 'date';
      $order   = 'DESC';

      $postType = get_field('auto_post_type');
      $count    = get_field('auto_display_count');
      $orderBy  = get_field('auto_order_by');

      switch($orderBy){
        case 'dateasc':
          $order = 'ASC';
        break;
        case 'random':
        break;
        case 'menuorder': 
          $orderBy = 'menu_order';
          $order = 'DESC';
        break;

      }

      $res = getPosts(
        $postType,
        '', [] , [], 
        $count, 0, 
        $metaArray,
        $orderBy, 
        $order
      );

    }else { // manual

    }

    $styleType = get_field('style_type');
    
    $styleDirection = get_field('style_direction');

  ?>
  Urbosa repeater posts
</div>