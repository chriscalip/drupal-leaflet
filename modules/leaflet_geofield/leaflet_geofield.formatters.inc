<?php

/**
 * @file
 * Leaflet Geofield formatter functions.
 */

/**
 * Implements hook_field_formatter_info().
 */
function leaflet_geofield_field_formatter_info() {
  $formatters = array();
  if (!module_exists('geofield')) {
    return $formatters;
  }
  $formatters['geofield_leaflet'] = array(
    'label' => t('Leaflet'),
    'field types' => array('geofield'),
    'settings' => array('leaflet_map' => 'leaflet_geofield_map_geofield'),
  );
  return $formatters;
}

/**
 * Implements hook_field_formatter_settings_form().
 */
function leaflet_geofield_field_formatter_settings_form($field, $instance, $view_mode, $form, &$form_state) {
  $display = $instance['display'][$view_mode];
  $settings = $display['settings'];
  $element = array();
  // Map preset formatter
  if ($display['type'] == 'geofield_leaflet') {
    // Get preset options, filtered to those which have the GeoField component.
    if (!function_exists('ctools_export_crud_load_all')) {
      ctools_include('export');
    }
    $maps = ctools_export_crud_load_all('leaflet_maps');
    $map_options = array();
    foreach ($maps as $machine_name => $data) {
      /** @var \Drupal\leaflet\Map\Map $map */
      $map = leaflet_object_load('Map', $machine_name);
      foreach ($map->getComponents() as $component) {
        if ($component instanceof \Drupal\leaflet\Component\Geofield) {
          $map_options[$machine_name] = $data->name;
        }
      }
    }
    if (empty($map_options)) {
      form_set_error('leaflet_map', "Error: You have no compatible leaflet maps. Make sure that at least one map has the 'GeoField' component enabled.");
    }
    $element['leaflet_map'] = array(
      '#type' => 'select',
      '#title' => t('Leaflet Map'),
      '#default_value' => isset($settings['leaflet_map']) ? $settings['leaflet_map'] : 'leaflet_geofield_map_geofield',
      '#options' => $map_options,
      '#description' => t('Select which Leaflet map you would like to use. Only maps which have the GeoField behavior may be selected. If your preferred map is not here, add the GeoField behavior to it first. The "Draw Features" bahavior is incompatible - presets with this behavior are not shown.'),
    );
  }
  return $element;
}

/**
 * Implements hook_field_formatter_settings_summary().
 */
function leaflet_geofield_field_formatter_settings_summary($field, $instance, $view_mode) {
  $display = $instance['display'][$view_mode];
  $settings = $display['settings'];
  $leaflet_map = isset($settings['leaflet_map']) ? $settings['leaflet_map'] : 'leaflet_geofield_map_geofield';
  if ($display['type'] == 'geofield_leaflet') {
    $summary = t('Leaflet map: @map', array('@map' => $leaflet_map));
  }

  return $summary;
}

/**
 * Implements hook_field_formatter_view().
 */
function leaflet_geofield_field_formatter_view($entity_type, $entity, $field, $instance, $langcode, $items, $display) {
  $element = array();
  $settings = $display['settings'];

  if (count($items) == 0) {
    return '';
  }
  switch ($display['type']) {
    case 'geofield_leaflet':
      $machine_name = isset($settings['leaflet_map']) ? $settings['leaflet_map'] : 'leaflet_geofield_map_geofield';
      if ($map = leaflet_object_load('Map', $machine_name)) {
        $element[0] = $map->build();
      }
      break;
  }
  return $element;
}
