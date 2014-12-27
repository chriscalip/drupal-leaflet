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
      'rotation' => '0',
      'zoom' => '2',
    ),
    'contextualLinks' => 0,
    'provideBlock' => 0,
    'layers' => array(
      0 => 'leaflet_tilelayer_osm',
    ),
    'controls' => array(
      0 => 'leaflet_control_rotate',
      1 => 'leaflet_control_zoom',
      2 => 'leaflet_control_zoomslider',
      3 => 'leaflet_control_zoomtoextent',
    ),
    'interactions' => array(
      0 => 'leaflet_interaction_dragpan',
      1 => 'leaflet_interaction_dragrotate',
      2 => 'leaflet_interaction_mousewheelzoom',
    ),
    'components' => array(
      0 => 'leaflet_examples_component_map_edit_form_values',
      1 => 'leaflet_examples_component_map_edit_form_setcenter',
    ),
  );
  $export['leaflet_map_view_edit_form'] = $ol_map;

  return $export;
}
