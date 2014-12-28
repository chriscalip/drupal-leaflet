<?php
/**
 * @file
 * Provide default maps.
 */

/**
 * Implements hook_default_maps().
 */
function leaflet_default_maps() {
  $export = array();

  $ol_map = new stdClass();
  $ol_map->disabled = TRUE;
  $ol_map->api_version = 1;
  $ol_map->machine_name = 'leaflet_map_view_edit_form';
  $ol_map->name = 'Map view edit form';
  $ol_map->description = 'Map used in the the map edit form to get center, zoom';
  $ol_map->factory_service = 'leaflet.map.internal.Map';
  $ol_map->options = array(
    'width' => 'auto',
    'height' => '400px',
    'view' => array(
      'center' => array(
        'lat' => '0',
        'lon' => '0',
      ),
      'zoom' => '2',
    ),
    'contextualLinks' => 0,
    'provideBlock' => 0,
    'layers' => array(
      0 => 'leaflet_tilelayer_osm',
    ),
  );
  $export['leaflet_map_view_edit_form'] = $ol_map;

  return $export;
}
