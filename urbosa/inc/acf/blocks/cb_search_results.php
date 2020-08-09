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
// Prep variables


$blockID = $block['id'];

$headingSelector = $blockID.'_heading';
$contentSelector = $blockID.'_content';
$loadMoreSelector = $blockID.'_load_more';
$s = isset($_GET['s'])?$_GET['s']:'';

$heading  = str_replace('{found_posts}','...', get_field('template_heading'));
$heading  = str_replace('{search_term}',$s, $heading);
$noResult = get_field('template_no_result');

$blockData = array(
  'template' => get_field('template'),
  'search_post_types' => explode(',' , get_field('search_post_types'))
);

ajaxContent('cb_search_results', $contentSelector, $loadMoreSelector, $blockData);



?>

<div class="urbosa-block <?=$className?>">
  <div class="cb-heading" id="<?=$headingSelector?>">
    <?=$heading?>
  </div>
  <?php 
    if(!is_admin()){
      ?>
      <div class="cb-content" id="<?=$contentSelector?>"></div>
      <div class="cb-nav" id="<?=$loadMoreSelector?>"></div>
      <?php
    }else{
      ?>
      <div class="no-resource-set">
        Search results block is not available in the admin.
      </div>
      <?php
    }
  ?>
</div>

<script>
  $(document).ready(function(){
    if($('#<?=$contentSelector?>').length){
        var opt = <?=$contentSelector?>_optionData;
        var $contentContainer = $('#<?=$contentSelector?>')

        if($contentContainer.html()==''){
          $contentContainer.html(opt.label_loading);
        }

        console.log('Option', opt);
        if(opt){

          $.ajax({
            url: '/wp-admin/admin-ajax.php',
            type: 'POST',
            data:
              'action=' + opt.action + '&data=' + JSON.stringify(opt) + '&pg=' + opt.page,
            success: function (results) {
              console.log('Search results: ', results)
            }
          })
        }
    }
  })
</script>