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
   * A unique ID for the map.
   *
   * @var string
   */
  protected $id;

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
   * @inheritDoc
   */
  public function attached() {
    $attached = parent::attached();

    $settings = $this->getCollection()->getJS();
    $settings['map'] = array_shift($settings['map']);

    $attached['js'][] = array(
      'data' => array(
        'leaflet' => array(
          'options' => array(
            'library_path' => libraries_get_path('leaflet', TRUE)
          ),
          'maps' => array(
            $this->getId() => $settings,
          ),
        ),
      ),
      'type' => 'setting',
    );

    return $attached;
  }

  /**
   * {@inheritdoc}
   */
  public function build($build = array()) {
    $map = $this;

    // Run prebuild hook to all objects who implements it.
    $map->preBuild($build, $map);

    $asynchronous = 0;
    foreach ($map->getCollection()->getFlatList() as $object) {
      // Check if this object is asynchronous.
      $asynchronous += (int) $object->isAsynchronous();
    }

    // If this is asynchronous flag it as such.
    if ($asynchronous) {
      $settings['data']['leaflet']['maps'][$map->getId()]['map']['async'] = $asynchronous;
    }

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
        'class' => array(
          'contextual-links-region',
          'leaflet-container',
        ),
      ),
      'map-container' => array(
        '#type' => 'container',
        '#attributes' => array(
          'style' => $css_styles,
          'class' => array()
        ),
        'map' => array(
          '#type' => 'container',
          '#attributes' => array(
            'id' => $map->getId(),
            'style' => $css_styles,
            'class' => array(
              'leaflet-map',
              $map->machine_name,
            ),
          ),
          '#attached' => $map->getCollection()->getAttached(),
        ),
      )
    );

    // If this is an asynchronous map flag it as such.
    if ($asynchronous) {
      $build['leaflet']['map-container']['map']['#attributes']['class'][] = 'asynchronous';
    }

    if ((bool) $this->getOption('capabilities', FALSE) === TRUE) {
      $items = array_values($this->getOption(array('capabilities', 'options', 'table'), array()));
      array_walk($items, 'check_plain');

      $variables = array(
        'items' => $items,
        'title' => '',
        'type' => 'ul',
      );

      $build['leaflet']['capabilities'] = array(
        '#weight' => 1,
        '#type' => $this->getOption(array('capabilities', 'options', 'container_type'), 'fieldset'),
        '#title' => $this->getOption(array('capabilities', 'options', 'title'), NULL),
        '#description' => $this->getOption(array('capabilities', 'options', 'description'), NULL),
        '#collapsible' => $this->getOption(array('capabilities', 'options', 'collapsible'), TRUE),
        '#collapsed' => $this->getOption(array('capabilities', 'options', 'collapsed'), TRUE),
        'description' => array(
          '#type' => 'container',
          '#attributes' => array(
            'class' => array(
              'description'
            )
          ),
          array(
            '#markup' => theme('item_list', $variables)
          )
        )
      );
    }

    $map->postBuild($build, $map);

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function optionsToObjects() {
    $import = array();

    foreach (Leaflet::getPluginTypes(array('map')) as $type) {
      foreach ($this->getOption($type . 's', array()) as $weight => $object) {
        if ($merge_object = Leaflet::load($type, $object)) {
          $merge_object->setWeight($weight);
          $import[] = $merge_object;
        }
      }
    }

    return $import;
  }

  /**
   * {@inheritdoc}
   */
  public function getJS() {
    $js = parent::getJS();
    $js['opt']['target'] = $this->getId();
    unset($js['opt']['capabilities']);
    return $js;
  }
}
