<?php get_header(); ?>
<article>
  <?php
  /**
   * Load the feature you want to see based on its functionality
   */

  /**
   * locate_template: Load a template similar to get_template_part()
   * Use this in your plugin to load the theme template file first before 
   * loading your default template file.Returns '' empty string if not found
   */
  require_once(locate_template(['funcs/page-templates/parts/custom-login.php']));


  ?>
</article>
<?php get_footer(); ?>