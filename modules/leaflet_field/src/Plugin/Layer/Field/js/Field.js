Drupal.leaflet.pluginManager.register({
  fs: 'leaflet.Layer:Field',
  init: function(data) {
    if (typeof data.opt.geojson_data !== 'undefined') {
      return new L.geoJson(data.opt.geojson_data);
    }
  }
});
