Drupal.leaflet.pluginManager.register({
  fs: 'leaflet.control.internal.scaleline',
  init: function(data) {
    return new L.control.scale(data.opt);
  }
});
