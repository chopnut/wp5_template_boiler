function helper () {
  this.getParameterByName = function (name, $default = '', url = '') {
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
  this.removeURLParameter = function (key, url = '') {
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
  this.setCookie = function (cname, cvalue, seconds) {
    var d = new Date()
    d.setTime(d.getTime() + seconds * 1000)
    var expires = 'expires=' + d.toUTCString()
    document.cookie = cname + '=' + cvalue + ';' + expires + ';path=/'
  }
  this.getCookie = function (cname) {
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
  this.getTransitionEndEventName = function () {
    var transitions = {
      transition: 'transitionend',
      OTransition: 'oTransitionEnd',
      MozTransition: 'transitionend',
      WebkitTransition: 'webkitTransitionEnd'
    }
    let bodyStyle = document.body.style
    for (let transition in transitions) {
      if (bodyStyle[transition] != undefined) {
        return transitions[transition]
      }
    }
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
  this.replacePageURL = function (page) {
    ar = []
    ar.push(['pg', page])
    q = this.setQueryParameter(ar)
    window.history.pushState('Page ' + page, '', q)
  }
  this.copyAttributes = function ($src, $dst) {
    var attributes = $src.prop('attributes')
    $.each(attributes, function () {
      $dst.attr(this.name, this.value)
    })
  }
}
window.copyClonesAttributes = function($actual, $clones){
  if($clones.length){
    $actual.map(function(i,item){         
      $item = $(item)
      ID = $item.data('id');
      $clones.map(function(i2, item2){
        $cloneItem = $(item2)
        if($cloneItem.hasClass(ID)){  
          Helper.copyAttributes($item, $cloneItem)
        }
      })
    })
  }
}
window.Helper = new helper()
// Slideout menu

window.initSlideOutMenu = function (opt) {
  if (!opt) return

  window.__slideOutTimeout = null

  var ulSelector = opt.menu
  var containerSelector = opt.container
  var transEndName = Helper.getTransitionEndEventName()

  $ulMenu = $(ulSelector)
  $container = $(containerSelector)

  if ($ulMenu.length > 0 && $container.length > 0) {
    $ulMenu.children('li').map(function (i, item) {
      __slideOutMenuHelperRecursive($(item), $container, 1, opt)
    })

    __initTriggerSlideOut(opt)
    __nonTrigger(opt)

    $container.children('div').map(function (i, item) {
      // arrange z

      $item = $(item)
      $item.css('z-index', i)

      // attach end transition to open columns only
      $item[0].addEventListener(transEndName, __transEndColumn)
    })
  } else {
    console.log(
      `Warning: SlideOutMenu - (${opt.container}) or (${opt.menu}) dont exist.`
    )
  }
}
window.__slideOutMenuHelperRecursive = function ($li, $container, level, opt) {
  // Get the ID and stick it to the A tag

  liID = $li.attr('id')

  // Now check for UL inside LI and if a new div has already added with same level deep

  $ul = $li.children('ul.sub-menu')

  $existingContainer = $(`.level-${level}`)

  if ($ul.length > 0) {
    $li
      .children('a')
      .attr('data-level', 'level-' + level)
      .attr('data-step', level)
      .attr('data-ul', liID)
      .addClass(' slide-out-trigger')

    // ul is found
    var newLevel = level
    $newUlClone = null

    if ($existingContainer.length > 0) {
      $newUlClone = $ul.addClass(liID).clone() // add reference from the LI parent

      if ($newUlClone.find('ul').length > 0) {
        $newUlClone.removeClass('sub-menu')
      }

      $existingContainer.prepend($newUlClone) // only clone it not move, for mobile purposes we might need it
    } else {
      // if not found create a new container level + 1
      levelClass = 'level-' + newLevel
      $newUlClone = $ul.addClass(liID).clone() // add reference from the LI parent

      $newDiv = $(
        `<div class="${levelClass} level ${opt.direction}" data-step="${newLevel}"></div>`
      ).prepend($newUlClone)
      $container.prepend($newDiv)

      newLevel++
    }

    $newUlClone.removeClass('sub-menu')

    // then go over again its children
    $newUlClone.children('li').map(function (i, item) {
      __slideOutMenuHelperRecursive($(item), $container, newLevel, opt)
    })
  }
}
// Attach event to non-trigger
window.__nonTrigger = function (opt) {
  var nonTriggers = $(opt.container)
    .children('.level')
    .children('ul')
    .children('li')
    .find('a:not(.slide-out-trigger)')
  nonTriggers.on('mouseover', function (e) {
    __mouseOverNonTrigger(e, opt)
  })
  nonTriggers.on('mouseout', function (e) {
    __mouseOutNonTrigger(e, opt)
  })
}
window.__mouseOverNonTrigger = function (e, opt) {
  clearInterval(__slideOutTimeout)
  __slideOutTimeout = null
}

window.__mouseOutNonTrigger = function (e, opt) {
  __slideOutTimeout = setTimeout(() => {
    $(opt.container)
      .children('.open')
      .map(function (i, item) {
        var dir = opt.direction
        if (dir == 'left') dir = 'right'
        $(item)
          .css(dir, '0%')
          .removeClass('open')
      })
  }, opt.delay)
}
// Make all ul invisible when 'open' is removed from its class
window.__transEndColumn = function (e) {
  $this = $(e.target)
  if (!$this.hasClass('open')) {
    $this.children('ul').removeClass('open')
  }
}
// Attach event to triggers
window.__initTriggerSlideOut = function (opt) {
  $('.slide-out-trigger').on('mouseover', function (e) {
    clearInterval(__slideOutTimeout)
    __slideOutTimeout = null

    win = $(this).data('level') // the column class
    ulRef = $(this).data('ul') // the reference to the ul menu
    stepN = parseInt($(this).data('step')) // number of times to fill the animation
    var dir = opt.direction // direction of the animation
    if (dir == 'left') dir = 'right'

    // find they references
    $win = $(opt.container).children('.' + win)
    $ul = $win.find('ul.' + ulRef)

    if ($ul.length > 0) {
      adjust = 100 * stepN

      $win.addClass('open') // open the column and slide it
      $win.children('ul').removeClass('open') //hide all ul first
      $ul.addClass('open') // reveal the ul
      $win.css(dir, adjust + '%')
    }
  })

  $('.slide-out-trigger').on('mouseout', function (e) {
    __slideOutTimeout = setTimeout(() => {
      // Close all columns
      $(opt.container)
        .children('.open')
        .map(function (i, item) {
          var dir = opt.direction
          $item = $(item)
          if (dir == 'left') dir = 'right'
          $item.css(dir, '0%').removeClass('open')
        })
    }, opt.delay)
  })
}
/* end slide-out */
// Slide out menus/page by page
window.initSlideInMenu = function(containerSelector){
  var startIndex   = 10;
  var animation  = {
    animation: 'slide left',
    duration: 650
  }
  $menuContainer = $(containerSelector);
  $mainUl = $menuContainer.find('ul:first');

  window._slideRecursive = function($ul,level){
    $lis = $ul.children();

    $lis.map(function(key,item){

      $li    = $(item);
      $subUL = $li.find('ul:first');
      if($subUL.length){
        
        var targetID = 'target_' + $li.attr('id');
        $subUL.attr('id',targetID);         // set its parent index for referencing
        $subUL.css('z-index', startIndex+level); // set stacking

        // click parent link caret
        $parentLink = $(`<span class="parent-link"></span>`)
        $parentLink.click(function(e){
          e.preventDefault();
          $('#'+targetID).transition(animation)
        })
        $parentLink.appendTo($subUL.parent());


        // back button
        $backLi = $(`<li class="back-link"></li>`);
        $backA  = $(`<a href="javascript:;">Back</a>`);
        $backA.click(function(e){
          $(this).closest('ul').transition(animation);
        })
        $backA.appendTo($backLi);
        

        // move the subul to the main container
        $backLi.prependTo($subUL);
        $subUL.appendTo($menuContainer);
        $subUL.transition(animation);
        
        _slideRecursive($subUL, level + 1);
      }
    })

  }
  if($mainUl.length){
    _slideRecursive($mainUl,0);
    // Add body style
    $('body').append(`<style>
        ${containerSelector}{
          display: grid;
          grid-template-columns: 100%;
        }
        ${containerSelector}>*{
          grid-row-start: 1;
          grid-column-start: 1;
          background: white;
        }
        ${containerSelector} .parent-link{
          display: inline-block;
          width: 1rem;
          height: 1rem;
          border: 1px solid black;
          float: right;
          cursor: pointer;
        }
      </style>
    `);
  }
}
