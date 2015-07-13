<?php
/**
 * @file
 * Provide default controls.
 */

/**
 * Implements hook_default_controls().
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

  $leaflet_control = new stdClass();
  $leaflet_control->api_version = 1;
  $leaflet_control->machine_name = 'leaflet_control_scale';
  $leaflet_control->name = 'Leaflet: Scale';
  $leaflet_control->description = '';
  $leaflet_control->factory_service = 'leaflet.Control:Scale';
  $export['leaflet_control_scale'] = $leaflet_control;

  return $export;
}
