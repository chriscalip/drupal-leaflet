Drupal.leaflet.pluginManager.register({
  fs: 'leaflet.Component:InlineJS',
  init: function(data) {
    eval(data.opt.javascript);
  }
});
