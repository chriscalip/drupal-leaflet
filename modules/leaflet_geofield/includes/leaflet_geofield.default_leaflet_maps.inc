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

  $leaflet_map = new stdClass();
  $leaflet_map->disabled = FALSE; /* Edit this to true to make a default map disabled initially */
  $leaflet_map->api_version = 1;
  $leaflet_map->machine_name = 'leaflet_geofield_map_geofield';
  $leaflet_map->name = 'Geofield Map';
  $leaflet_map->description = '';
  $leaflet_map->factory_service = 'leaflet.map.internal.Map';
  $leaflet_map->options = array(
    'width' => 'auto',
    'height' => '300px',
    'contextualLinks' => 1,
    'provideBlock' => 1,
    'provideIframe' => 1,
    'view' => array(
      'center' => array(
        'lat' => '0',
        'lon' => '0',
      ),
      'rotation' => '0',
      'zoom' => '2',
    ),
    'layers' => array(
      0 => 'leaflet_geofield_layer_openstreetmap',
    ),
    'controls' => array(),
    'interactions' => array(
      0 => 'leaflet_interaction_doubleclickzoom',
      1 => 'leaflet_interaction_dragpan',
      2 => 'leaflet_interaction_mousewheelzoom',
    ),
    'components' => array(
      0 => 'leaflet_geofield_component_geofield',
    ),
  );
  $export['leaflet_geofield_map_geofield'] = $leaflet_map;

  return $export;
}
