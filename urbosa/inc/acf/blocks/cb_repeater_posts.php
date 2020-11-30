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
//    Normalise posts

if(!function_exists('cb_normalise_posts')){
  function cb_normalise_posts($posts, $viewButton = '',$category = null){
    $isProgressive = function_exists('urbosa_progressive');
    $tmp = [];
    if(!empty($posts)){
      foreach($posts as $post){
        $type='post';
        if(isset($post['object_type']) && $post['object_type']== 'cta'){
          $type = 'cta';
          $ctaPost = $post['object_cta'];
        }
  
        switch($type){
          case 'cta':
            $lowImage = $highImage = $alt = '';
            $ctaImage = $ctaPost['cta_image'];
            if($ctaImage){
              
              $lowImage = $ctaImage['sizes']['progressive_landscape'];
              $highImage = $ctaImage['sizes']['large'];
              $alt = $ctaImage['alt'];
  
              if(!$isProgressive){
                $lowImage = $highImage;
              }
            }
            $data = [
              'ID' => 0,
              'title' => $ctaPost['cta_title'],
              'excerpt' => $ctaPost['cta_description'],
              'image' => array(
                'high'=> $highImage,
                'low' => $lowImage,
                'alt' => $alt,
              ),
              'link' => ($ctaPost['cta_link']?$ctaPost['cta_link']: array(
                'url'=>'',
                'target' => '_self',
                'title'=> ''
              )),
              'date' => '',
              'post' => $ctaPost,
              'button_label' => $post['object_button_label'],
              'terms' => '',
            ];
            $tmp[]= $data;
          break;
          default: 
            $theID = $theTitle = $theExcerpt = $theDate =  $theContent = $terms = '' ;
  
  
            if(isset($post['object_type']) && $post['object_type']== 'post'){
              $thePost = $post['object_post'];
  
              if($thePost){
                $theID = $thePost->ID; 
                $theTitle = $thePost->post_title;
                $theExcerpt = $thePost->post_excerpt;
                $theDate = $thePost->post_date;
                $theContent = $thePost->post_content;
              }
  
            }else{
  
              $theID = $post['ID']; 
              $theTitle = $post['post_title'];
              $theExcerpt = $post['post_excerpt']; 
              $theDate = $post['post_date'];
              $theContent = $post['post_content'];
  
            }
  
            if(empty($theExcerpt)){
              $theExcerpt = $theContent;
            }
  
            $lowImage = $highImage = $alt = '';
            $gatewayImage = get_field('gateway_image', $theID);
  
            if($gatewayImage){
  
              $lowImage = $gatewayImage['sizes']['progressive_landscape'];
              $highImage = $gatewayImage['sizes']['large'];
              $alt = $gatewayImage['alt'];
  
            }else{
              $lowImage = getFeaturedImage($theID,'progressive_landscape');
              $highImage = getFeaturedImage($theID,'large',$alt);
            }
  
            if(!$isProgressive){
              $lowImage = $highImage;
            }
  
            $tmpTerms = [];
            if($category){
              
              switch($category['type']){
                case 'category':
                  // If taxonomy is set and not empty that means , we are forcing using custom taxonomy
                  if(isset($category['taxonomy']) && !empty($category['taxonomy'])){
                    $tmpTerms = get_the_terms($theID, $category['taxonomy']);
  
                  }else{
                    $tmpTerms = get_the_category($theID);
                  }
  
                break;
                default;
                  $tmpTerms = get_the_terms($theID, $category['taxonomy']);
  
              }
            }
  
            if(!empty($tmpTerms)){
  
              $tmpArrTerms  = array();
              foreach($tmpTerms as $term){
                $tmpArrTerms[] = $term->name;
              }
              $terms = implode(',',$tmpArrTerms);
            }
            
            // Add uncategorized
            if(
              $category['type'] == 'category' &&
              isset($category['taxonomy']) && 
              !empty($category['taxonomy']) && 
              empty($tmpTerms)){
              $terms = 'Uncategorised';
            }
            $data = [
              'ID' => $theID,
              'title' =>   $theTitle,
              'excerpt' => $theExcerpt,
              'image' => array(
                'high'=> $highImage,
                'low' => $lowImage,
                'alt' => $alt,
              ),
              'link' => array(
                'title'=> $theTitle,
                'url'=> get_permalink($theID),
                'target'=> '_self'
              ),
              'date' =>  $theDate,
              'post' => $post,
              'button_label' => $viewButton,
              'terms' => $terms
            ];
            $tmp[]= $data;
        }
      }
    }
    return $tmp;
  }
}
//--------------------------------------------------------------------------
$repeaterType = get_field('repeater_type');
$manual       = get_field('manual_objects');
$isProgressive = function_exists('urbosa_progressive');
$fillEmpty     = get_field('fill_in_empty');

$displayCategory = get_field('display_category');
$customTaxonomy   = get_field('custom_taxonomy');

$placeholder   = cb_get_placeholder_image();

?>


<div class="urbosa-block <?=$className?>">
  <?php 
    $posts =[];


    if($repeaterType == 'auto'){ 
      
      $autoOption     = get_field('auto_post_options');

      // default
      $metaArray= [];
      $orderBy = 'date';
      $order   = 'DESC';
      $categories = [];
      $taxonomies = [];
      $tmpTaxonomies = [];

      $postType = $autoOption['auto_post_type'];
      $count    = $autoOption['auto_display_count'];
      $orderBy  = $autoOption['auto_order_by'];
      $catOption = $autoOption['auto_cat_option'];
      $viewButton = $autoOption['button_label'];

      $catType   = $catOption['auto_cat_type'];
      $autoCat   = $catOption['auto_categories'];
      $autoTax   = $catOption['auto_taxonomies'];

      $category  = array(
        'type' => '',
        'taxonomy' => ''
      );
      
      $overrideArgs = array();

      if($catType == 'category' && !empty($autoCat) || $displayCategory){
        $categories = array_map('trim', explode(',', $autoCat));
        $category['type'] = 'category';

        if(!empty($customTaxonomy)){
          $category['taxonomy'] = $customTaxonomy;
        }
      }
      if($catType == 'taxonomy' && !empty($autoTax)){
        $taxonomies = array_map('trim', explode(',', $autoTax));
      }


      switch($orderBy){
        case 'dateasc':
          $order = 'ASC';
        break;
        case 'random':
          $orderBy = 'rand';
        break;
        case 'menuorder': 
          $orderBy = 'menu_order';
          $order = 'ASC';
        break;
        case 'metafield':
          $meta         = $autoOption['meta_fields_order'];
          $metaOrderBy  = $meta['orderby'];   // meta_value_num | meta_value
          $metaOrder    = $meta['order'];     //  ASC | DESC
          $metaKey      = $meta['meta_key'];  // the fieldname

          if(!empty($metaKey)){            
            $overrideArgs['orderby']    = $metaOrderBy; 
            $overrideArgs['order']      = $metaOrder; 
            $overrideArgs['meta_key']   = $metaKey; 
          }

        break;

      }

      if(!empty($taxonomies)){
        $customTaxGroup = $catOption['auto_taxonomy_option'];

        $category['type'] = 'taxonomy';
        $category['taxonomy'] = $customTaxGroup['main_taxonomy'];

        $tmpTaxonomies[] = array(
          'taxonomy' => $customTaxGroup['main_taxonomy'],
          'field' => $customTaxGroup['tax_comparison'],
          'terms' => $taxonomies
        );
      }
     


      $res = getPosts(
        $postType,
        '', 
        ['gateway_image'],    // acf 
        $categories,          // categories
        $tmpTaxonomies,       // taxonomies
        $count,               // per_page
        0, 
        $metaArray,
        $orderBy, 
        $order,
        true,
        $overrideArgs
      );
      
      
      $posts = cb_normalise_posts($res['posts'], $viewButton, $category);


    }else { // manual
      
      $posts = cb_normalise_posts($manual, '');

    }


    $styleDirection = get_field('style_direction');
    $columns        = get_field('style_columns');
    $wordsLimit     = get_field('style_words_limit');
    $styleType      = get_field('style_type');
    $styleSplitMode = get_field('style_split_mode');
    $overrideTemplate = get_field('override_template');

    if(!empty($posts)){

      // Root properties
      $mainClasses = array();
      $mainAttr  = '';
      

      if(
        $styleType=='default' || 
        $styleType=='center'  ||
        ($styleType=='split' && $styleDirection=='row')
        ){
          
        // Do nothing
        $mainClasses[] = 'mt-0';
        $mainClasses[] = 'mb-0';
      }else{

        $mainClasses[] ='is-vcentered';
        $mainClasses[] ='is-gapless';

      }

      $mainClass = implode(' ', $mainClasses);

      ?>

      <div class="columns is-multiline <?=$mainClass?> <?=$styleType?> <?=$styleSplitMode?>" <?=$mainAttr?>>
      

      <?php 

            for($n=0; $n < count($posts); $n++){

              //---------------------------
              //      Prep variables
              //---------------------------

              $post     = $posts[$n];
              $excerpt  = wp_trim_words($post['excerpt'], $wordsLimit);

              $title    = $post['title'];
              $link     = $post['link'];
              $low      = $post['image']['low'];
              $high     = $post['image']['high'];
              $alt      = $post['image']['alt'];

              $rawLow   = $low;

              $placeHolderImage = $placeholder;
              $postData     = $post['post'];
              $buttonLabel  = $post['button_label'];

              if($low && $isProgressive){
                $low = encodeDataImage($low);
              }
              if($low){
                $placeHolderImage = $low;
              }

              if(empty($high)){
                $high = $placeholder;
              }

              $imgAttr = '';
              if(is_admin()){
                $imgAttr  = "src='$high'";
              }
              //---------------------------
              //     Gather template
              //---------------------------

              $template = '';
              if(!empty($overrideTemplate)){

                  $template = str_replace('{title}',$title, $overrideTemplate);
                  $template = str_replace('{id}',$post['ID'], $template);
                  $template = str_replace('{excerpt}',$excerpt, $template);
                  $template = str_replace('{content}',$post['excerpt'], $template);
                  $template = str_replace('{image_url}',$high, $template);
                  $template = str_replace('{image_alt}',$alt, $template);
                  $template = str_replace('{permalink}',$link['url'], $template);
                  $template = str_replace('{category}',$post['terms'], $template);
                
                  if(is_admin()){
                    $imgTag   = "<img src='$high' style='width: 100%; height:auto;'/>";
                    $template = str_replace('{image_for_admin}', $imgTag, $template);
                  }else{
                    $template = str_replace('{image_for_admin}', '', $template);
                
                  }
                
                  $pregGroup = pregMatchGrouping('/{(.*?)}/', $template);

                  if(!empty($pregGroup)){
                    foreach ($pregGroup as $acfField) {
                      $fieldName  = $acfField[1];
                      $toReplace  = $acfField[0];
                      $func       = explode(':', $fieldName); 
                      $value      = '';
                
                      // -----------------------------------
                      if(!empty($fieldName)){
                        if(isset($postData[$fieldName])){
                          $value = $postData[$fieldName];
                        }else{
                          if(count($func)>0){
                            $fieldName = $func[0];
                          }
                          $value = get_field($fieldName,$post['ID']);
                        }
                
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
              }


              //---------------------------
              //        Render
              //---------------------------
              
              if($styleDirection=='column' && $styleType=='split'){
                if(!empty($template)){
                  echo $template;
                }else{

                  ?>
                  <div class="column is-12 columns is-vcentered ">
                      <div class="column pr-0 pl-0 pt-0 pb-0">
                        <figure>
                          <a href="<?=$link['url']?>" target="<?=$link['target']?>">
                            <div class="image ratio square progressive <?=$block['id']?>" data-raw-low="<?=$rawLow?>" data-low="<?=$low?>" data-high="<?=$high?>" style="background-image:url(<?=$placeHolderImage?>)">
                          </div>
                          </a>
                        </figure>
                      </div>  
                      <div class="column pr-0 pl-0">
                        <div class="title"><?=$title?></div>
                        <div class="desc pt-2 pb-4"><?=$excerpt?></div>
                        <?php 
                          if(!empty($buttonLabel)){
                            ?>
                            <div>
                              <a href="<?=$link['url']?>" class="button"><?=$buttonLabel?></a>
                             </div>
                            <?php
                          }
                        ?>
                      </div> 
                  </div>
                  <?php
                }
              
              
              }else{

                $columnClass = 'is-12';
                if($styleDirection=='row'){
                  
                  $totalCol = 12;
                  $tmpColumn = $totalCol/$columns;
                  $columnClass = 'is-'.$tmpColumn;

                  // if the column in question is not divisible by 12 make it auto.
                  if($totalCol % $columns  !=0 ){
                    $columnClass = '';
                  }
                }

              ?>
              <div class="column <?=$columnClass?> is-centered">
                <?php 
                  if(!empty($template)){
                    echo $template;
                  }else{
                    ?>
                    <div class="card">
                      <?php 
                        if($styleType=='center'){
                          ?>
                        <div class="title pt-4"><a href="<?=$link['url']?>" target="<?=$link['target']?>"><?=$title?></a></div>
                          <?php
                        }
                      ?>
                      <figure>
                        <div class="ratio tv">
                          <a href="<?=$link['url']?>" target="<?=$link['target']?>">
                            <div class="img-bg cover">
                              <img class="urbosa-lazy-load" data-src="<?=$high?>" alt="<?=$alt?>" <?=$imgAttr?>/>
                            </div>
                          </a>
                        </div>
                      </figure>
                      <?php 
                        if($styleType!=='center'){
                          ?>
                          <div class="title pt-4"><a href="<?=$link['url']?>" target="<?=$link['target']?>"><?=$title?></a></div>
                          <?php
                        }
                      ?>
                      <div class="desc pt-4 pb-4"><?=$excerpt?></div>
                      <?php 
                        if(!empty($buttonLabel)){
                          ?>
                          <div>
                            <a href="<?=$link['url']?>" class="button"><?=$buttonLabel?></a>
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
              
                // Correct missing column
                if(empty($columnClass) && $fillEmpty){
                  $count = count($posts);
                  if($columns>$count){
                    $sub = $columns-$count;
                    echo str_repeat('<div class="column"></div>', $sub);
                  }
                }
              }
            } // for (posts)



    } else {
      cb_no_resource_set('Repeater posts block', 'No posts/object found.');

    }
  ?>
</div>
<?php 
  if(!is_admin()){
    ?>
    <script>
      $(document).ready(function(){
        initProgressive('<?=$block['id']?>')
      })
    </script>
    <?php
  }
?>
</div>