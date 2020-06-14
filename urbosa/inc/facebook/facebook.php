<?php 
  /* 
    Facebook integration 
  */
  class Facebook {
    private $html;
    private $messengerPageID;
    private $messengerThemeColor;
    private $host;

    function __construct($appID='',$version='v7.0'){
      ob_start();
      ?>
      <div class="fb-root"></div>
      <script>
        window.fbAsyncInit = function() {
          FB.init({
            appId      : '<?=$appID?>',
            cookie     : true,
            xfbml      : true,
            version    : '<?=$version?>'
          });
          FB.AppEvents.logPageView();   
        };
      </script>
      <?php
      $this->html = ob_get_clean();
      add_action('wp_body_open', array($this,'init'));

      /* Get host */
      if(isset($_SERVER['HTTP_X_ORIGINAL_HOST'])){
        $this->host = $_SERVER['HTTP_X_ORIGINAL_HOST'];
      }else{
        $this->host = $_SERVER['HTTP_HOST'];
      }
      
    }
    public function init(){ 
      echo $this->html;
    }
    public function messenger(){
      /* Add facebook messenger to the page */
      /* Prerequisite: 
         Get facebook page id, and whitelist domain
      */
      ?>
      <script>
        (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));</script>
      <!-- Your Chat Plugin code -->
      <div class="fb-customerchat"
        attribution=setup_tool
        page_id="<?=$this->messengerPageID?>"
        theme_color="<?=$this->messengerThemeColor?>">
      </div>
      <?php
    }
    public function login(){}
    public function addMessenger($pageID='',$themeColor='#0084ff'){
      $this->messengerPageID = $pageID;
      $this->messengerThemeColor = $themeColor;
      add_action('wp_body_open', array($this,'messenger'));
    }
    public function addLogin(){

    }
  }
?>
