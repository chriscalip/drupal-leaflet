<?php
/**
 * @file
 * Provide default layers.
 */

/**
 * Implements hook_default_layers().
 */
function leaflet_default_leaflet_controls() {
  $export = array();

  $leaflet_control = new stdClass();
  $leaflet_control->api_version = 1;
  $leaflet_control->machine_name = 'leaflet_control_zoom';
  $leaflet_control->name = 'Leaflet: Zoom';
  $leaflet_control->description = '';
  $leaflet_control->factory_service = 'leaflet.Control:Zoom';
  $export['leaflet_control_zoom'] = $leaflet_control;

  return $export;
}
