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

  $leaflet_layer = new stdClass();
  $leaflet_layer->api_version = 1;
  $leaflet_layer->machine_name = 'leaflet_tilelayer_default';
  $leaflet_layer->name = 'Default layer';
  $leaflet_layer->description = '';
  $leaflet_layer->factory_service = 'leaflet.Layer:TileLayer';
  $leaflet_layer->options = array(
    'url' => '//otile4.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png',
    'minZoom' => 0,
    'maxZoom' => 0,
    'attribution' => '&copy; <a href="www.openstreetmap.org/copyright">OpenStreetMap</a>'
  );
  $export['leaflet_tilelayer_default'] = $leaflet_layer;

  return $export;
}
