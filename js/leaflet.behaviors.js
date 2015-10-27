(function ($, Drupal) {

  "use strict";

  Drupal.behaviors.leaflet = {
      attach: function (context, settings) {
        Drupal.leaflet.pluginManager.attach(context, settings);

        $('.leaflet-map:not(.asynchronous)', context).once('leaflet-map', function () {
          var map_id = $(this).attr('id');
          if (Drupal.settings.leaflet.maps[map_id] !== undefined) {
            Drupal.leaflet.processMap(map_id, context);
          }
        });

        // Create dynamic callback functions for asynchronous maps.
        $('.leaflet-map.asynchronous', context).once('leaflet-map.asynchronous', function () {
          var map_id = $(this).attr('id');
          if (Drupal.settings.leaflet.maps[map_id] !== undefined) {
            Drupal.leaflet.asyncIsReadyCallbacks[map_id.replace(/[^0-9a-z]/gi, '_')] = function () {
              Drupal.leaflet.asyncIsReady(map_id);
            };
          }
        });
      },
      detach: function (context, settings) {
        Drupal.leaflet.pluginManager.detach(context, settings);
      }
  };
}(jQuery, Drupal));
