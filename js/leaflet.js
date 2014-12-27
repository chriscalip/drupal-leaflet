Drupal.leaflet = (function($){
  "use strict";
  return {
    processMap: function (map_id, context) {
      var settings = $.extend({}, {layer:[]}, Drupal.settings.leaflet.maps[map_id]);

      $(document).trigger('leaflet.build_start', [
        {
          'type': 'objects',
          'settings': settings,
          'context': context
        }
      ]);

      try {
        $(document).trigger('leaflet.map_pre_alter', [{context: context, cache: Drupal.leaflet.cacheManager}]);
        var map = Drupal.leaflet.getObject(context, 'maps', settings.map, null);
        Drupal.leaflet.cacheManager.set(map.mn, map);
        $(document).trigger('leaflet.map_post_alter', [{map: map, cache: Drupal.leaflet.cacheManager}]);

        $(document).trigger('leaflet.layers_pre_alter', [{layers: settings.layer, cache: Drupal.leaflet.cacheManager}]);
        settings.layer.map(function (data) {
          Drupal.leaflet.cacheManager.set(data.mn, Drupal.leaflet.getObject(context, 'layers', data, map));
          map.addLayer(Drupal.leaflet.cacheManager.get(data.mn));
        });
        $(document).trigger('leaflet.layers_post_alter', [{layers: settings.layer, cache: Drupal.leaflet.cacheManager}]);

        $(document).trigger('leaflet.build_stop', [
          {
            'type': 'objects',
            'cache': Drupal.leaflet.cacheManager,
            'settings': settings,
            'context': context
          }
        ]);
      } catch (e) {
        if (typeof console != 'undefined') {
          Drupal.leaflet.console.log(e.message);
          Drupal.leaflet.console.log(e.stack);
        } else {
          $(this).text('Error during map rendering: ' + e.message);
          $(this).text('Stack: ' + e.stack);
        }
      }
    },

// Holds dynamic created asyncIsReady callbacks for every map id.
// The functions are named by the cleaned map id. Everything besides 0-9a-z
// is replaced by an underscore (_).
    asyncIsReadyCallbacks: {},
    asyncIsReady: function (map_id) {
      if (typeof Drupal.settings.leaflet.maps[map_id] != 'undefined') {
        Drupal.settings.leaflet.maps[map_id].map.async--;
        if (!Drupal.settings.leaflet.maps[map_id].map.async) {
          $('#' + map_id).once('leaflet-map', function () {
            Drupal.leaflet.processMap(map_id, document);
          });
        }
      }
    },
    getObject: (function (context, type, data, map) {
      var object = null;
      if (Drupal.leaflet.pluginManager.isRegistered(data['fs'])) {
        $(document).trigger('leaflet.object_pre_alter', [
          {
            'type': type,
            'mn': data.mn,
            'data': data,
            'map': map,
            'cache': Drupal.leaflet.cacheManager,
            'context': context
          }
        ]);
        object = Drupal.leaflet.pluginManager.createInstance(data['fs'], {
          'data': data,
          'opt': data.opt,
          'map': map,
          'cache': Drupal.leaflet.cacheManager,
          'context': context
        });
        $(document).trigger('leaflet.object_post_alter', [
          {
            'type': type,
            'mn': data.mn,
            'data': data,
            'map': map,
            'cache': Drupal.leaflet.cacheManager,
            'context': context
          }
        ]);
        return object;
      }
    }),
    getObjectFromCache: (function (context, type, data, map) {
      var object = null;
      if (!Drupal.leaflet.cacheManager.isRegistered(data.mn)) {
        object = this.getObject(context, type, data, map);
        Drupal.leaflet.cacheManager.set(data.mn, object);
      } else {
        $(document).trigger('leaflet.object_pre_alter', [
          {
            'type': type,
            'mn': data.mn,
            'data': data,
            'map': map,
            'cache': Drupal.leaflet.cacheManager,
            'context': context
          }
        ]);
        object = Drupal.leaflet.cacheManager.get(data.mn);
        $(document).trigger('leaflet.object_post_alter', [
          {
            'type': type,
            'mn': data.mn,
            'data': data,
            'map': map,
            'cache': Drupal.leaflet.cacheManager,
            'context': context
          }
        ]);      }
      return object;
    })
  };
})(jQuery);
