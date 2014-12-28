Drupal.leaflet.pluginManager.register({
  fs: 'leaflet.control.internal.attribution',
  init: function(data) {

    var options = {};
    options.position = data.opt.position;
    if (data.opt.prefix != '') {
      options.prefix = data.opt.position;
    }

    return new L.control.attribution(options).addAttribution(data.opt.text);
  }
});
