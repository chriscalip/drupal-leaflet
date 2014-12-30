<?php
/**
 * @file
 * Default components.
 */

/**
 * Implements hook_default_components().
 */
function leaflet_geofield_default_leaflet_components() {
  $export = array();

  $leaflet_component = new stdClass();
  $leaflet_component->disabled = FALSE; /* Edit this to true to make a default component disabled initially */
  $leaflet_component->api_version = 1;
  $leaflet_component->machine_name = 'leaflet_geofield_component_geofield';
  $leaflet_component->name = 'Leaflet Geofield';
  $leaflet_component->description = '';
  $leaflet_component->factory_service = 'leaflet.component.internal.Geofield';
  $leaflet_component->options = array(
    'dataType' => array(
      'GeoJSON' => 'GeoJSON',
      'KML' => 'KML',
      'GPX' => 'GPX',
      'WKT' => 'WKT',
    ),
    'actionFeature' => array(
      'draw' => 'draw',
      'modify' => 'modify',
    ),
    'typeOfFeature' => array(
      'Point' => 'Point',
      'LineString' => 'LineString',
      'Polygon' => 'Polygon',
    ),
    'featureLimit' => 0,
    'showInputField' => 1,
    'inputFieldName' => 'geom',
    'initialData' => '',
    'dataProjection' => 'EPSG:4326',
  );
  $export['leaflet_geofield_component_geofield'] = $leaflet_component;

  return $export;
}
