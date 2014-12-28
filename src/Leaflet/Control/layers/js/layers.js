Drupal.leaflet.pluginManager.register({
  fs: 'leaflet.control.internal.mouseposition',
  init: function(data) {

    // @todo: fix this.

    return new L.control.layers(baseLayers, overlays);
  }
});
