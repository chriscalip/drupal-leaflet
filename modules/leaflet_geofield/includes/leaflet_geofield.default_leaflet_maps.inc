<?php
/**
 * @file
 * Default maps.
 */

/**
 * Implements hook_default_maps().
 */
function leaflet_geofield_default_leaflet_maps() {
  $export = array();
  $machine_name = 'leaflet_geofield_formatter_map_geofield';
  $leaflet_map = new stdClass();
  $leaflet_map->disabled = FALSE; /* Edit this to true to make a default map disabled initially */
  $leaflet_map->api_version = 1;
  $leaflet_map->machine_name = $machine_name;
  $leaflet_map->name = 'Default Map for Leaflet Geofield';
  $leaflet_map->description = '';
  $leaflet_map->factory_service = 'leaflet.map.internal.Map';
  $leaflet_map->options = array(
    'width' => 'auto',
    'height' => '300px',
    'contextualLinks' => 1,
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
      0 => 'leaflet_geofield_layer_openstreetmap',
      1 => 'leaflet_geofield_formatter_layer_geofield',
    ),
  );
  $export[$machine_name] = $leaflet_map;

  return $export;
}
