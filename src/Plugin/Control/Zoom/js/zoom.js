Drupal.leaflet.pluginManager.register({
  fs: 'leaflet.Control:Zoom',
  init: function(data) {
    return new L.control.zoom(data.opt);
  }
});
