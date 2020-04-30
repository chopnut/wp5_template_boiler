function getParameterByName (name, url) {
  if (!url) url = window.location.href
  name = name.replace(/[\[\]]/g, '\\$&')
  var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
    results = regex.exec(url)
  if (!results) return null
  if (!results[2]) return ''
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
function setQueryParameter ($nameValues = []) {
  $url = new URL(window.location.href)
  $param = $url.searchParams
  $nameValues.map(function ($item) {
    $param.set($item[0], $item[1])
  })
  $url.search = $param.toString()
  return $url.toString()
}
