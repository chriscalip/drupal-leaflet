(function ($, Drupal) {

  "use strict";

  Drupal.leaflet = {
    instances: {},
    processMap: function (map_id, context) {
      var settings = $.extend({}, {
        layer: [],
        style: [],
        control: [],
        interaction: [],
        source: [],
        projection: [],
        component: []
      }, Drupal.settings.leaflet.maps[map_id]);
      var map = false;

      // If already processed just return the instance.
      if (Drupal.leaflet.instances[map_id] !== undefined) {
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
        $(document).trigger('leaflet.map_pre_alter', [
          {
            context: context,
            settings: settings,
            map_id: map_id
          }
        ]);
        map = Drupal.leaflet.getObject(context, 'maps', settings.map, map_id);
        $(document).trigger('leaflet.map_post_alter', [{map: Drupal.leaflet.instances[map_id].map}]);

        if (settings.style.length > 0) {
          $(document).trigger('leaflet.styles_pre_alter', [
            {
              styles: settings.style,
              map_id: map_id
            }
          ]);
          settings.style.map(function (data) {
            Drupal.leaflet.getObject(context, 'styles', data, map_id);
          });
          $(document).trigger('leaflet.styles_post_alter', [
            {
              styles: settings.style,
              map_id: map_id
            }
          ]);
        }

        if (settings.source.length > 0) {
          $(document).trigger('leaflet.sources_pre_alter', [
            {
              sources: settings.source,
              map_id: map_id
            }
          ]);
          settings.source.map(function (data) {
            if (data.opt !== undefined && data.opt.attributions !== undefined) {
              data.opt.attributions = [
                new ol.Attribution({
                  'html': data.opt.attributions
                })
              ];
            }
            Drupal.leaflet.getObject(context, 'sources', data, map_id);
          });
          $(document).trigger('leaflet.sources_post_alter', [
            {
              sources: settings.source,
              map_id: map_id
            }
          ]);
        }

        if (settings.interaction.length > 0) {
          $(document).trigger('leaflet.interactions_pre_alter', [
            {
              interactions: settings.interaction,
              map_id: map_id
            }
          ]);
          settings.interaction.map(function (data) {
            var interaction = Drupal.leaflet.getObject(context, 'interactions', data, map_id);
            if (interaction) {
              map.addInteraction(interaction);
            }
          });
          $(document).trigger('leaflet.interactions_post_alter', [
            {
              interactions: settings.interaction,
              map_id: map_id
            }
          ]);
        }

        if (settings.layer.length > 0) {
          $(document).trigger('leaflet.layers_pre_alter', [
            {
              layers: settings.layer,
              map_id: map_id
            }
          ]);

          var groups = {};
          var layers = {};

          settings.layer.map(function (data, key) {
            if (data.fs === 'leaflet.Layer:Group') {
              groups[data.mn] = data;
            }
            else {
              layers[data.mn] = data;
            }
          });

          for (var i in layers) {
            var data = jQuery.extend(true, {}, layers[i]);
            data.opt.source = Drupal.leaflet.instances[map_id].sources[data.opt.source];
            if (data.opt.style !== undefined && Drupal.leaflet.instances[map_id].styles[data.opt.style] !== undefined) {
              data.opt.style = Drupal.leaflet.instances[map_id].styles[data.opt.style];
            }
            var layer = Drupal.leaflet.getObject(context, 'layers', data, map_id);

            if (layer) {
              if (data.opt.name !== undefined) {
                layer.set('title', data.opt.name);
              }
              layers[i] = layer;
            }
          }

          for (var i in groups) {
            data = jQuery.extend(true, {}, groups[i]);
            var candidates = [];
            data.opt.grouplayers.map(function (layer_machine_name) {
              candidates.push(layers[layer_machine_name]);
              delete layers[layer_machine_name];
            });
            data.opt.grouplayers = candidates;
            layer = Drupal.leaflet.getObject(context, 'layers', data, map_id);

            if (layer) {
              groups[i] = layer;
            }
          }

          $.map(layers, function (layer) {
            map.addLayer(layer);
          });

          // Todo: See why it's not ordered properly automatically.
          var groupsOrdered = [];
          for (var i in groups) {
            groupsOrdered.push(groups[i]);
          }
          groupsOrdered.reverse().map(function (layer) {
            map.addLayer(layer);
          });

          $(document).trigger('leaflet.layers_post_alter', [
            {
              layers: settings.layer,
              map_id: map_id
            }
          ]);
        }

        if (settings.control.length > 0) {
          $(document).trigger('leaflet.controls_pre_alter', [
            {
              controls: settings.control,
              map_id: map_id
            }
          ]);
          settings.control.map(function (data) {
            var control = Drupal.leaflet.getObject(context, 'controls', data, map_id);
            if (control) {
              map.addControl(control);
            }
          });
          $(document).trigger('leaflet.controls_post_alter', [
            {
              controls: settings.control,
              map_id: map_id
            }
          ]);
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
      if (Drupal.settings.leaflet.maps[map_id] !== undefined) {
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
      if (Drupal.settings.leaflet.maps[map_id] !== undefined) {
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
      if (Drupal.leaflet.instances[map_id] === undefined) {
        Drupal.leaflet.instances[map_id] = {
          map: null,
          layers: {},
          styles: {},
          controls: {},
          interactions: {},
          sources: {},
          projections: {},
          components: {}
        };
      }

      // Check if we've already an instance of this object for this map.
      if (Drupal.leaflet.instances[map_id] !== undefined && Drupal.leaflet.instances[map_id][instances_type] !== undefined) {
        if (instances_type !== 'map' && Drupal.leaflet.instances[map_id][instances_type][data.mn] !== undefined) {
          return Drupal.leaflet.instances[map_id][instances_type][data.mn];
        }
        else
          if (instances_type === 'map' && Drupal.leaflet.instances[map_id][instances_type]) {
            return Drupal.leaflet.instances[map_id][instances_type];
          }
      }

      var object = null;
      // Make sure that data.opt exist even if it's empty.
      data.opt = $.extend({}, {}, data.opt);
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
        $(document).trigger('leaflet.object_error', [
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
      }
    }),
    log: function (string) {
      if (Drupal.leaflet.console !== undefined) {
        Drupal.leaflet.console.log(string);
      }
    }
};
}(jQuery, Drupal));
