Drupal.leaflet.pluginManager.register({
  fs: 'leaflet.Control:Scale',
  init: function(data) {
    return new L.control.scale(data.opt);
  }
});
