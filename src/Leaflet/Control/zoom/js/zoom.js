Drupal.leaflet.pluginManager.register({
  fs: 'leaflet.control.internal.zoom',
  init: function(data) {
    return new L.control.zoom(data.opt);
  }
});
