Drupal.leaflet.pluginManager.register({
  fs: 'leaflet.Component:SetValues',
  init: function(data) {
    data.map.on('moveend', function(evt){
      var map = data.map;
      var coord = map.getCenter();
      var extent = [map.getBounds().getWest(), map.getBounds().getNorth(), map.getBounds().getEast(), map.getBounds().getSouth()];

      jQuery('#' + data.opt.latitude).val(coord.lat);
      jQuery('#' + data.opt.longitude).val(coord.lng);
      jQuery('#' + data.opt.zoom).val(map.getZoom());
      jQuery('#' + data.opt.extent).val(extent.join(', '));
    });
  }
});
