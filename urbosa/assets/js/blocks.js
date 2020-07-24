jQuery(document).ready(function ($) {

  initBlockAccordion();
})

function initBlockAccordion(){
  if($('.cb_accordion').length){
    $('.cb_accordion.trigger').click(function(){
      $(this).closest('.item').toggleClass('active')
    })
  }
}
