<?php
/**
 * @file
 * Layer: TileLayer.
 */

namespace Drupal\leaflet\Plugin\Layer\TileLayer;
use Drupal\leaflet\Component\Annotation\LeafletPlugin;
use Drupal\leaflet\Types\Layer;

/**
 * Class TileLayer.
 *
 * @LeafletPlugin(
 *   id = "TileLayer"
 * )
 */
class TileLayer extends Layer {

  /**
   * {@inheritdoc}
   */
  public function optionsForm(&$form, &$form_state) {
    $form['options']['url'] = array(
      '#type' => 'textfield',
      '#title' => t('URL'),
      '#default_value' => $this->getOption('url', 1),
      '#description' => t(''),
    );
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
