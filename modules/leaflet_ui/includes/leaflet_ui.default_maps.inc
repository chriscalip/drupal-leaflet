<?php
/**
 * @file
 * Provide default maps.
 */

/**
 * Implements hook_default_maps().
 */
function leaflet_ui_default_maps() {
  $export = array();

  $ol_map = new stdClass();
  $ol_map->disabled = FALSE;
  $ol_map->api_version = 1;
  $ol_map->machine_name = 'leaflet_map_ui_default';
  $ol_map->name = 'Default UI Map';
  $ol_map->description = 'Map used if there are no other maps defined';
  $ol_map->factory_service = 'leaflet.map.internal.Map';
  $ol_map->options = array(
    'width' => 'auto',
    'height' => '400px',
    'view' => array(
      'center' => array(
        'lat' => '0',
        'lon' => '0',
      ),
      'rotation' => '0',
      'zoom' => '2',
    ),
    'contextualLinks' => 0,
    'provideBlock' => 0,
    'provideIframe' => 1,
    'layers' => array(
      0 => 'leaflet_tilelayer_osm',
    ),
  );
  $export['leaflet_map_ui_default'] = $ol_map;

  $ol_map = new stdClass();
  $ol_map->disabled = FALSE;
  $ol_map->api_version = 1;
  $ol_map->machine_name = 'leaflet_map_ui_default2';
  $ol_map->name = 'Default UI Map OSM hot';
  $ol_map->description = 'Map used if there are no other maps defined';
  $ol_map->factory_service = 'leaflet.map.internal.Map';
  $ol_map->options = array(
    'width' => 'auto',
    'height' => '400px',
    'view' => array(
      'center' => array(
        'lat' => '0',
        'lon' => '0',
      ),
      'rotation' => '0',
      'zoom' => '2',
    ),
    'contextualLinks' => 0,
    'provideBlock' => 0,
    'provideIframe' => 1,
    'layers' => array(
      0 => 'leaflet_tilelayer_osm_hot',
    ),
  );
  $export['leaflet_map_ui_default2'] = $ol_map;

  return $export;
}
