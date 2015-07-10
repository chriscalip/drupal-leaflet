<?php
/**
 * @file
 * Class Map.
 */

namespace Drupal\leaflet\Types;
use Drupal\leaflet\Leaflet;

/**
 * Class Map.
 */
abstract class Map extends Object implements MapInterface {
  /**
   * @var string
   */
  protected $id;

  /**
   * {@inheritdoc}
   */
  public function init() {
    parent::init();

    foreach (Leaflet::getPluginTypes(array('map')) as $type) {
      foreach ($this->getOption($type . 's', array()) as $weight => $object) {
        if ($merge_object = Leaflet::load($type, $object)) {
          $merge_object->setWeight($weight);
          $this->getCollection()->merge($merge_object->getCollection());
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getId() {
    if (!isset($this->id)) {
      $css_map_name = drupal_clean_css_identifier($this->machine_name);
      // Use uniqid to ensure we've really an unique id - otherwise there will
      // occur issues with caching.
      $this->id = drupal_html_id('leaflet-map-' . $css_map_name . '-' . uniqid('', TRUE));
    }

    return $this->id;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $map = $this;

    $build = array();

    // Run prebuild hook to all objects who implements it.
    $map->preBuild($build, $map);

    $asynchronous = 0;
    foreach ($map->getCollection()->getFlatList() as $object) {
      // Check if this object is asynchronous.
      $asynchronous += (int) $object->isAsynchronous();
    }

    $settings = $map->getCollection()->getJS();
    $settings['map'] = $settings['map'][0];
    $settings = array(
      'data' => array(
        'leaflet' => array(
          'maps' => array(
            $map->getId() => $settings,
          ),
        ),
      ),
      'type' => 'setting',
    );

    // If this is asynchronous flag it as such.
    if ($asynchronous) {
      $settings['data']['leaflet']['maps'][$map->getId()]['map']['async'] = $asynchronous;
    }

    $attached = $map->getCollection()->getAttached();
    $attached['js'][] = $settings;

    $styles = array(
      'width' => $map->getOption('width'),
      'height' => $map->getOption('height'),
      'overflow' => 'hidden',
    );

    $css_styles = '';
    foreach ($styles as $property => $value) {
      $css_styles .= $property . ':' . $value . ';';
    }

    $build['leaflet'] = array(
      '#type' => 'container',
      '#attributes' => array(
        'id' => 'leaflet-container-' . $map->getId(),
        'style' => $css_styles,
        'class' => array(
          'contextual-links-region',
          'leaflet-container',
        ),
      ),
      'map' => array(
        '#theme' => 'html_tag',
        '#tag' => 'div',
        '#value' => '',
        '#attributes' => array(
          'id' => $map->getId(),
          'class' => array(
            'leaflet-map',
            $map->machine_name,
          ),
        ),
        '#attached' => $attached,
      ),
    );

    // If this is an asynchronous map flag it as such.
    if ($asynchronous) {
      $build['map']['#attributes']['class'][] = 'asynchronous';
    }

    $map->postBuild($build, $map);

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getOptions() {
    $export = array_change_key_case($this->getCollection()->getExport(), CASE_LOWER);
    $options = isset($this->options) ? $this->options : array();

    unset($export['map']);

    // Synchronize this item's options with its the Collection.
    foreach(Leaflet::getPluginTypes(array('map')) as $type) {
      $option = drupal_strtolower($type) . 's';
      if (isset($export[$type])) {
        $options[$option] = $export[$type];
      }
    }

    $this->options = $options;

    return $this->options;
  }

  /**
   * {@inheritdoc}
   */
  public function getJS() {
    $js = parent::getJS();
    $js['opt']['target'] = $this->getId();
    return $js;
  }
}
