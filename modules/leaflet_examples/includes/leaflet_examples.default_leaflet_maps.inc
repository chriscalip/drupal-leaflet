<?php
/**
 * @file
 * Provide default maps.
 */

/**
 * Implements hook_default_maps().
 */
function leaflet_examples_default_leaflet_maps() {
  $export = array();

  $leaflet_map = new stdClass();
  $leaflet_map->disabled = FALSE;
  $leaflet_map->api_version = 1;
  $leaflet_map->machine_name = 'leaflet_examples_map_osm_fr';
  $leaflet_map->name = 'Map with Openstreetmap FR layer';
  $leaflet_map->description = '';
  $leaflet_map->factory_service = 'leaflet.Map:LMap';
  $leaflet_map->options = array(
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
      0 => 'leaflet_examples_tilelayer_osm_fr',
    ),
    'controls' => array(
      0 => 'leaflet_control_zoom',
      1 => 'leaflet_control_scale'
    )
  );
  $export['leaflet_examples_map_osm_fr'] = $leaflet_map;

  $leaflet_map = new stdClass();
  $leaflet_map->disabled = FALSE;
  $leaflet_map->api_version = 1;
  $leaflet_map->machine_name = 'leaflet_examples_map_osm_de';
  $leaflet_map->name = 'Map with Openstreetmap DE layer';
  $leaflet_map->description = '';
  $leaflet_map->factory_service = 'leaflet.Map:LMap';
  $leaflet_map->options = array(
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
      0 => 'leaflet_examples_tilelayer_osm_de',
    ),
    'controls' => array(
      0 => 'leaflet_control_zoom',
      1 => 'leaflet_control_scale'
    )
  );
  $export['leaflet_examples_map_osm_de'] = $leaflet_map;

  return $export;
}
