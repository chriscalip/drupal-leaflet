<?php
/**
 * @file
 * Provide default maps.
 */

/**
 * Implements hook_default_leaflet_maps().
 */
function leaflet_ui_default_leaflet_maps() {
  $export = array();

  $leaflet_map = new stdClass();
  $leaflet_map->disabled = FALSE;
  $leaflet_map->api_version = 1;
  $leaflet_map->machine_name = 'leaflet_map_ui_default';
  $leaflet_map->name = 'Default UI Map';
  $leaflet_map->description = 'Map used if there are no other maps defined';
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
    'provideIframe' => 1,
    'layers' => array(
      0 => 'leaflet_tilelayer_default',
    ),
    'controls' => array(
      0 => 'leaflet_control_zoom',
      1 => 'leaflet_control_scale'
    )
  );
  $export['leaflet_map_ui_default'] = $leaflet_map;

  $leaflet_map = new stdClass();
  $leaflet_map->disabled = TRUE;
  $leaflet_map->api_version = 1;
  $leaflet_map->machine_name = 'leaflet_map_view_edit_form';
  $leaflet_map->name = 'Map view edit form';
  $leaflet_map->description = 'Map used in the the map edit form to get center, zoom';
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
      0 => 'leaflet_tilelayer_default',
    ),
    'controls' => array(
      0 => 'leaflet_control_zoom',
      1 => 'leaflet_control_scale'
    )
  );
  $export['leaflet_map_view_edit_form'] = $leaflet_map;

  return $export;
}
