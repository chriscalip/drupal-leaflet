<?php
/**
 * @file
 * Control: Attribution.
 */

namespace Drupal\leaflet\Plugin\Control\Attribution;
use Drupal\leaflet\Component\Annotation\LeafletPlugin;
use Drupal\leaflet\Types\Control;


/**
 * Class Attribution.
 *
 * @LeafletPlugin(
 *  id = "Attribution",
 *  description = "Provides an information button with layers information."
 * )
 */
class Attribution extends Control {
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
