window.getParameterByName = function (name, $default = '', url = '') {
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
window.setQueryParameter = function ($nameValues = []) {
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
window.removeURLParameter = function (key, url = '') {
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
function setCookie (cname, cvalue, seconds) {
  var d = new Date()
  d.setTime(d.getTime() + seconds * 1000)
  var expires = 'expires=' + d.toUTCString()
  document.cookie = cname + '=' + cvalue + ';' + expires + ';path=/'
}
function getCookie (cname) {
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
