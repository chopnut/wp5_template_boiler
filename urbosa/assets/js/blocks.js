jQuery(document).ready(function($){
  initMediaDimmer();
  initBlockAccordion();
  initBlockGoogleMap();

})

function initMediaDimmer(){
  $('.media-dimmer-trigger').click(function(e){
    var dimmerID = $(this).data('dimmer-id')
    if($('#'+dimmerID).length){
      var $dimmer = $('#'+dimmerID);
      e.preventDefault();
      $dimmer.dimmer('toggle');
    }
  })
}
function initBlockAccordion(){
  if($('.cb_accordion').length){
    $('.cb_accordion.trigger').click(function(){
      $(this).closest('.item').toggleClass('active')
    })
  }
}

function initBlockGoogleMap(){
  window.overlays    = []
  window.markerIcons = []

  /* Create the map */

  window.new_map = function($el){
 
    var $markers = $el.find('.marker')

    // map arguments
    var args = {
      zoom: 8,
      center: new google.maps.LatLng(0, 0),
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      disableDefaultUI: true,
      styles: [],
      zoomControl: true
    }

    // create map
    var map = new google.maps.Map($el[0], args)

    // add a markers reference
    map.markers = []

    // add markers
    $markers.each(function () {
      add_marker($(this), map)
    })

    // center map
    center_map(map)

    // return
    return map
  }

  /* Center the map */

  window.center_map = function(map) {

    // vars
    var bounds = new google.maps.LatLngBounds()


    // loop through all markers and create bounds


    $.each(map.markers, function (i, marker) {

      var latlng = new google.maps.LatLng(
        marker.position.lat(),
        marker.position.lng()
      )

      bounds.extend(latlng)
    })

    // single marker
    if (map.markers.length == 1) {

      // set center of map
      map.setCenter(bounds.getCenter())
      map.setZoom(15)

    } else {
      // multiple markers
      // fit to bounds so it all fits in one view

      map.fitBounds(bounds)
    }
  }

  /* Add marker */

  window.add_marker = function($marker, map ) {
    // vars
    var latlng = new google.maps.LatLng(
      $marker.data('lat'),
      $marker.data('lng')
    )
    customMarker = $marker.data('custom-marker')

    var markerImg =
      websiteData.stylesheet_directory_uri +
      '/assets/img/icons/map-marker.svg'
    var selectedImg =
      websiteData.stylesheet_directory_uri +
      '/assets/img/icons/map-marker.svg'

    var selectedMarker = markerImg
    if (customMarker) selectedMarker = selectedImg

    var icon = {
      url: selectedMarker, // url
      scaledSize: new google.maps.Size(30, 30), // scaled size
      origin: new google.maps.Point(0, 0), // origin
      anchor: new google.maps.Point(0, 0) // anchor
    }

    var iconSelected = {
      url: selectedImg, // url
      scaledSize: new google.maps.Size(30, 30), // scaled size
      origin: new google.maps.Point(0, 0), // origin
      anchor: new google.maps.Point(0, 0) // anchor
    }

    // create marker
    var marker = new google.maps.Marker({
      position: latlng,
      map: map,
      icon: icon,
      state: $marker.data('state')
    })

    // Property window info
    var windowInfo = $marker.data('window-info')
    var markerIconIndex = markerIcons.length
    markerIcons.push(marker)

    if (windowInfo && $('#'+windowInfo).length) {
      
      var overlaysIndex = overlays.length

      // ------- Pop up set up----------
      Popup = createPopupClass()
      $popUpWindow = $('#' + windowInfo)
      popup = new Popup(latlng, $popUpWindow.get(0))
      //-------------------------------
      overlays.push(popup)

      $popUpWindow.find('.close').click(function () {
        overlays[overlaysIndex].setMap(null)
        markerIcons[overlaysIndex].setIcon(icon)
      })

      marker.addListener('click', function () {
        overlays.map(function (item, index) {
          if (index !== overlaysIndex) {
            overlays[index].setMap(null)
          }
        })
        markerIcons.map(function (item, index) {
          if (index !== markerIconIndex) {
            markerIcons[index].setIcon(icon)
          }
        })
        markerIcons[markerIconIndex].setIcon(iconSelected)
        overlays[overlaysIndex].setMap(map)
      })

    }

    // add to array
    map.markers.push(marker)

    // if marker contains HTML/Content, add it to an infoWindow
    // infoWindow is a generic popup window not a custom one.
    
    if ($marker.html()) {
      // create info window
      var infowindow = new google.maps.InfoWindow({
        content: $marker.html()
      })

      // show info window when marker is clicked
      google.maps.event.addListener(marker, 'click', function () {
        infowindow.open(map, marker)
      })
    }
  }

  /* Create pop-up class */

  function createPopupClass () {
    /**
     * A customized popup on the map.
     * @param {!google.maps.LatLng} position
     * @param {!Element} content The bubble div.
     * @constructor
     * @extends {google.maps.OverlayView}
     */
    function Popup (position, content) {
      this.position = position

      content.classList.add('popup-bubble')

      // This zero-height div is positioned at the bottom of the bubble.
      var bubbleAnchor = document.createElement('div')
      bubbleAnchor.classList.add('popup-bubble-anchor')
      bubbleAnchor.appendChild(content)

      // This zero-height div is positioned at the bottom of the tip.
      this.containerDiv = document.createElement('div')
      this.containerDiv.classList.add('popup-container')
      this.containerDiv.appendChild(bubbleAnchor)

      // Optionally stop clicks, etc., from bubbling up to the map.
      google.maps.OverlayView.preventMapHitsAndGesturesFrom(this.containerDiv)
    }
    // ES5 magic to extend google.maps.OverlayView.
    Popup.prototype = Object.create(google.maps.OverlayView.prototype)

    /** Called when the popup is added to the map. */
    Popup.prototype.onAdd = function () {
      this.getPanes().floatPane.appendChild(this.containerDiv)
    }

    /** Called when the popup is removed from the map. */
    Popup.prototype.onRemove = function () {
      if (this.containerDiv.parentElement) {
        this.containerDiv.parentElement.removeChild(this.containerDiv)
      }
    }

    /** Called each frame when the popup needs to draw itself. */
    Popup.prototype.draw = function () {
      var divPosition = this.getProjection().fromLatLngToDivPixel(this.position)

      // Hide the popup when it is far out of view.
      var display =
        Math.abs(divPosition.x) < 4000 && Math.abs(divPosition.y) < 4000
          ? 'block'
          : 'none'

      if (display === 'block') {
        this.containerDiv.style.left = divPosition.x + 'px'
        this.containerDiv.style.top = divPosition.y + 'px'
      }
      if (this.containerDiv.style.display !== display) {
        this.containerDiv.style.display = display
      }
    }

    return Popup
  }

  /* Trigger the map! */
  $('.acf-map').each(function () {
    map = new_map($(this))
  })
}