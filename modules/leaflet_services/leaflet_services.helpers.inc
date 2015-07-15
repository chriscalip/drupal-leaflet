<?php
/**
 * @file
 * Helper functions
 */

function _leaflet_services_retrieve($name) {
  if (($map = \Drupal\leaflet\Leaflet::load('Map', $name)) == TRUE) {
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
}

function _leaflet_services_access($operation, $map) {
  $map = \Drupal\leaflet\Leaflet::load('Map', $map[0]);
  return $map == TRUE && (bool) $map->getOption('provideIframe', FALSE);
}
