<?php
/**
 * @file
 * Provide default layers.
 */

/**
 * Implements hook_default_layers().
 */
function leaflet_default_leaflet_layers() {
  $export = array();

  $ol_layer = new stdClass();
  $ol_layer->api_version = 1;
  $ol_layer->machine_name = 'leaflet_tilelayer_osm';
  $ol_layer->name = 'OpenStreetMap';
  $ol_layer->description = 'Layer OpenStreetMap description';
  $ol_layer->factory_service = 'leaflet.layer.internal.TileLayer';
  $ol_layer->options = array(
    'url' => '//{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
  );
  $export['leaflet_tilelayer_osm'] = $ol_layer;

  $ol_layer = new stdClass();
  $ol_layer->api_version = 1;
  $ol_layer->machine_name = 'leaflet_tilelayer_osm_hot';
  $ol_layer->name = 'OpenStreetMap Hot';
  $ol_layer->description = 'Layer OpenStreetMap Hot description';
  $ol_layer->factory_service = 'leaflet.layer.internal.TileLayer';
  $ol_layer->options = array(
    'url' => '//{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png'
  );
  $export['leaflet_tilelayer_osm_hot'] = $ol_layer;

  return $export;
}
