Drupal.leaflet.pluginManager.register({
  fs: 'leaflet.layer.internal.tilelayer',
  init: function(data) {
    var options = data.opt;
    return new L.TileLayer(options.url, {minZoom: options.minZoom, maxZoom: options.maxZoom, attribution: options.attributions});
  }
});
