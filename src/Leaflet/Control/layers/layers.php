<?php
/**
 * @file
 * Control: Layers.
 */

namespace Drupal\leaflet\Control;
use Drupal\leaflet\Types\Control;

$plugin = array(
  'class' => '\\Drupal\\leaflet\\Control\\layers',
);

/**
 * Class layers.
 */
class layers extends Control {

  /**
   * {@inheritdoc}
   */
  public function optionsForm(&$form, &$form_state) {
    $form['options']['position'] = array(
      '#type' => 'select',
      '#title' => t('Position'),
      '#description' => 'The position of the control (one of the map corners).',
      '#default_value' => $this->getOption('position'),
      '#options' => array(
        'topleft' => 'Top left',
        'topright' => 'Top right',
        'bottomright' => 'Bottom right',
        'bottomleft' => 'Bottom left',
      )
    );
    $form['options']['collapsed'] = array(
      '#type' => 'checkbox',
      '#title' => t('Collapsed'),
      '#description' => 'If true, the control will be collapsed into an icon and expanded on mouse hover or touch.',
      '#default_value' => $this->getOption('collapsed'),
    );
    $form['options']['autoZIndex'] = array(
      '#type' => 'checkbox',
      '#title' => t('autoZIndex'),
      '#description' => 'If true, the control will assign zIndexes in increasing order to all of its layers so that the order is preserved when switching them on/off.',
      '#default_value' => $this->getOption('autoZIndex'),
    );
  }
}
