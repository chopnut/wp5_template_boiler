<?php 
  // ini_set('display_errors', 1);
  // ini_set('display_startup_errors', 1);
  // error_reporting(E_ALL);
  require __DIR__ . '/vendor/autoload.php';
  use MatthiasMullie\Minify;

  /* Set these variables outside if you like to override */
  global $minifyCSSExceptions, $minifyJSExceptions;
  /* ------------------------------------------------------ */
  $tmpMinifyCSSExceptions = ['wp-block-library','dashicons'];
  $tmpMinifyJSExceptions  = ['jquery'];
  
  if(isset($minifyCSSExceptions)) $tmpMinifyCSSExceptions = array_merge($tmpMinifyCSSExceptions, $minifyCSSExceptions);
  if(isset($minifyJSExceptions)) $tmpMinifyJSExceptions = array_merge($tmpMinifyJSExceptions, $minifyJSExceptions);

  if(!function_exists('pregMatchGrouping')){  
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
  // Check if wordpress is loaded
  if(defined('ABSPATH')){
    global $live;
    $strPos = strpos(__FILE__,'wp-content');
    if($strPos!==false){
      $itself = home_url('/').substr(__FILE__,$strPos);
      /* Minify resources only when $live=true or $live is not set */
      if(!isset($live) || (isset($live)) && $live){
        // minify css
        function inc_minify_style($html,$handle){
          global $itself, $tmpMinifyCSSExceptions;
          $newHtml = $html;
          $matches = pregMatchGrouping('/href=["|\'](.*?)["|\']/',$html);
  
          if(!in_array($handle, $tmpMinifyCSSExceptions)){
            if(count($matches)){
              foreach($matches as $match){
                $toReplace = $match[1];
                $replacedBy = $itself.'?path='.$toReplace;
                $newHtml = str_replace($toReplace,$replacedBy, $newHtml);
              }
            }
          }
          return $newHtml;
        }
        add_filter('style_loader_tag', 'inc_minify_style', 10,2);
        // minify js
        function inc_minify_javascript($html,$handle){
          global $itself, $tmpMinifyJSExceptions;
          $newHtml = $html;
          if(!in_array($handle, $tmpMinifyJSExceptions)){
            $matches = pregMatchGrouping('/src=["|\'](.*?)["|\']/',$html);
            if(count($matches)){
              foreach($matches as $match){
                $toReplace = $match[1];
                $replacedBy = $itself.'?path='.$toReplace;
                $newHtml = str_replace($toReplace,$replacedBy, $newHtml);
              }
            }
          }
          return $newHtml;
        }
        add_filter('script_loader_tag', 'inc_minify_javascript', 10,2);
      }
    }

  }else{
    $removePortFromLocalhost = true;
    if(isset($_GET['path'])){
      $tmpPath = $_GET['path'];
      $type   = strtolower(pathinfo(parse_url($tmpPath, PHP_URL_PATH), PATHINFO_EXTENSION));
      $p     = parse_url($tmpPath);
      $path  = $tmpPath;
      
      if($removePortFromLocalhost){
        $path = $p['scheme'].'://'.$p['host'].$p['path'];
        if(!empty($p['query'])) $path .= '?'.$p['query'];
      }

      if($type=='js' || $type =='css'){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_HOST']);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FILETIME, true);
        // Check the return value of curl_exec(), too
        $content = curl_exec($ch);
        if ($content === false) {
            throw new Exception(curl_error($ch), curl_errno($ch));
        }
        $timestamp = curl_getinfo($ch, CURLINFO_FILETIME);
        if ($timestamp != -1) { 
            $timestamp = time();
        } 
        curl_close($ch);
        if(!empty($content)){
          if($type=='js') $minifier = new Minify\JS();
          if($type=='css') $minifier = new Minify\CSS();
          header('Content-type: text/'.$type);
          // header('Content-encoding: gzip'); enable this if server supports it
          header('Cache-control: max-age=2592000, must-revalidate');
          header('Last-Modified: ' . gmdate('D, d M Y H:i:s T', $timestamp));
          if(isset($minifier) && $minifier){
            $minifier->add($content);
            echo $minifier->minify();
          }
        }
      }
    }
  }
?>