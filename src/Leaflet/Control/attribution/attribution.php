<?php
/**
 * @file
 * Control: Attribution.
 */

namespace Drupal\leaflet\Control;
use Drupal\leaflet\Types\Control;

$plugin = array(
  'class' => '\\Drupal\\leaflet\\Control\\attribution',
);

/**
 * Class attribution.
 */
class attribution extends Control {

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
    $form['options']['prefix'] = array(
      '#type' => 'textfield',
      '#title' => t('Prefix'),
      '#description' => 'The HTML text shown before the attributions. Pass false to disable.',
      '#default_value' => $this->getOption('prefix'),
    );
    $form['options']['attribution'] = array(
      '#type' => 'textfield',
      '#title' => t('Attribution'),
      '#description' => 'The position of the control (one of the map corners).',
      '#default_value' => $this->getOption('attribution'),
    );
  }

}
