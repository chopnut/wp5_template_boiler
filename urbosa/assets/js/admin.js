$ = jQuery
jQuery(document).ready(function () {
  initDisableACF()
})
function initDisableACF () {
  // Add acf=1 to top right link to check if you are editing a custom field.
  $currentMenu = $('#adminmenu').find('.wp-menu-open')
  $id = $currentMenu.attr('id')

  if ($id == 'toplevel_page_edit-post_type-acf-field-group') {
    var post = Helper.getParameterByName('post')
    if (post) {
      $statE = $('#wp-admin-bar-urbosa-theme-status a')
      if ($statE.length > 0) {
        $url = $('#wp-admin-bar-urbosa-theme-status a').attr('href')
        $statE.attr('href', $url + '&acf=1')
      }
    }
  }
  // Check if theme is live and do not let add/new
  var postType = Helper.getParameterByName('post_type')
  if (
    websiteData &&
    websiteData.is_theme_live == '1' &&
    postType &&
    postType == 'acf-field-group'
  ) {
    $('.wp-heading-inline + a').remove()
  }
}
