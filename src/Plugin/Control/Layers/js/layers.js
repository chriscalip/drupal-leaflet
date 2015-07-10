Drupal.leaflet.pluginManager.register({
  fs: 'leaflet.Control:Layers',
  init: function(data) {

    // @todo: fix this.

    return new L.control.layers(baseLayers, overlays);
  }
});
