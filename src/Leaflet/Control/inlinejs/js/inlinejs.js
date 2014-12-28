Drupal.leaflet.pluginManager.register({
  fs: 'leaflet.control.internal.inlinejs',
  init: function(data) {
    eval(data.opt.javascript);
    return control;
  }
});
