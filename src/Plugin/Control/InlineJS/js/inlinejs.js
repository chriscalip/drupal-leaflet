Drupal.leaflet.pluginManager.register({
  fs: 'leaflet.Control:InlineJS',
  init: function(data) {
    eval(data.opt.javascript);
    return control;
  }
});
