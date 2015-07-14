<?php
/**
 * @file
 * Class Layer.
 */

namespace Drupal\leaflet\Types;
use Drupal\leaflet\Leaflet;

/**
 * Class Layer.
 */
abstract class Layer extends Object implements LayerInterface {
  /**
   * The array containing the options.
   *
   * @var array
   */
  protected $options = array();
}
