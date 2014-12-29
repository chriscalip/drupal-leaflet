<?php
/**
 * @file
 * Helper functions
 */

function _leaflet_services_retrieve($name) {
  $map = leaflet_object_load('Map', check_plain($name));
  $build = $map->build();
  $render = drupal_render($build);
  $js = drupal_get_js();
  $styles = drupal_get_css();

  return theme('leaflet_map_iframe', array(
    'page' => $render,
    'scripts' => $js,
    'styles' => $styles,
  ));
}

function _leaflet_services_access($operation, $map) {
  $map = leaflet_object_load('Map', check_plain($map[0]));
  return (bool) $map->getOption('provideIframe', FALSE);
}
