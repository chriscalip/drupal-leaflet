<?php
/**
 * @file
 * Provide default layers.
 */

/**
 * Implements hook_default_layers().
 */
function leaflet_examples_default_leaflet_layers() {
  $export = array();

  $leaflet_layer = new stdClass();
  $leaflet_layer->api_version = 1;
  $leaflet_layer->machine_name = 'leaflet_examples_tilelayer_osm_fr';
  $leaflet_layer->name = 'OpenStreetMap FR';
  $leaflet_layer->description = 'OpenStreetMap layer from openstreetmap.fr';
  $leaflet_layer->factory_service = 'leaflet.layer.internal.TileLayer';
  $leaflet_layer->options = array(
    'url' => '//{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png'
  );
  $export['leaflet_examples_tilelayer_osm_fr'] = $leaflet_layer;

  $leaflet_layer = new stdClass();
  $leaflet_layer->api_version = 1;
  $leaflet_layer->machine_name = 'leaflet_examples_tilelayer_osm_de';
  $leaflet_layer->name = 'OpenStreetMap DE';
  $leaflet_layer->description = 'OpenStreetMap layer from openstreetmap.de';
  $leaflet_layer->factory_service = 'leaflet.layer.internal.TileLayer';
  $leaflet_layer->options = array(
    'url' => '//{s}.tile.openstreetmap.de/tiles/osmde/{z}/{x}/{y}.png',
  );
  $export['leaflet_examples_tilelayer_osm_de'] = $leaflet_layer;

  return $export;
}
