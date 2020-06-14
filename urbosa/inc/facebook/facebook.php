<?php 
  /* 
    Facebook integration 
    Prerequisite:
    - Have a developer FB account and get APPID
    - Have page ID
  */
  class Facebook {
    private $html;
    private $messengerPageID;
    private $messengerThemeColor;
    function __construct($appID='',$version='v7.0'){
      ob_start();
      ?>
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

        (function(d, s, id){
          var js, fjs = d.getElementsByTagName(s)[0];
          if (d.getElementById(id)) {return;}
          js = d.createElement(s); js.id = id;
          js.src = "https://connect.facebook.net/en_US/sdk.js";
          fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
      </script>
      <?php
      $this->html = ob_get_clean();
      add_action('wp_body_open', array($this,'after_body'));
      
    }
    /* Private */
    public function after_body(){ 
      echo $this->html;
    }
    public function messenger(){
      /* Add facebook messenger to the page */
      ?>
      <div class="fb-customerchat"
          page_id="<?=$this->messengerPageID?>"
          logged_in_greeting="How can we help you shop today?"
          logged_out_greeting="How can we help you shop today?"
          theme_color="<?=$this->messengerThemeColor?>"
      >
      </div>
      <?php
    }
    public function _login(){

    }
    /* Public */
    public function addMessenger($pageID='',$themeColor='#5c9165'){
      $this->messengerPageID = $pageID;
      $this->messengerThemeColor = $themeColor;
      add_action('wp_body_open', array($this,'messenger'));
    }
    public function addLogin(){

    }
  }
?>
