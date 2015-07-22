Drupal.leaflet.pluginManager.register({
  fs: 'leaflet.Map:LMap',
  init: function(data) {
    return new L.map(data.opt.target, {zoomControl:false}).setView(data.opt.view.center, data.opt.view.zoom);
  },
  attach: function(context, settings) {},
  detach: function(context, settings) {}
});
