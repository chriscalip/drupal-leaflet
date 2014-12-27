Drupal.leaflet.pluginManager.register({
  fs: 'leaflet.map.internal.map',
  init: function(data) {
    var options = data.opt;

    var map = L.map(data.opt.target);
    map.setView(new L.LatLng(options.view.center.lat, options.view.center.lon), options.view.zoom);

    return map;
  },
  attach: function(context, settings) {},
  detach: function(context, settings) {}
});
