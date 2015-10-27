<?php
/**
 * @file
 * Class Source.
 */

namespace Drupal\leaflet\Types;
use Drupal\leaflet\Leaflet;

/**
 * Class Source.
 */
abstract class Source extends Object implements SourceInterface {
  /**
   * The array containing the options.
   *
   * @var array
   */
  protected $options;

}
