Drupal.behaviors.leaflet = (function($) {
  "use strict";
  return {
    attach: function (context, settings) {
      Drupal.leaflet.pluginManager.attach(context, settings);

      $('.leaflet-map:not(.asynchronous)').once('leaflet-map', function () {
        var map_id = $(this).attr('id');
          if (typeof Drupal.settings.leaflet.maps[map_id] != 'undefined') {
            Drupal.leaflet.processMap(map_id, context);
          }
      });

      // Create dynamic callback functions for asynchronous maps.
      $('.leaflet-map.asynchronous').once('leaflet-map.asynchronous', function () {
        var map_id = $(this).attr('id');
          if (typeof Drupal.settings.leaflet.maps[map_id] != 'undefined') {
          Drupal.leaflet.asyncIsReadyCallbacks[map_id.replace(/[^0-9a-z]/gi, '_')] = function () {
            Drupal.leaflet.asyncIsReady(map_id);
          }
        }
      });
    },
    detach: function (context, settings) {
      Drupal.leaflet.pluginManager.detach(context, settings);
    }
  }
})(jQuery);



