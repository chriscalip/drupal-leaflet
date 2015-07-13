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

  $(document).on('leaflet.controls_pre_alter', function(event, objects) {
    console.groupCollapsed("Building controls...");
    console.time('Building controls');
  });
  $(document).on('leaflet.controls_post_alter', function(event, objects) {
    console.timeEnd('Building controls');
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
    console.groupCollapsed("Loading " + objects.type + " " + objects.data.mn + '...');
    console.info('Object data');
    console.debug(objects.data);
    console.time('Time');
  });
  $(document).on('leaflet.object_post_alter', function(event, objects) {
    console.timeEnd('Time');
    console.groupEnd();
  });

  $(document).on('leaflet.build_stop', function(event, objects) {
    console.timeEnd('Total building time');
    console.groupEnd();
    console.groupEnd();
  });

  $(document).on('leaflet.build_failed', function(event, objects) {
    console.timeEnd('Total building time');
    console.groupEnd();
    Drupal.leaflet.console.error(objects.error.message);
    Drupal.leaflet.console.error(objects.error.stack);
    $('#' + objects.settings.map.map_id).html('<pre><b>Error during map rendering:</b> ' + objects.error.message + '</pre>');
    $('#' + objects.settings.map.map_id).append('<pre>' + objects.error.stack + '</pre>');
  });

})(jQuery);
