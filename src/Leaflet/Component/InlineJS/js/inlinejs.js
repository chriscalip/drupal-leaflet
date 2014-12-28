Drupal.leaflet.pluginManager.register({
  fs: 'leaflet.component.internal.inlinejs',
  init: function(data) {
    eval(data.opt.javascript);
  }
});
