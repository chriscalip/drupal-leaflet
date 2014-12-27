<?php
/**
 * @file
 * Class leaflet_layer.
 */

namespace Drupal\leaflet\Types;

/**
 * Class leaflet_layer.
 */
abstract class Layer extends Object implements LayerInterface {

  /**
   * {@inheritdoc}
   */
  public function buildCollection() {
    parent::buildCollection();

    return $this->getCollection();
  }

  /**
   * {@inheritdoc}
   */
  public function getJS() {
    $options = $this->options;

    return array(
      'mn' => $this->machine_name,
      'fs' => strtolower($this->factory_service),
      'opt' => $options,
    );
  }

}
