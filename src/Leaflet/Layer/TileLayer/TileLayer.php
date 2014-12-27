<?php
/**
 * @file
 * Layer: TileLayer.
 */

namespace Drupal\leaflet\Layer;
use Drupal\leaflet\Types\Layer;

$plugin = array(
  'class' => '\\Drupal\\leaflet\\Layer\\TileLayer',
);

/**
 * Class TileLayer.
 */
class TileLayer extends Layer {

  /**
   * {@inheritdoc}
   */
  public function optionsForm(&$form, &$form_state) {
    $form['options']['minZoom'] = array(
      '#type' => 'textfield',
      '#title' => t('minZoom'),
      '#default_value' => $this->getOption('minZoom', 1),
      '#description' => t(''),
    );
    $form['options']['maxZoom'] = array(
      '#type' => 'textfield',
      '#title' => t('maxZoom'),
      '#default_value' => $this->getOption('maxZoom', 1),
      '#description' => t(''),
    );
    $form['options']['attributions'] = array(
      '#type' => 'textfield',
      '#title' => t('Attributions'),
      '#default_value' => $this->getOption('attributions', 1),
      '#description' => t(''),
    );
  }

}
