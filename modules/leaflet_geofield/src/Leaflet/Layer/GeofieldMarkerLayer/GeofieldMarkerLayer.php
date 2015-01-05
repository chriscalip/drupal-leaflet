<?php
/**
 * @file
 * Layer: GeofieldMarkerLayer.
 */

namespace Drupal\leaflet\Layer;
use Drupal\leaflet\Types\Layer;

$plugin = array(
  'class' => '\\Drupal\\leaflet\\Layer\\GeofieldMarkerLayer',
);

/**
 * Class GeofieldMarkerLayer.
 */
class GeofieldMarkerLayer extends Layer {

  /**
   * {@inheritdoc}
   */
  public function optionsForm(&$form, &$form_state) {
    $form['options']['displaysGeofieldInfo'] = array(
      '#type' => 'checkbox',
      '#title' => t('Displays Geofield Related Information'),
      '#default_value' => $this->getOption('displaysGeofieldInfo', 1),
      '#description' => t("This layer is intended to display geofield data and it's entity data through token"),
    );
  }

}
