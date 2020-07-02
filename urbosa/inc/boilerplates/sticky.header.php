<!-- header -->
<header>
  <div class="wrapper">
    <div class="left">
      <a href="/"><img src="<?=get_stylesheet_directory_uri().'/assets/img/bca-logo.png'?>" alt="<?=wp_title().' Logo' ?>" class="logo"/></a>
    </div>
    <div class="right">&nbsp;</div>
  </div>
</header>
<div class="rogue-header">
  <div class="wrapper">
    <div class="left">&nbsp;</div>
    <div class="right">
      <?php 
        $hamburger=get_stylesheet_directory_uri().'/assets/img/icons/icon-hamburger.svg';
        $hamburgerClose=get_stylesheet_directory_uri().'/assets/img/icons/icon-close.svg';
      ?>
      <img src="<?=$hamburger?>" alt="<?=wp_title() + ' logo' ?>"
        class="hamburger"
        data-close="<?=$hamburger?>"
        data-open="<?=$hamburgerClose?>"
      />
    </div>
  </div>
</div>