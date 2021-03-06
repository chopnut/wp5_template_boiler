<?php 
if(function_exists('acf_register_block')){

  // #######################################################
  // #######################################################
  /* ####             Register ACF Blocks              ####*/
  // #######################################################
  // #######################################################

  function register_blocks(){
    $acfBlocksLocation = 'inc/acf/blocks/';
    /* 
      Instruction: Copy the acf_register_block , 1 per each block set the render template.
      Create the template php file in the blocks folder. Icon should be dashicon of wordpress without the 'dashicons'
    */
    
    // 1. Urbosa Panel with options to add images left/right
    acf_register_block(array(
      'name'=> 'cb_content_panel',
      'title'=> __('Panel'),
      'description'=>__('Theme Panel'),
      'render_template'=> $acfBlocksLocation.'cb_content_panel.php',
      'category'=> 'urbosa-blocks',
      'icon'	=> 'layout',
      'keywords'=> array( 'Theme Panel' ),
    ));
    // 2. Urbosa Theme Slider
    enableProgressiveBG();
    acf_register_block(array(
      'name'=> 'cb_theme_slider',
      'title'=> __('Slider'),
      'description'=>__('Theme Slider'),
      'render_template'=> $acfBlocksLocation.'cb_theme_slider.php',
      'category'=> 'urbosa-blocks',
      'icon'	=> 'image-flip-horizontal',
      'keywords'=> array( 'Theme Slider' ),
    ));
    // 3. Repeater posts and CTA
    acf_register_block(array(
      'name'=> 'cb_repeater_posts',
      'title'=> __('Repeater Posts'),
      'description'=>__('Repeater Posts'),
      'render_template'=> $acfBlocksLocation.'cb_repeater_posts.php',
      'category'=> 'urbosa-blocks',
      'icon'	=> 'grid-view',
      'keywords'=> array( 'Repeater Posts' ),
    ));
    // 4. ACF Google Map
    acf_register_block(array(
      'name'=> 'cb_google_map',
      'title'=> __('Google Map'),
      'description'=>__('Theme Google Map'),
      'render_template'=> $acfBlocksLocation.'cb_google_map.php',
      'category'=> 'urbosa-blocks',
      'icon'	=> 'location',
      'keywords'=> array( 'Theme Google Map' ),
    ));
    // 5. Accordion
    acf_register_block(array(
      'name'=> 'cb_accordion',
      'title'=> __('Accordion'),
      'description'=>__('Theme Accordion'),
      'render_template'=> $acfBlocksLocation.'cb_accordion.php',
      'category'=> 'urbosa-blocks',
      'icon'	=> 'list-view',
      'keywords'=> array( 'Theme Accordion' ),
    ));
    // 6. Search Result
    acf_register_block(array(
      'name'=> 'cb_search_results',
      'title'=> __('Seach/Blog Results'),
      'description'=>__('Theme Seach/Blog Results'),
      'render_template'=> $acfBlocksLocation.'cb_search_results.php',
      'category'=> 'urbosa-blocks',
      'icon'	=> 'search',
      'keywords'=> array( 'Theme Seach/Blog Results' ),
    ));

    // 7. Media dimmer
    acf_register_block(array(
      'name'=> 'cb_media_dimmer',
      'title'=> __('Media Dimmer'),
      'description'=>__('Theme Media Dimmer'),
      'render_template'=> $acfBlocksLocation.'cb_media_dimmer.php',
      'category'=> 'urbosa-blocks',
      'icon'	=> 'format-video',
      'keywords'=> array( 'Theme Media Dimmer' ),
    ));
  }

  add_action('acf/init', 'register_blocks');
  //----------------------------------------------
  function theme_category_block($categories,$post){
    return array_merge($categories, array(
      array(
        'slug'=>'urbosa-blocks',
        'title'=>__('Theme','urbosa-blocks')
      )
      ));
  }
  add_filter('block_categories','theme_category_block',10,2);
}

/* common */
function cb_no_resource_set($head,$body){
  ?>
  <div class="no-resource-set">  
    <div style="text-align:center;">
      <strong><?=$head?></strong><br/>
      <?=$body?>
    </div> 
  </div>
  <?php
}
/* 
  Used by: 
  - cb_repeater_posts.php 
  - getPosts()
*/
function cb_get_placeholder_image(){
  $placeholder   = get_stylesheet_directory_uri().'/assets/img/placeholder.jpg';
  return $placeholder;
}
/* search blocks */
function cb_search_block_content($data){

    // Prepare data
 
    $initialPage    = $data['initial_page'];
    $page           = $data['page'];
    $postTypes      = $data['post']['search_post_types'];
    $rawTemplate    = $data['post']['template'];
    $postCategories = $data['post']['post_categories'];
    $perPage        = $data['per_page'];

    $postTaxonomies = $data['post']['post_taxonomies'];
    $taxMain        = $data['post']['tax_main'];
    $taxField       = $data['post']['tax_field'];
    $displayCategory= $data['post']['display_category'];
    $enablePlaceholder = $data['post']['enable_placeholder'];
    $excludes       = $data['post']['exclude_posts'];


    $s = isset($_GET['s'])?$_GET['s']:'';

    $tmpTaxonomies  = array();
    if(!empty($postTaxonomies)){
      $tmpTaxonomies[] = array(
        'taxonomy' => $taxMain,
        'field' => $taxField,
        'terms' => $postTaxonomies
      );
    }

    if($initialPage){
      
      $perPage =$initialPage*$perPage;
      $offset  = 0;
      
    }else{
      
      $offset = ($page - 1)*$perPage;
    }

    $excludes = $tmpExcludes =  explode(',', $excludes);

    // Check for skip negative value and only 1 is allowed to be negative
    if(count($excludes)>0){
      foreach($excludes as $eVal){
        $ntVal = intval($eVal);
        if($ntVal<0){
          
          $posVal = abs($ntVal);
          
          // Do initial query to grab first N postID
          $query = getPosts(
            $postTypes,
            '',
            array('gateway_image'),
            $postCategories,
            $tmpTaxonomies,
            $perPage,
            $offset,
            array(),
            'date',
            'DESC',
            true,
            array(
              'post__not_in'=> $tmpExcludes,
              's' => $s
            )
          );

          $posts = $query['posts'];
          for ($i=0; $i < count($posts); $i++) {
            if($posVal>$i){
              // Include the ID to the exclude
              $tmpExcludes[] = $posts[$i]['ID'];
            } 
          }
          break;
        }
      
      }
    }

    $query = getPosts(
      $postTypes,
      '',
      array('gateway_image'),
      $postCategories,
      $tmpTaxonomies,
      $perPage,
      $offset,
      array(),
      'date',
      'DESC',
      true,
      array(
        'post__not_in'=> $tmpExcludes,
        's' => $s
      )
    );

    // Aggregate contents

    $posts = $query['posts'];
    $contents = array(
      'found_posts' => $query['query']->found_posts,
      'render' => '',
      'posts'=> $posts
    );

    // Render 
    ob_start();
    if(!empty($posts)){
      foreach ($posts as $post) {


        // {image} {date} {excerpt} {title} {permalink} {category}

        // image
        $img = '';
        $imgAlt = '';
        $imgTag = '';

        if($post['gateway_image']){

          $img    = $post['gateway_image']['sizes']['large'];
          $imgAlt = $post['gateway_image']['alt'];

        } else if($post['featured_image']['normal']!==''){

          $img    = $post['featured_image']['normal'];
          $imgAlt = $post['featured_image']['alt'];
        } else if($enablePlaceholder){
          
          $img   = cb_get_placeholder_image();
          $imgAlt= $post['post_title'];
        }

        if(!empty($img)){
          $imgTag = '<img data-src="'.$img.'" alt="'.$imgAlt.'" class="urbosa-lazy-load" />';
        }
        
        $template = $rawTemplate;
        $template = str_replace('{image}', $imgTag, $template);

        // title, permalink, excerpt
        $permalink = get_permalink( $post['ID']);
        $titleLink = $post['post_title'];
        $template = str_replace('{title}', $titleLink, $template);
        $template = str_replace('{permalink}',$permalink, $template);
        
        $excerpt  = $post['post_excerpt'];
        if(empty($excerpt)) {
          $excerpt = wp_trim_words($post['post_content']);
        }

        $excerpt  = strip_shortcodes( $excerpt );
        $template = str_replace('{excerpt}',$excerpt, $template);

        $time = strtotime($post['post_date']);
        $date = date('d/m/Y', $time);
        $template = str_replace('{date}',$date, $template);

        // category
        $tmpTerms = [];
        $terms    = '';
        if(!empty($postCategories) || $displayCategory){
          $tmpTerms = get_the_category($post['ID']);
        }
        if(!empty($postTaxonomies)){
          $tmpTerms = get_the_terms($post['ID'], $taxMain );
        }
        if(!empty($tmpTerms)){
          $tmpArrTerms  = array();
          foreach($tmpTerms as $term){
            $tmpArrTerms[] = $term->name;
          }
          $terms = implode(',',$tmpArrTerms);
        }
        $template = str_replace('{category}',$terms, $template);

        // get manual tags

        $pregGroup = pregMatchGrouping('/{(.*)?}/', $template);
        if(!empty($pregGroup)){
          foreach ($pregGroup as $acfField) {
            $fieldName  = $acfField[1];
            $toReplace  = $acfField[0];
            $func       = explode(':', $fieldName); 
            $value      = '';

            // -----------------------------------
            if(!empty($fieldName)){
              
              if(count($func)>0){
                $fieldName = $func[0];
              }
              $value = get_field($fieldName,$post['ID']);    

              if(!empty($func) && count($func)>1){ // check for function name
                $funcName = $func[1];
                if(function_exists($funcName)){

                  // check for additional pass data
                  $extraData = null;
                  if(isset($func[2])){
                    $extraData = $func[2];
                  }

                  $value = $funcName($value, $post, $extraData);


                }
              }
            }
            // -----------------------------------

            $template = str_replace($toReplace, $value , $template);

          }
        }

        echo $template;
      }
    }
    $contents['render']= ob_get_clean();

    return $contents;
}
function cb_search_results($data){
  if(isset($_POST['data'])){
    $data = json_decode(stripslashes($_POST['data']),true);
    $searchContents = cb_search_block_content($data);
    echo $searchContents['render'];
  }
  exit;
}
add_action('wp_ajax_cb_search_results', 'cb_search_results');
add_action('wp_ajax_nopriv_cb_search_results', 'cb_search_results');//for users that are not logged in.




// #######################################################
// #######################################################
/* ####             helper functions                 ####*/
// #######################################################
// #######################################################

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
   * @param  mixed $taxonomies
   * @param  mixed $perPage
   * @param  mixed $offset
   * @param  mixed $metaArray
   * @param  mixed $orderby
   * @param  mixed $order
   * @param  mixed $withFeaturedImage 
   * @param  mixed $overrideArgs 
   * @return void
   */
  function getPosts( 
    $postType='post',  
    $slug='', 
    $acfFields = array(),  
    $categories = array(),  
    $taxonomies = array(),
    $perPage = -1,  
    $offset = 0, 
    $metaArray = array(),  
    $orderby = 'menu_order',  
    $order = 'DESC',
    $withFeaturedImage= true,
    $overrideArgs = array()
  )
  {
    $args = array(
      'post_type' => $postType,
      'post_status' => 'publish',
      'ignore_sticky_posts' => 0,
      'posts_per_page' => $perPage,
      'orderby' => $orderby,
      'order' => $order,
      'offset' => $offset,
    );
  
    if(count($categories)) {
      $args['category_name'] = implode(',', $categories);
    }
    if(count($taxonomies)){
      /* 
        Example of taxonomies:
        array(
          [
            'taxonomy' => 'advert_tag',
            'field' => 'slug',
            'terms' => 'politics',
          ]
          );
        
      */
      $args['tax_query'] = $taxonomies;

    }
    if(!empty($slug)){
      if(is_numeric($slug)){
        $args['p'] = $slug;
      } else if(is_array($slug)){
        $args['post__in'] = $slug;
      }else{
        $args['name'] = $slug;
      }
    }
  
    if (count($metaArray)) {
      /* 
        Example of metaArray:
        array(
            'relation'		=> 'AND',
            array(
              'key'	 	=> 'color',
              'value'	  	=> array('red', 'orange'),
              'compare' 	=> 'IN',
            ),
            array(
              'key'	  	=> 'featured',
              'value'	  	=> '1',
              'compare' 	=> '=',
            ),
          );
        
      */
      $args['meta_query'] = $metaArray;

    }
    
    /* use to override arguments */

    if(!empty($overrideArgs)){
      $args = array_merge($args, $overrideArgs);
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
      if($withFeaturedImage){
        $alt = '';
        
        $imgWidth  = 336;
        $imgHeight = 224;

        $imgSrc = getFeaturedImage($thisID,'large',$alt, $imgWidth,$imgHeight);
        $featImage = array(
          'normal' => $imgSrc,
          'progressive' => (function_exists('urbosa_progressive')? getFeaturedImage($thisID,'thumb') :''),
          'alt' => $alt,
          'width' => $imgWidth,
          'height' => $imgHeight,
        );
        
        if(empty($imgSrc)){

          $featImage['normal'] = cb_get_placeholder_image();
          $featImage['alt'] = 'Placeholder';
        }

        $data['featured_image'] = $featImage;
      }
      $dataArray[] = $data;
    }
    return 
      array(
        'posts' => $dataArray,
        'query' => $query,
        'args'=> $args
      );  
    
  }
}
if(!function_exists('getFeaturedImage')){  
  /**
   * getFeaturedImage of a post
   *
   * @param  mixed $postID
   * @param  mixed $size eg: 'thumbnail','medium','large','full'
   * @return string
   */
  function getFeaturedImage($postID=null, $size='medium', &$alt=NULL,&$width=NULL, &$height=NULL){
    $pID = 0;
    if($postID){
      $pID = $postID;
    } else {
      $pID = get_the_ID();
    }
    
    $thumbID = get_post_thumbnail_id( $pID);
    $alt = get_post_meta($thumbID, '_wp_attachment_image_alt', true); 
    $url = wp_get_attachment_image_src( $thumbID, $size );

    if($alt!==NULL) $alt = $alt;
    
    if(is_array($url) && count($url)>0){

      if($width!==NULL) $width = $url[1];
      if($height!==NULL) $height = $url[2];
      return $url[0];
    }
    return '';
  }
}
if(!function_exists('progressiveBG') && !function_exists('enableProgressiveBG')){
  function progressiveBG(){
    ?>
<script>
function initProgressive(className='progressive'){
  $elements = $('.'+className+':not(.enhanced)');
  for (let n = 0; n < $elements.length; n++) {
    initProgressiveSingle($('.'+ className)[n])
  }
}
function initProgressiveSingle(jEl){
    if(!jEl) return;
    var $el = $(jEl);
    var high = $el.data('high')

    if(high!==''){

      // Load hi-res
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
    }else{
      $el.addClass('enhanced')
    }
  
}
</script>
<style>
.progressive { background-position: center;background-repeat: no-repeat; background-size: cover; transform: translateZ(0); will-change: transform;}
</style>
    <?php
  }
  function enableProgressiveBG(){
    add_action('wp_footer', 'progressiveBG');
    if(!function_exists('urbosa_progressive')){ function urbosa_progressive(){}}
  }
}

?>
