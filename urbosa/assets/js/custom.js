$(document).ready(function () {
  initLazyLoadImage()
  initSimpleParallax()
  initLoginPage()

  initSlideOutMenu({
    container: '#menu-container',
    menu: '#menu-menu-1',
    direction: 'left',
    delay: 500
  })
})
