Drupal.leaflet.pluginManager.register({
  fs: 'leaflet.Layer:TileLayer',
  init: function(data) {
    return new L.TileLayer(data.opt.url);
  }
});
