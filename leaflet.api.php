<?php

/**
 * @file
 * Hooks for leaflet module.
 */

/**
 * This hook will be triggered before a map is built and on each of its object.
 *
 * @param array $build
 *   The render array that will be rendered later.
 * @param \Drupal\leaflet\Types\ObjectInterface $context
 *   The context, this will be an leaflet object.
 */
function hook_leaflet_object_preprocess_alter(array $build, \Drupal\leaflet\Types\ObjectInterface $context) {

}

/**
 * This hook will be triggered after a map is built and on each of its object.
 *
 * @param array $build
 *   The render array that will be rendered after this hook.
 * @param \Drupal\leaflet\Types\ObjectInterface $context
 *   The context, this will be an leaflet object.
 */
function hook_leaflet_object_postprocess_alter(array $build, \Drupal\leaflet\Types\ObjectInterface $context) {

}
