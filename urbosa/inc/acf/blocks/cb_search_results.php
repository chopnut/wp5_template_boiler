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
$loadMoreFunc     = $blockID.'_load_more_func';

$s = isset($_GET['s'])?$_GET['s']:'';

$postCategories = get_field('post_categories');
$postTaxonomies = get_field('post_taxonomies');

$blockData = array(
  'template' => get_field('template'),
  'search_post_types' => explode(',' , get_field('search_post_types')),
  'post_categories'   => !empty($postCategories)?explode(',' , $postCategories):array(),
  'post_taxonomies'   => !empty($postTaxonomies)?explode(',' , $postTaxonomies):array()
);

$postData = ajaxContent('cb_search_results', $contentSelector, $loadMoreSelector, $blockData);

$searchContents = cb_search_block_content($postData);
$foundPosts = $searchContents['found_posts'];
$heading  = str_replace('{found_posts}',$foundPosts, get_field('template_heading'));
$heading  = str_replace('{search_term}',$s, $heading);
$noResult = get_field('template_no_result');

$pages = ceil($foundPosts/$postData['per_page']);
$currentPage = $postData['page'];
if($postData['initial_page']){
  $currentPage = $postData['initial_page'];
}
?>

<div class="urbosa-block <?=$className?>">

  <?php 
    if(!is_admin()){
      ?>
      <div class="cb-heading" id="<?=$headingSelector?>">
        <?=$heading?>
      </div>
      <div class="cb-content" id="<?=$contentSelector?>">
      <?php 
        if($foundPosts){
          echo $searchContents['render'];
        }else{
          echo get_field('template_no_result');
        }
      ?>
      </div>
      <div class="cb-nav" id="<?=$loadMoreSelector?>">
        <?php 
          if($pages>=$currentPage){
            ?>
            <button onclick="<?=$loadMoreFunc?>(this)"><?=$postData['labels']['label_load_more']?></button>
            <?php
          }
        ?>
      </div>
      <?php
    }else{

      cb_no_resource_set('Search results block', 'is not available in the admin.');

    }
  ?>
</div>

<script>


  function <?=$loadMoreFunc?>(e){
    var opt = <?=$contentSelector?>_optionData;
    if($('#<?=$contentSelector?>').length && opt.busy==false){
      
        // Modify options
        var $loadButton = $(e);

        opt.page = opt.page + 1;
        opt.busy = true;
        $loadButton.prop('disabled',  true).html(opt.labels.label_loading);

        $.ajax({
          url: '/wp-admin/admin-ajax.php',
          type: 'POST',
          data:
            'action=' + opt.action + '&data=' + JSON.stringify(opt) + '&pg=' + opt.page,
          success: function (results) {

            var $contentContainer = $('#<?=$contentSelector?>')
            $contentContainer.append(results);

            // Reset options
            opt.busy = false;
            $loadButton.prop('disabled',  false).html(opt.labels.label_load_more);
            if(opt.page>=<?=$pages?>){
              $loadButton.css('display','none');
            }
          }
        })
        
  
    }
  }
  
</script>