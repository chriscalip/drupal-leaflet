(function($) {
  "use strict";
  var message;
  var type = null;

  $(document).on('leaflet.build_start', function(event, objects) {
    console.time('Total building time');
    console.groupCollapsed("********************* Starting building " + objects.settings.map.mn + " *********************");
  });

  $(document).on('leaflet.map_pre_alter', function(event, objects) {
    console.groupCollapsed("Building map...");
    console.time('Building map');
  });
  $(document).on('leaflet.map_post_alter', function(event, objects) {
    console.timeEnd('Building map');
    console.groupEnd();
  });
  $(document).on('leaflet.sources_pre_alter', function(event, objects) {
    console.groupCollapsed("Building sources...");
    console.time('Building sources');
  });
  $(document).on('leaflet.sources_post_alter', function(event, objects) {
    console.timeEnd('Building sources');
    console.groupEnd();
  });

  $(document).on('leaflet.controls_pre_alter', function(event, objects) {
    console.groupCollapsed("Building controls...");
    console.time('Building controls');
  });
  $(document).on('leaflet.controls_post_alter', function(event, objects) {
    console.timeEnd('Building controls');
    console.groupEnd();
  });

  $(document).on('leaflet.interactions_pre_alter', function(event, objects) {
    console.groupCollapsed("Building interactions...");
    console.time('Building interactions');
  });
  $(document).on('leaflet.interactions_post_alter', function(event, objects) {
    console.timeEnd('Building interactions');
    console.groupEnd();
  });

  $(document).on('leaflet.styles_pre_alter', function(event, objects) {
    console.groupCollapsed("Building styles...");
    console.time('Building styles');
  });
  $(document).on('leaflet.styles_post_alter', function(event, objects) {
    console.timeEnd('Building styles');
    console.groupEnd();
  });

  $(document).on('leaflet.layers_pre_alter', function(event, objects) {
    console.groupCollapsed("Building layers...");
    console.time('Building layers');
  });
  $(document).on('leaflet.layers_post_alter', function(event, objects) {
    console.timeEnd('Building layers');
    console.groupEnd();
  });

  $(document).on('leaflet.components_pre_alter', function(event, objects) {
    console.groupCollapsed("Building components...");
    console.time('Building components');
  });
  $(document).on('leaflet.components_post_alter', function(event, objects) {
    console.timeEnd('Building components');
    console.groupEnd();
  });

  $(document).on('leaflet.object_pre_alter', function(event, objects) {
    if (!Drupal.leaflet.cacheManager.isRegistered(objects.data.mn)) {
      message = "Computing " + objects.type + " " + objects.data.mn + "...";
    } else {
      message = "Loading " + objects.type + " " + objects.data.mn + " from cache...";
    }
    console.groupCollapsed(message);
    console.time('Time');

  });
  $(document).on('leaflet.object_post_alter', function(event, objects) {
    console.timeEnd('Time');
    console.groupEnd();
  });

  $(document).on('leaflet.build_stop', function(event, objects) {
    console.timeEnd('Total building time');
    console.log('Cache has ' + Object.keys(Drupal.leaflet.cacheManager.getCache()).length + ' objects.');
    console.groupEnd();
  });
})(jQuery);
