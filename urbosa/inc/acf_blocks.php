<?php 
/* Register blocks ACF only */
if(function_exists('acf_register_block')){
  //----------------------------------------------
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
      true
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
        }

        if(!empty($img)){
          $imgTag = '<img src="'.$img.'" alt="'.$imgAlt.'" />';
        }
        
        $template = $rawTemplate;
        $template = str_replace('{image}', $imgTag, $template);

        // title, permalink, excerpt
        $permalink = get_permalink( $post['ID']);
        $titleLink = '<a href="'.$permalink.'">'.$post['post_title'].'</a>';
        $template = str_replace('{title}', $titleLink, $template);
        $template = str_replace('{permalink}',$permalink, $template);
        
        $excerpt  = $post['post_excerpt'];
        if(empty($excerpt)) {
          $excerpt = wp_trim_words($post['post_content']);
        }

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

?>
