Drupal.leaflet.pluginManager.register({
  fs: 'leaflet.map.internal.mapbox',
  init: function(data) {
    var options = data.opt;
    console.log('mapbox');
    var map = L.map(data.opt.target, {zoomControl:false});
    map.setView(new L.LatLng(options.view.center.lat, options.view.center.lon), options.view.zoom);

    return map;
  },
  attach: function(context, settings) {},
  detach: function(context, settings) {}
});
