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

$items = get_field('accordion_items');

?>


<div class="urbosa-block <?=$className?>">
  <?php 
  
    if(!empty($items)){
      ?>
      <div class="items">
        <?php 
          for ($i=0; $i < count($items); $i++) { 
            $item = $items[$i];
            ?>
            <div class="item">
              <div class="q <?=$className?> trigger">
                <span class="icon"></span>
                <?=$item['main']?>
              </div>
              <div class="a">
                <div class="content">
                  <?=wpautop( $item['sub'])?>
                </div>
              </div>
            </div>
            <?php
          }
        ?>
      </div>
      <?php
    }else{
      ?>
      <div class="no-posts">
        No items yet.
      </div>
      <?php
    }
  ?>
</div>