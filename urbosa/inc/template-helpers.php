<?php
if(!function_exists('debug')){  
  /**
   * debug
   * More presentational debug display of a variable
   * @param  mixed $data
   * @return void
   */
  function debug($data){
    $html  = '<pre style="padding: 15px; font-family: \'courier new\'; font-size: 12px; border: 1px dashed #800">';
    $html .= print_r($data, true);
    $html .= '</pre>';
    echo $html;
  }
}
if(!function_exists('objectToArray')){  
  /**
   * objectToArray
   * Convert object into an array
   * @param  mixed $object
   * @return array
   */
  function objectToArray($object)
  {
    if (!is_object($object) && !is_array($object)) {
      return $object;
    }
  
    if (is_object($object)) {
      $object = (array)$object;
    }
  
    return array_map('objectToArray', $object);
  }
}

if(!function_exists('getPosts')){  
  /**
   * getPosts - Return posts depending on parameters.
   * @param  mixed $post_type
   * @param  mixed $slug
   * @param  mixed $acfFields
   * @param  mixed $categories
   * @param  mixed $perPage
   * @param  mixed $offset
   * @param  mixed $metaArray
   * @param  mixed $orderby
   * @param  mixed $order
   * @return void
   */
  function getPosts( $post_type='post',  $slug='', $acfFields = array(),  $categories = array(),  $perPage = -1,  $offset = 0, $metaArray = array(),  $orderby = 'menu_order',  $order = 'DESC')
  {
    $args = array(
      'post_type' => $post_type,
      'post_status' => 'publish',
      'ignore_sticky_posts' => 0,
      'posts_per_page' => $perPage,
      'orderby' => $orderby,
      'order' => $order,
      'offset' => $offset,
    );
  
    if (count($categories) > 0) {
      $args['category_name'] = implode(',', $categories);
    }
    if(!empty($slug)){
      $args['name'] = $slug;
    }
  
    if (count($metaArray) > 0) {
      foreach ($metaArray as $meta) {
        if ($meta['value'] != '') {
          $args['meta_query'][] = array(
            'key' => $meta['key'],
            'value' => $meta['value'],
          );
        }
      }
    }
    
    $dataItems = new WP_Query($args);
    $query     = $dataItems;
    $dataItems = objectToArray($dataItems->posts);
    $dataArray = array();
  
    foreach ($dataItems as $data) {
      $thisID = $data['ID'];
      foreach ($acfFields as $field) {
        $data[$field] = get_field($field, $thisID);
        if (stripos($field, 'image') !== false) {
          $data[$field] = $data[$field];
        }
      }
      $dataArray[] = $data;
    }
    return 
      array(
        'posts' => $dataArray,
        'query' => $query,
      );  
    
  }
}

if(!function_exists('getWCProducts')){  
  /**
   * getWCProducts
   * 
   * @param  mixed $perPage
   * @param  mixed $searchTerm      eg: 'cool product'
   * @param  mixed $categorySlugs   eg: array('slug-1')
   * @param  mixed $attributeSlugs  eg: array('colour' => 'red','size'=> array('small','large'))
   * @param  mixed $minMaxPrice     eg: array(100,500)
   * @param  mixed $orderby         eg: array('orderby'=>'meta_value_num','order'=>'desc','meta_key'=>'order')
   * @param  mixed $inStock         true or false | returns all product in stuck
   * @return void
   */
  function getWCProducts(
    $perPage=10,
    $searchTerm='',
    $categorySlugs=array(), 
    $attributeSlugs=array(),
    $minMaxPrice=null,
    $orderby=null,
    $inStock=false){
    
    global $paged;
  
    $offset       = ($paged-1)*$perPage;
    if($offset<=0){
      $offset = 0;
    }
    $argCats      = array();
    $metaArgs     = array();
  
    $argCats['relation']    = 'AND';
    $metaArgs['relation']   = 'AND';
  
  
  
    if($minMaxPrice && count($minMaxPrice)==2){
      $metaArgs[] = array(
        'key' => '_price',
        'value' => array($minMaxPrice[0], $minMaxPrice[1]),
        'compare' => 'BETWEEN',
        'type' => 'NUMERIC'
      );
    }
    if($inStock){
      $metaArgs[]= array(
        'key' => '_stock_status',
        'value' => 'instock',
        'compare' => '=',
      );
    }
    foreach($categorySlugs as $cat){
      if(!empty($cat)){
        $argCats[] = array(
          'taxonomy' => 'product_cat',
          'field' => 'slug',
          'terms'=> $cat
        );
      }
    }
    foreach($attributeSlugs as $attSlug=>$attValue){
      if(!empty($attValue)){
        $argCats[] = array(
          'taxonomy'        =>  'pa_'.$attSlug,
          'field'           => 'slug',
          'terms'           =>  $attValue,
          'operator'        => 'IN',
        );
      }
    }
    $args = array(
      'post_type'             => 'product',
      'post_status'           => 'publish',
      'ignore_sticky_posts'   => 1,
      'posts_per_page'        => $perPage,
      'offset'                => $offset,
      'orderby' => 'post_date',
      'order' => 'DESC',
      'meta_key' => '',
      'tax_query' => $argCats,
      'meta_query'=> $metaArgs,
      's'=>$searchTerm,
      'paged'=>$paged
    );
    if($orderby){
      if(isset($orderby['orderby']) && isset($orderby['order'])){
        $args['orderby']  = $orderby['orderby'];
        $args['order']    = $orderby['order'];
  
        if(isset($orderby['meta_key'])){
          $args['meta_key'] = $orderby['meta_key'];
        }
      }
    }
  
  
    return new WP_Query($args); 
  }
}
if(!function_exists('getCurrentTemplate')){
  add_filter( 'template_include', 'var_template_include', 1000 );
  function var_template_include( $t ){
      $GLOBALS['current_theme_template'] = basename($t);
      return $t;
  }  
  /**
   * getCurrentTemplate
   * Display current template being used.
   * @param  mixed $echo eg: To echo or return
   * @return mixed
   */
  function getCurrentTemplate( $echo = false ) {
      if( !isset( $GLOBALS['current_theme_template'] ) )
          return false;
      if( $echo )
          echo $GLOBALS['current_theme_template'];
      else
          return $GLOBALS['current_theme_template'];
  }
}

if(!function_exists('getChildrenCategories')){  
  /**
   * getChildrenCategories
   * Get all children category under the main category
   * @param  mixed $parent_slug_or_id - Category slug or ID
   * @param  mixed $hide_empty        - To hide category with no posts
   * @return void
   */
  function getChildrenCategories($parent_slug_or_id, $hide_empty = false)
  {
    $parent_id = 0;
    $parent = null;

    if (is_numeric($parent_slug_or_id)) {
      $parent_id = $parent_slug_or_id;
    } else {
      $parent = get_category_by_slug($parent_slug_or_id);
      if ($parent) {
        $parent_id = $parent->term_id;
      }
    }


    if ($parent_id) {
      // Category by parent
      $args = array(
        'type' => 'post',
        'child_of' => $parent_id,
        'hierarchical' => 1,
        'taxonomy' => 'category',
        'hide_empty' => $hide_empty
      );
      $children = get_categories($args);
      return $children;
    }
    return null;
  }
}

if(!function_exists('getWidgetArray')){
    
  /**
   * Returns widgets into an array of widgets for display
   *
   * @param  mixed $widget_id
   * @return array
   */
  function getWidgetArray($widget_id){
    global $wp_registered_widgets;
    $sidebars_widgets = wp_get_sidebars_widgets();

    if(!isset($sidebars_widgets[$widget_id])){
      return array();
    }
    $widgets = $sidebars_widgets[$widget_id]; 
    
    $final  = [];
   
    foreach ($widgets as $widget) {

      $option_name = $wp_registered_widgets[$widget]['callback'][0]->option_name;
      $key = $wp_registered_widgets[$widget]['params'][0]['number'];
      $widget_instances = get_option($option_name);
      
      // make sure "content" has values cause sometimes , "text" has it.
      $data    = $widget_instances[$key];
      $content = '';
      
      if(!isset($data['content']) && !isset($data['nav_menu'])){
        ob_start();
        echo do_shortcode(wpautop($data['text']));
        $content = ob_get_clean();

      }else if(isset($data['nav_menu'])){
        // Grab the menu object by slug as to make sure you are grabbing the correct one
        $menuID = $data['nav_menu'];
        ob_start();
        $menuObject = wp_get_nav_menu_object($menuID);
        wp_nav_menu(array('menu'=>$menuObject->slug));
        $content = ob_get_clean();
      }
      $menuTitle = strtolower($data['title']);
      ob_start();
      ?>
      <div class="widget <?=$menuTitle?>">
        <div class="widget-title"><?=$data['title']?></div>
        <div class="widget-content"><?=$content?></div>
      </div>
      <?php
      $content = ob_get_clean();
      $data['content'] = $content; 
      $final[] = $data;
    }
    return $final;
  }
}
if(!function_exists('pregMatchGrouping')){  
  /**
   * Preg_match_all replacement as this groups matches in their own array instead of separate.
   *
   * @param  mixed $exp
   * @param  mixed $subject
   * @param  bool $unique = if values needs to be unique
   * @return array
   */
  function pregMatchGrouping($exp, $subject, $unique= true){
    preg_match_all($exp,$subject, $matches);
    $tmp = $unique = array();

    if($matches && count($matches)>0){
      for ($i=0; $i < count($matches); $i++) { 
        $match = $matches[$i];
        for ($n=0; $n < count($match); $n++) { 
          if(!isset($unique[$match[$n]])){
            $unique[$match[$n]] = true;
            if(!isset($tmp[$n])){
              $tmp[$n] = array();
              $tmp[$n][] = $match[$n];
            }else{
              $tmp[$n][] = $match[$n];
            }
          }
        }
      }
    }
    return $tmp;
  }
}
if(!function_exists('getFeatureImage')){  
  /**
   * getFeatureImageSrc of a post
   *
   * @param  mixed $postID
   * @param  mixed $size eg: 'thumbnail','medium','large','full'
   * @return string
   */
  function getFeatureImage($postID=null, $size='medium'){
    $pID = 0;
    if($postID){
      $pID = $postID;
    } else {
      $pID = get_the_ID();
    }
    $url = wp_get_attachment_image_src( get_post_thumbnail_id( $pID), $size );
    if(is_array($url) && count($url)>0){
      return $url[0];
    }
    return '';
  }
}

if(!function_exists('youtubeEmbed')){
  
  /**
   * Display embeded video as normal (being outputted anywhere on the page) or background as a full width browser bg.
   *
   * @param  mixed $embed_video eg: raw iframe code from youtube
   * @param  mixed $mode eg: 'normal' or 'background'
   * @param  mixed $properties
   * @param  mixed $srcquery
   * @return void
   */
  function youtubeEmbed($embed_video,$mode='normal',$properties="width='100%' height='100%' frameborder='0' allowfullscreen",$srcquery="&autoplay=1&controls=0&html5=1&loop=1&mute=1&rel=0"){
    $new_embed = $embed_video;
    if($mode=='normal'){
      echo '<div class="video-container">';
      echo $new_embed;
      echo '</div>';
    }else if($mode=='background'){
      $new_embed = preg_replace('/'.'width=".*?"'.'/','', $new_embed);
      $new_embed = preg_replace('/'.'height=".*?"'.'/','', $new_embed);
      $new_embed = preg_replace('/'.'<iframe'.'/','<iframe '.$properties, $new_embed);
    
      preg_match('/'.'(src=".*?")'.'/',$embed_video,$match);
      if($match){
        $src = $match[1];
        $src = substr($src,0,-1);
        $src = $src.$srcquery.'"';
        $new_embed = preg_replace('/'.'src=".*?"'.'/',$src, $new_embed);
      }
      echo '<div class="video-background">';
        echo '<div class="video-foreground">';
        echo $new_embed;
        echo '</div>';
      echo '</div>';
    }
  }
}
if(!function_exists('getContentBlocks')){
    
  /**
   * getContentBlocks
   *
   * @param  mixed $postID
   * @param  mixed $blockNames eg: array or string
   * @param  mixed $getAllOther
   * @return void
   */
  function getContentBlocks( $postID, $blockNames, &$getAllOther= null ) {
    $post_content = get_post( $postID )->post_content;
  
    //get all blocks of requested type
    $otherBlocks  = array();
    $blocks       = array();
    $parseBlocks =  parse_blocks($post_content);

    foreach($parseBlocks as $parseBlock){
      $tmpBlock = $parseBlock;
      if(is_array($blockNames)){
        $found = 0;
        foreach ($blockNames as $eachBlockName) {
          $blockID = str_replace('_','-', $eachBlockName);
          $acfBlockID = 'acf/'.$blockID;
          if($blockID==$parseBlock['blockName'] || $acfBlockID==$parseBlock['blockName']){
            $found = 1;
          }
        }
        // Check for reusable block
        if( $parseBlock['blockName'] === 'core/block' && ! empty( $parseBlock['attrs']['ref'] ) ){
          $blockContent = parse_blocks( get_post( $parseBlock['attrs']['ref'] )->post_content );
          if(is_array($blockContent) && count($blockContent)>0){
            $blockID       = str_replace('acf/','', $blockContent[0]['blockName']);
            $blockID       = str_replace('-','_', $blockID);
  
            if(in_array($blockID, $blockNames)){
              $found = 1;
              $tmpBlock = $blockContent[0];
            }
          }

        }
        if($found){
          $blocks[] = $tmpBlock;
          continue;
        }else{
          if(!empty($parseBlock['blockName'])){
            $otherBlocks[] = $tmpBlock;
          }
        }
      }else{
        $blockID = str_replace('_','-', $blockNames);
        $acfBlockID = 'acf/'.$blockID;
        
        if($blockID==$parseBlock['blockName'] || $acfBlockID==$parseBlock['blockName']){
          $blocks[] = $parseBlock;
        }else{
          if(!empty($parseBlock['blockName'])){
            $otherBlocks[] = $parseBlock;
          }
        }
      }

    }

    if(is_array($getAllOther)){
      foreach ($otherBlocks as $otherBlock) {
        $getAllOther[] = $otherBlock;
      }
    }
    return $blocks;
  }
}

if(!function_exists('getVar')){  
  /**
   * get post/get/request if available return default otherwise
   *
   * @param  mixed $name
   * @param  mixed $default
   * @return void
   */
  function getVar($name,$default=''){
    if(isset($_REQUEST[$name])){
      return $_REQUEST[$name];
    }
    if(isset($_GET[$name])){
      return $_GET[$name];
    }
    if(isset($_POST[$name])){
      return $_POST[$name];
    }
    return $default;
  }
}
if(!function_exists('getACFImage')){  
  
  /**
   * Get the image and return default if acf is not available
   *
   * @param  mixed $acfField
   * @param  mixed $postID
   * @param  mixed $default
   * @param  mixed $size
   * @return void
   */
  function getACFImage($acfField,$postID=0,$default='',$size='large'){
    $acfImage = ($postID)? get_field($acfField,$postID):get_field($acfField);
    if(!$acfImage || !isset($acfImage['sizes']) || !isset($acfImage['url'])){
      return $default;
    }
    if($size=='full' || $size=='url'){
      return $acfImage['url'];
    }
    if(isset($acfImage['sizes'][$size])){
      return $acfImage['sizes'][$size];
    }
    return $default;
  }
}
if(!function_exists('doCurl')){
  function doCurl($path,$postParams= null){
    /* Prep-variables */
    $ch       = curl_init();
    /* Initialize CURL set-up */
    curl_setopt($ch, CURLOPT_URL, $path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING , "");
  
    if($postParams){
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postParams));
    }
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
  }
}
/* 
  Progressive background
  How to use: 
  1. Call enableProgressiveBG() 
  2. Use progressive_landscape/progressive_portrait image size for lowResImageURL
  3. Set your element eg: <div class="progressive" data-low="<?=encodeDataImage(lowResImageURL)?>" data-high="{highResImageURL}"></div>
*/
if(!function_exists('progressiveBG') && !function_exists('enableProgressiveBG')){
  function progressiveBG(){
    ?>
<script>
jQuery(document).ready(function($){
  /* 
    Parallax and Progressive Example
    <img class="progressive" src="{encodedLowImage}" data-high="{highImageURL}" />
  */
  for (let n = 0; n < $('.progressive').length; n++) {
    var svgblur =`<svg xmlns="http://www.w3.org/2000/svg"
        xmlns:xlink="http://www.w3.org/1999/xlink"
        width="1500" height="823"
        viewBox="0 0 1500 823">
      <filter id="blur" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
        <feGaussianBlur stdDeviation="100 100" edgeMode="duplicate" />
        <feComponentTransfer>
          <feFuncA type="discrete" tableValues="1 1" />
        </feComponentTransfer>
      </filter>
      <image filter="url(#blur)" xlink:href="REPLACEME" x="0" y="0" height="100%" width="100%"/>
    </svg>`
    // Load low-res
    var $el = $($('.progressive')[n]);
    var base64Image = $el.data('low')
    var all = `url(data:image/svg+xml;base64,${window.btoa(svgblur.replace('REPLACEME', base64Image ))})`

    if($el.prop('tagName')=='IMG'){
      $el.attr('src',all);
    }else{
      $el.css('background-image',all);
    }
    // Load hi-res
    var high = $el.data('high')
    var imgHigh = new Image();
    imgHigh.onload = function() {
      if($el.prop('tagName')=='IMG'){
        $el.attr('src',high);
      }else{
        $el.css('background-image',`url(${high})`)
      }
      $el.addClass('enhanced')
    };
    if (high) { imgHigh.src = high; }
  }
})
</script>
<style>
.progressive { background-size: cover; transform: translateZ(0); transition: filter .5s ease-in; filter: blur(4px);}
.progressive.enhanced{ filter: blur(0px); }
</style>
    <?php
  }
  function enableProgressiveBG($lowResImageSize='progressive'){
    add_image_size( $lowResImageSize.'_landscape', 40, 22 );
    add_image_size( $lowResImageSize.'_portrait', 22, 40 );
    add_action('wp_footer', 'progressiveBG');
  }
}
if(!function_exists('urbosaLoader')){
  function urbosaLoader($color='#000',$width="20"){
    $svg = '<svg width="'.$width.'" height="'.$width.'" viewBox="0 0 44 44" xmlns="http://www.w3.org/2000/svg" stroke="'.$color.'">
          <g fill="none" fill-rule="evenodd">
              <g transform="translate(5 5)" stroke-width="5">
                  <circle stroke-opacity=".5" cx="18" cy="18" r="18"/>
                  <path d="M36 18c0-9.94-8.06-18-18-18">
                      <animateTransform
                          attributeName="transform"
                          type="rotate"
                          from="0 18 18"
                          to="360 18 18"
                          dur="1s"
                          repeatCount="indefinite"/>
                  </path>
              </g>
          </g>
      </svg>';
    return "data:image/svg+xml;base64,".base64_encode($svg);
  }
}
if(!function_exists('removePortFromPath')){
  function removePortFromPath($tmpPath){
    $p     = parse_url($tmpPath);
    $path = $p['scheme'].'://'.$p['host'].$p['path'];
    if(!empty($p['query'])) $path .= '?'.$p['query'];
    return $path;
  }
}
if(!function_exists('encodeDataImage')){
  function encodeDataImage($path,$removePort=true){
    $tmpPath = $path;
    if($removePort) $tmpPath = removePortFromPath($tmpPath);
    $type = pathinfo($tmpPath, PATHINFO_EXTENSION);
    $data = file_get_contents($tmpPath);
    return 'data:image/' . $type . ';base64,' . base64_encode($data);
  }
}
/* Allows you to set AJAX content loading.*/
if(!function_exists('initAJAXContent')){
  function initAJAXContent(
      $action,
      $contentSelector,
      $loadMoreSelector, 
      $postData = array(),
      $labels= array(
        'label_loading'=> 'Loading',
        'label_not_found'=> 'Nothing has been found',
      )){
    $defaultData = array(
      'initial_page' => isset($_GET['pg'])?$_GET['pg']:0,
      'action' => $action,
      'content_container_selector'=> $contentSelector,
      'load_more_selector' => $loadMoreSelector,
      'page' => 1, // this value will be changed
      'post'=>$postData,
      'busy'=> false,
      'found'=> null,
      'per_page'=> get_option('posts_per_page'),
      'label_loading' => $labels['label_loading'],
      'label_not_found' => $labels['label_not_found'],
    );
    ?>
    <script>var optionData= <?=json_encode($defaultData)?></script>
    <?php
  }
}