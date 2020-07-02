$(document).ready(function () {
  initSliders()
  initLazyLoadImage()
  initParallax()
  initLoginPage()
})

/* pre-built */
function initLoginPage () {
  var loginLogo = $('#login h1 a')
  if (loginLogo.length > 0 && homeURL) {
    loginLogo.attr('href', homeURL)
  }
}
function initSliders () {
  if ($.slick) {
    $('.slick-sliders').slick({
      slidesToShow: 1,
      nextArrow:
        '<i class="dashicons dashicons-arrow-right-alt2 slick-nav-next"></i>',
      prevArrow:
        '<i class="dashicons dashicons-arrow-left-alt2 slick-nav-prev"></i>',
      dots: true,
      autoplay: true,
      autoplaySpeed: 5000
    })
  }
}
function initLazyLoadImage () {
  if ($.visibility) {
    $('img.lazy-load').visibility({
      type: 'image',
      transition: 'fade in',
      duration: 1000
    })
  }
}
function initParallax () {
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
