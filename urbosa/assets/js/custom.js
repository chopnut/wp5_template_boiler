$(document).ready(function () {
  initLazyLoadImage()
  initSimpleParallax()
  initLoginPage()
})

function initLoginPage () {
  var loginLogo = $('#login h1 a')
  if (loginLogo.length > 0 && homeURL) {
    loginLogo.attr('href', homeURL)
  }
}

/* pre-built */
function initLazyLoadImage () {
  if ($.visibility) {
    $('img.urbosa-lazy-load').visibility({
      type: 'image',
      transition: 'fade in',
      duration: 1000
    })
  }
}
function initSimpleParallax () {
  var parallaxClass = 'parallax'
  if (
    $('.' + parallaxClass).length > 0 &&
    typeof simpleParallax === 'function'
  ) {
    var image = document.getElementsByClassName(parallaxClass)
    new simpleParallax(image)
  }
}
function mcFormCallback (result) {
  console.log('Mailchimp response:', result)
}
