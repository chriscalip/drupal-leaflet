(function ($, Drupal) {

  "use strict";

  $(document).on('leaflet.build_start', function (event, objects) {
    Drupal.leaflet.console.time('Total building time');
    Drupal.leaflet.console.groupCollapsed("********************* Starting build of " + objects.settings.map.mn + " *********************");
  });
  $(document).on('leaflet.map_pre_alter', function (event, objects) {
    Drupal.leaflet.console.groupCollapsed("Building map");
    Drupal.leaflet.console.time('Building map');
  });
  $(document).on('leaflet.map_post_alter', function (event, objects) {
    Drupal.leaflet.console.timeEnd('Building map');
    Drupal.leaflet.console.groupEnd();
  });
  $(document).on('leaflet.sources_pre_alter', function (event, objects) {
    Drupal.leaflet.console.groupCollapsed("Building sources");
    Drupal.leaflet.console.time('Building sources');
  });
  $(document).on('leaflet.sources_post_alter', function (event, objects) {
    Drupal.leaflet.console.timeEnd('Building sources');
    Drupal.leaflet.console.groupEnd();
  });
  $(document).on('leaflet.controls_pre_alter', function (event, objects) {
    Drupal.leaflet.console.groupCollapsed("Building controls");
    Drupal.leaflet.console.time('Building controls');
  });
  $(document).on('leaflet.controls_post_alter', function (event, objects) {
    Drupal.leaflet.console.timeEnd('Building controls');
    Drupal.leaflet.console.groupEnd();
  });
  $(document).on('leaflet.interactions_pre_alter', function (event, objects) {
    Drupal.leaflet.console.groupCollapsed("Building interactions");
    Drupal.leaflet.console.time('Building interactions');
  });
  $(document).on('leaflet.interactions_post_alter', function (event, objects) {
    Drupal.leaflet.console.timeEnd('Building interactions');
    Drupal.leaflet.console.groupEnd();
  });
  $(document).on('leaflet.styles_pre_alter', function (event, objects) {
    Drupal.leaflet.console.groupCollapsed("Building styles");
    Drupal.leaflet.console.time('Building styles');
  });
  $(document).on('leaflet.styles_post_alter', function (event, objects) {
    Drupal.leaflet.console.timeEnd('Building styles');
    Drupal.leaflet.console.groupEnd();
  });
  $(document).on('leaflet.layers_pre_alter', function (event, objects) {
    Drupal.leaflet.console.groupCollapsed("Building layers");
    Drupal.leaflet.console.time('Building layers');
  });
  $(document).on('leaflet.layers_post_alter', function (event, objects) {
    Drupal.leaflet.console.timeEnd('Building layers');
    Drupal.leaflet.console.groupEnd();
  });
  $(document).on('leaflet.components_pre_alter', function (event, objects) {
    Drupal.leaflet.console.groupCollapsed("Building components");
    Drupal.leaflet.console.time('Building components');
  });
  $(document).on('leaflet.components_post_alter', function (event, objects) {
    Drupal.leaflet.console.timeEnd('Building components');
    Drupal.leaflet.console.groupEnd();
  });
  $(document).on('leaflet.object_pre_alter', function (event, objects) {
    Drupal.leaflet.console.groupCollapsed(objects.data.mn);
    Drupal.leaflet.console.info('Object data');
    Drupal.leaflet.console.debug(objects.data);
    Drupal.leaflet.console.time('Time');
  });
  $(document).on('leaflet.object_post_alter', function (event, objects) {
    var objType = typeof objects.object;
    if (((objType !== 'object' && objType !== 'function') || objects.object == null) && objects.type !== 'components') {
      Drupal.leaflet.console.error('Failed to create object.');
      Drupal.leaflet.console.error(objects);
    }
    Drupal.leaflet.console.timeEnd('Time');
    Drupal.leaflet.console.groupEnd();
  });
  $(document).on('leaflet.build_stop', function (event, objects) {
    Drupal.leaflet.console.timeEnd('Total building time');
    Drupal.leaflet.console.groupEnd();
    Drupal.leaflet.console.groupEnd();
  });
  $(document).on('leaflet.object_error', function (event, objects) {
    Drupal.leaflet.console.info('Object ' + objects.data.mn + ' of type ' + objects.type + ' does not provide JS plugin.');
    Drupal.leaflet.console.info('Object data');
    Drupal.leaflet.console.debug(objects.data);
  });
  $(document).on('leaflet.build_failed', function (event, objects) {
    Drupal.leaflet.console.timeEnd('Total building time');
    Drupal.leaflet.console.groupEnd();
    Drupal.leaflet.console.error(objects.error.message);
    Drupal.leaflet.console.error(objects.error.stack);
    $('#' + objects.settings.map.map_id).html('<pre><b>Error during map rendering:</b> ' + objects.error.message + '</pre>');
    $('#' + objects.settings.map.map_id).append('<pre>' + objects.error.stack + '</pre>');
  });
}(jQuery, Drupal));
