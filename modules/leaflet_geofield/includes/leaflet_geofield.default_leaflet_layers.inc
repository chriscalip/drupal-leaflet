<?php
/**
 * @file
 * Default layers.
 */

/**
 * Implements hook_default_layers().
 */
function leaflet_geofield_default_leaflet_layers() {
  $export = array();

  $machine_name = 'leaflet_geofield_layer_openstreetmap';
  $leaflet_layer = new stdClass();
  $leaflet_layer->disabled = FALSE; /* Edit this to true to make a default layer disabled initially */
  $leaflet_layer->api_version = 1;
  $leaflet_layer->machine_name = $machine_name;
  $leaflet_layer->name = 'Geofield Preset OpenStreetMap';
  $leaflet_layer->description = '';
  $leaflet_layer->factory_service = 'leaflet.layer.internal.TileLayer';
  $leaflet_layer->options = array(
    'url' => '//{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
  );
  $export[$machine_name] = $leaflet_layer;

  $machine_name2 = 'leaflet_geofield_formatter_layer_geofield';
  $leaflet_layer2 = new stdClass();
  $leaflet_layer2->disabled = FALSE; /* Edit this to true to make a default layer disabled initially */
  $leaflet_layer2->api_version = 1;
  $leaflet_layer2->machine_name = $machine_name2;
  $leaflet_layer2->name = 'Geofield Marker Layer';
  $leaflet_layer2->description = "This layer is intended to display geofield data and it's entity data through token";
  $leaflet_layer2->factory_service = 'leaflet.layer.internal.GeofieldMarkerLayer';
  $leaflet_layer2->options = array(
    'displaysGeofieldInfo' => 1,
    'popupSettings' => array(
      'show' => 1,
      'text' => '',
    ),
    'markerSettings' => array(
      'clickable' => 1,
      'draggable' => 0,
      'keyboard' => 1,
      'title' => '',
      'alt' => '',
      'zIndexOffset' => 0,
      'opacity' => 1,
      'riseOnHover' => 0,
      'riseOffset' => 250,
    ),
  );
  $export[$machine_name2] = $leaflet_layer2;
  return $export;
}
