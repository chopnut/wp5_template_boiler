function helper (){
  this.getParameterByName = function(name, $default = '', url = '') {
    if (!url) url = window.location.href
    name = name.replace(/[\[\]]/g, '\\$&')
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
      results = regex.exec(url)
    if (!results) return $default
    if (!results[2]) return $default
    return decodeURIComponent(results[2].replace(/\+/g, ' '))
  }
  /*
    Modify Query URL Params
    $nameValues: array set to 
      array( 
        array ('name1', 'value1'),
        array ('name2', 'value2'),
      )
    return string
   */
  this.setQueryParameter = function ($nameValues = []) {
    $url = new URL(window.location.href)
    $param = $url.searchParams
    $nameValues.map(function ($item) {
      $param.set($item[0], $item[1])
    })
    $url.search = $param.toString()
    return $url.toString()
  }
  
  /*
    Removes a parameter from the specified URL
    defaults to current URL
   */
  this.removeURLParameter = function(key, url = '') {
    if (url == '') {
      url = window.location.href
    }
    //prefer to use l.search if you have a location/link object
    var urlparts = url.split('?')
    if (urlparts.length >= 2) {
      var prefix = encodeURIComponent(key) + '='
      var pars = urlparts[1].split(/[&;]/g)
  
      //reverse iteration as may be destructive
      for (var i = pars.length; i-- > 0; ) {
        //idiom for string.startsWith
        if (pars[i].lastIndexOf(prefix, 0) !== -1) {
          pars.splice(i, 1)
        }
      }
  
      url = urlparts[0] + '?' + pars.join('&')
      if (pars.length <= 0) {
        return urlparts[0]
      }
      return url
    } else {
      return url
    }
  }
  this.setCookie  = function(cname, cvalue, seconds) {
    var d = new Date()
    d.setTime(d.getTime() + seconds * 1000)
    var expires = 'expires=' + d.toUTCString()
    document.cookie = cname + '=' + cvalue + ';' + expires + ';path=/'
  }
  this.getCookie  = function(cname) {
    var name = cname + '='
    var decodedCookie = decodeURIComponent(document.cookie)
    var ca = decodedCookie.split(';')
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i]
      while (c.charAt(0) == ' ') {
        c = c.substring(1)
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length)
      }
    }
    return ''
  }
  this.loadAJAXContent = function (callbackFunction = null) {
    if (typeof optionData !== 'undefined') {
      let page = optionData.page
      let contentSelector = optionData.content_container_selector
      let loadMoreSelector = optionData.load_more_selector
      let labelLoading = optionData.label_loading
      let labelNotFound = optionData.label_not_found
      let perPage = parseInt(optionData.per_page)
  
      if ($(contentSelector).length > 0 && !optionData.busy) {
        if ($(contentSelector).html() == '') {
          $(contentSelector).html(labelLoading)
        }
        $(loadMoreSelector).attr('disabled', 'true')
  
        optionData.busy = true
        $.ajax({
          url: '/wp-admin/admin-ajax.php',
          type: 'POST',
          data: 'action=' + action + '&data=' + JSON.stringify(optionData),
          success: function (results) {
            optionData.page = parseInt(optionData.page) + 1
            optionData.busy = false
            $(contentSelector).append(results)
            if (results == '') {
              $(loadMoreSelector).remove()
              if (page == 1) {
                $(contentSelector).html(
                  `<div class='not-found'>${labelNotFound}</div>`
                )
              }
              optionData.found = 0
            } else {
              if (typeof foundPosts !== 'undefined') {
                pages = Math.ceil(foundPosts / perPage)
                if (page < pages) {
                  $(loadMoreSelector)
                    .css('display', 'inline-block')
                    .attr('disabled', false)
                } else {
                  $(loadMoreSelector).remove()
                }
              }
              optionData.found = foundPosts
            }
            /* URL */
            this.replacePageURL(page)
            if (callbackFunction) {
              callbackFunction(results, optionData)
            }
          }
        })
      }
    }
  }
  this.replacePageURL = function(page) {
    ar = []
    ar.push(['pg', page])
    q = setQueryParameter(ar)
    window.history.pushState('Page ' + page, '', q)
  }
}
window.Helper = new helper()
// Slideout menu
window.__slideOutTimeout = null
window.__slideOutDelay   = 1000
  
window.initSlideOutMenu = function(opt) {
  if(!opt) return 

  var ulSelector = opt.menu 
  var containerSelector = opt.container

  $ulMenu = $(ulSelector)
  $container = $(containerSelector)

  if ($ulMenu.length > 0 && $container.length>0) {
    $ulMenu.children('li').map(function (i, item) {
      __slideOutMenuHelperRecursive($(item), $container, 1, opt)
    })

    __initTriggerSlideOut(opt)
    __nonTrigger(opt)
    $container.children('div').map(function(i,item){
      $(item).css('z-index', i);
    })
  }
}
window.__slideOutMenuHelperRecursive = function ($li, $container, level, opt) {
  // Get the ID and stick it to the A tag


  $liID = $li.attr('id')

  // Now check for UL inside LI and if a new div has already added with same level deep

  $ul = $li.children('ul.sub-menu')

  $existingContainer = $(`.level-${level}`)

  if ($ul.length > 0) {
    $li
      .children('a')
      .attr('data-level', 'level-' + level)
      .attr('data-step', level)
      .addClass($liID + ' slide-out-trigger')

    // ul is found
    var newLevel = level
    $newUlClone = null

    if ($existingContainer.length > 0) {
      $newUlClone = $ul.addClass($liID).clone() // add reference from the LI parent
      

      if ($newUlClone.find('ul').length > 0) {
        $newUlClone.removeClass('sub-menu')
      }

      $existingContainer.prepend($newUlClone) // only clone it not move, for mobile purposes we might need it
    } else {
      // if not found create a new container level + 1
      levelClass = 'level-' + newLevel
      $newUlClone = $ul.addClass($liID).clone() // add reference from the LI parent
      


      $newDiv = $(`<div class="${levelClass} level ${opt.direction}" data-step="${newLevel}"></div>`).prepend(
        $newUlClone
      )
      $container.prepend($newDiv)

      newLevel++
    }

    $newUlClone.removeClass('sub-menu')

    // then go over again its children
    $newUlClone.children('li').map(function (i, item) {
      __slideOutMenuHelperRecursive($(item), $container, newLevel,opt)
    })
  }
}
window.__nonTrigger= function (opt){

  var nonTriggers = $(opt.container).children('.level').children('ul').children('li').find('a:not(.slide-out-trigger)')
  nonTriggers.on('mouseover',function(e){
    __mouseOverNonTrigger(e,opt)
  })
  nonTriggers.on('mouseout',function(e){
    __mouseOutNonTrigger(e,opt)
  })
  
}
 window.__mouseOverNonTrigger = function(e,opt){

  clearInterval(__slideOutTimeout)
  __slideOutTimeout = null

  $col = $(e.target).closest('.level')

  ownLevel = $col.data('step');


  $(opt.container).children('.open').map(function(i,item){

    var otherLevel = $(item).data('step')


    if(otherLevel == ownLevel){
     
      // do nothing

    }else{
  
      var dir = opt.direction
      if(dir == 'left') dir = 'right'
      $(item).css(dir, '0%')
      
    }

  })
 }


 window.__mouseOutNonTrigger = function(e,opt){

  __slideOutTimeout = setTimeout(() => {

    $(opt.container).children('.open').map(function(i,item){
      var dir = opt.direction
      if(dir == 'left') dir = 'right'
      $(item).css(dir, '0%').removeClass('open')

      console.log('was closing it all along', e.target, item);

    })

  }, __slideOutDelay);
}
 window.__initTriggerSlideOut = function(opt) {


  $('.slide-out-trigger').on('mouseover', function (e) {
    clearInterval(__slideOutTimeout)
    __slideOutTimeout =null

    $winItem = $(this).data('level')
    $stepN   = parseInt($(this).data('step'))

    $adjust = 100 * $stepN;

    $('.' + $winItem).addClass('open')
    var dir = opt.direction
    if(dir == 'left') dir = 'right'
    $('.' + $winItem).css(dir, $adjust+'%')

   
  })

  $('.slide-out-trigger').on('mouseout', function (e) {
 
   
    __slideOutTimeout = setTimeout(() => {

      $(opt.container).children('.open').map(function(i,item){
        var dir = opt.direction
        if(dir == 'left') dir = 'right'
        $(item).css(dir, '0%').removeClass('open')
      console.log('just closed another');


      })

    }, __slideOutDelay);
  })
}
/* end slide-out */

window.initLoginPage = function() {
  var loginLogo = $('#login h1 a')
  if (loginLogo.length > 0 && homeURL) {
    loginLogo.attr('href', homeURL)
  }
}


window.initLazyLoadImage = function() {
  if ($.visibility) {
    $('img.urbosa-lazy-load').visibility({
      type: 'image',
      transition: 'fade in',
      duration: 1000
    })
  }
}
window.initSimpleParallax = function() {
  var parallaxClass = 'parallax'
  if (
    $('.' + parallaxClass).length > 0 &&
    typeof simpleParallax === 'function'
  ) {
    var image = document.getElementsByClassName(parallaxClass)
    new simpleParallax(image)
  }
}