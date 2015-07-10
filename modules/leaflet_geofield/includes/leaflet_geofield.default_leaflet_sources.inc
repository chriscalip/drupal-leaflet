<?php
/**
 * @file
 * Default sources.
 */

/**
 * Implements hook_default_sources().
 */
function leaflet_geofield_default_leaflet_sources() {
  $export = array();

  $leaflet_source = new stdClass();
  $leaflet_source->api_version = 1;
  $leaflet_source->machine_name = 'leaflet_geofield_source_openstreetmap';
  $leaflet_source->name = 'OpenStreetMap';
  $leaflet_source->description = 'Source OpenStreetMap description';
  $leaflet_source->factory_service = 'leaflet.Source:OSM';
  $leaflet_source->options = array();
  $export['leaflet_geofield_source_openstreetmap'] = $leaflet_source;

  return $export;
}
