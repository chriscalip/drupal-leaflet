<?php
/**
 * @file
 * Provide default controls.
 */

/**
 * Implements hook_default_controls().
 */
function openlayers_examples_default_controls() {
  $export = array();

  $ol_control = new stdClass();
  $ol_control->disabled = FALSE; /* Edit this to true to make a default ol_control disabled initially */
  $ol_control->api_version = 1;
  $ol_control->machine_name = 'openlayers_examples_control_simple_layer_switcher';
  $ol_control->name = 'Openlayers Examples simple layer switcher';
  $ol_control->description = '';
  $ol_control->factory_service = 'openlayers.control.internal.LayerSwitcher';
  $ol_control->options = array(
    'label' => 'Layers',
    'layers' => array(
      'openlayers_examples_layer_europa' => 'openlayers_examples_layer_europa',
      'openlayers_examples_layer_openstreetmap' => 'openlayers_examples_layer_openstreetmap',
    ),
    'multiselect' => 0,
  );
  $export['openlayers_examples_control_simple_layer_switcher'] = $ol_control;

  return $export;
}