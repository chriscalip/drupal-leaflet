Drupal.leaflet = (function($){
  "use strict";
  return {
    instances: {},
    processMap: function (map_id, context) {
      var settings = $.extend({}, {layer:[], style:[], control:[], interaction:[], source: [], projection:[], component:[]}, Drupal.settings.leaflet.maps[map_id]);
      var map = false;

      // If already processed just return the instance.
      if (typeof Drupal.leaflet.instances[map_id] !== 'undefined') {
        console.log(Drupal.leaflet.instances[map_id]);
        return Drupal.leaflet.instances[map_id].map;
      }

      $(document).trigger('leaflet.build_start', [
        {
          'type': 'objects',
          'settings': settings,
          'context': context
        }
      ]);

      try {
        $(document).trigger('leaflet.map_pre_alter', [{context: context, settings: settings, map_id: map_id}]);
        map = Drupal.leaflet.getObject(context, 'maps', settings.map, map_id);
        $(document).trigger('leaflet.map_post_alter', [{map: Drupal.leaflet.instances[map_id].map}]);

        $(document).trigger('leaflet.layers_pre_alter', [{layers: settings.layer, map_id: map_id}]);
        settings.layer.map(function (data) {
          var layer = Drupal.leaflet.getObject(context, 'layers', data, map_id);
          map.addLayer(layer);
        });
        $(document).trigger('leaflet.layers_post_alter', [{layers: settings.layer, map_id: map_id}]);

        if (settings.control.length > 0) {
          $(document).trigger('leaflet.controls_pre_alter', [{controls: settings.control, map_id: map_id}]);
          settings.control.map(function (data) {
            var control = Drupal.leaflet.getObject(context, 'controls', data, map_id);
            if (control) {
              map.addControl(control);
            }
          });
          $(document).trigger('leaflet.controls_post_alter', [{controls: settings.control, map_id: map_id}]);
        }

        if (settings.component.length > 0) {
          $(document).trigger('leaflet.components_pre_alter', [{components: settings.component}]);
          settings.component.map(function (data) {
            Drupal.leaflet.getObject(context, 'components', data, map_id);
          });
        }

      } catch (e) {
        $('#' + map_id).empty();
        $(document).trigger('leaflet.build_failed', [
          {
            'error': e,
            'settings': settings,
            'context': context
          }
        ]);
        map = false;
      }

      $(document).trigger('leaflet.build_stop', [
        {
          'type': 'objects',
          'settings': settings,
          'context': context
        }
      ]);

      return map;
    },

    /**
     * Return the map instance collection of a map_id.
     *
     * @param map_id
     *   The id of the map.
     * @returns object/false
     *   The object or false if not instantiated yet.
     */
    getMapById: function (map_id) {
      if (typeof Drupal.settings.leaflet.maps[map_id] !== 'undefined') {
        // Return map if it is instantiated already.
        if (Drupal.leaflet.instances[map_id]) {
          return Drupal.leaflet.instances[map_id];
        }
      }
      return false;
    },

    // Holds dynamic created asyncIsReady callbacks for every map id.
    // The functions are named by the cleaned map id. Everything besides 0-9a-z
    // is replaced by an underscore (_).
    asyncIsReadyCallbacks: {},
    asyncIsReady: function (map_id) {
      if (typeof Drupal.settings.leaflet.maps[map_id] !== 'undefined') {
        Drupal.settings.leaflet.maps[map_id].map.async--;
        if (!Drupal.settings.leaflet.maps[map_id].map.async) {
          $('#' + map_id).once('leaflet-map', function () {
            Drupal.leaflet.processMap(map_id, document);
          });
        }
      }
    },

    /**
     * Get an object of a map.
     *
     * If it isn't instantiated yet the instance is created.
     */
    getObject: (function (context, type, data, map_id) {
      // If the type is maps the structure is slightly different.
      var instances_type = type;
      if (type == 'maps') {
        instances_type = 'map';
      }
      // Prepare instances cache.
      if (typeof Drupal.leaflet.instances[map_id] === 'undefined') {
        Drupal.leaflet.instances[map_id] = {map:null, layers:{}, controls:{}, components:{}};
      }

      // Check if we've already an instance of this object for this map.
      if (typeof Drupal.leaflet.instances[map_id] !== 'undefined' && typeof Drupal.leaflet.instances[map_id][instances_type] !== 'undefined') {
        if (instances_type != 'map' && typeof Drupal.leaflet.instances[map_id][instances_type][data.mn] !== 'undefined') {
          return Drupal.leaflet.instances[map_id][instances_type][data.mn];
        }
        else if (instances_type == 'map' && Drupal.leaflet.instances[map_id][instances_type]) {
          return Drupal.leaflet.instances[map_id][instances_type];
        }
      }

      var object = null;
      if (Drupal.leaflet.pluginManager.isRegistered(data['fs'])) {
        $(document).trigger('leaflet.object_pre_alter', [
          {
            'type': type,
            'mn': data.mn,
            'data': data,
            'map': Drupal.leaflet.instances[map_id].map,
            'objects': Drupal.leaflet.instances[map_id],
            'context': context,
            'map_id': map_id
          }
        ]);
        object = Drupal.leaflet.pluginManager.createInstance(data['fs'], {
          'data': data,
          'opt': data.opt,
          'map': Drupal.leaflet.instances[map_id].map,
          'objects': Drupal.leaflet.instances[map_id],
          'context': context,
          'map_id': map_id
        });
        $(document).trigger('leaflet.object_post_alter', [
          {
            'type': type,
            'mn': data.mn,
            'data': data,
            'map': Drupal.leaflet.instances[map_id].map,
            'objects': Drupal.leaflet.instances[map_id],
            'context': context,
            'object': object,
            'map_id': map_id
          }
        ]);

        // Store object to the instances cache.
        if (type == 'maps') {
          Drupal.leaflet.instances[map_id][instances_type] = object;
        }
        else {
          Drupal.leaflet.instances[map_id][instances_type][data.mn] = object;
        }
        return object;
      }
      else {
        Drupal.leaflet.log('fake', 'Factory service to build ' + type + ' not available: ' + data.fs);
      }
    }),
    log: function(string) {
      if (typeof Drupal.leaflet.console !== 'undefined') {
        Drupal.leaflet.console.log(string);
      }
    }
  };
})(jQuery);
