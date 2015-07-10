Drupal.leaflet.pluginManager.register({
  fs: 'leaflet.Map:LMap',
  init: function(data) {
    return new L.map(data.opt.target, {zoomControl:false}).setView([0, 0], 1);
  },
  attach: function(context, settings) {},
  detach: function(context, settings) {}
});
