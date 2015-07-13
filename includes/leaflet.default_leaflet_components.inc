<?php
/**
 * @file
 * Provide default components.
 */

/**
 * Implements hook_default_layers().
 */
function leaflet_default_leaflet_components() {
  $export = array();

  $ol_component = new stdClass();
  $ol_component->disabled = TRUE; /* Edit this to true to make a default component disabled initially */
  $ol_component->api_version = 1;
  $ol_component->machine_name = 'leaflet_component_map_edit_form_values';
  $ol_component->name = 'Map edit form values';
  $ol_component->description = 'Provides values for the center, rotation and zoom in the map edit form.';
  $ol_component->factory_service = 'leaflet.Component:SetValues';
  $ol_component->options = array(
    'latitude' => 'edit-options-view-center-lat',
    'longitude' => 'edit-options-view-center-lon',
    'rotation' => 'edit-options-view-rotation',
    'zoom' => 'edit-options-view-zoom',
    'extent' => 'edit-options-view-extent',
  );
  $export['leaflet_component_map_edit_form_values'] = $ol_component;

  return $export;
}
