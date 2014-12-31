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


  $leaflet_layer = new stdClass();
  $leaflet_layer->disabled = FALSE; /* Edit this to true to make a default layer disabled initially */
  $leaflet_layer->api_version = 1;
  $leaflet_layer->machine_name = 'leaflet_geofield_layer_openstreetmap';
  $leaflet_layer->name = 'Geofield OpenStreetMap';
  $leaflet_layer->description = '';
  $leaflet_layer->factory_service = 'leaflet.layer.internal.TileLayer';
  $leaflet_layer->options = array(
    'source' => 'leaflet_geofield_source_openstreetmap',
  );
  $export['leaflet_geofield_layer_openstreetmap'] = $leaflet_layer;

  return $export;
}
