<?php
/**
 * @file
 * Control: Zoom.
 */

namespace Drupal\leaflet\Plugin\Control\Zoom;
use Drupal\leaflet\Component\Annotation\LeafletPlugin;
use Drupal\leaflet\Types\Control;

/**
 * Class Zoom.
 *
 * @LeafletPlugin(
 *   id = "Zoom",
 *   description = "A basic zoom control with two buttons."
 * )
 */
class Zoom extends Control {
  /**
   * {@inheritdoc}
   */
  public function optionsForm(&$form, &$form_state) {
    $form['options']['position'] = array(
      '#type' => 'select',
      '#title' => t('Position'),
      '#description' => 'The position of the control (one of the map corners).',
      '#default_value' => $this->getOption('position', 'topleft'),
      '#options' => array(
        'topleft' => 'Top left',
        'topright' => 'Top right',
        'bottomright' => 'Bottom right',
        'bottomleft' => 'Bottom left',
      )
    );
    $form['options']['zoomInText'] = array(
      '#type' => 'textfield',
      '#title' => t('Zoom in text'),
      '#description' => 'The text set on the zoom in button.',
      '#default_value' => $this->getOption('zoomInText', '+'),
    );
    $form['options']['zoomOutText'] = array(
      '#type' => 'textfield',
      '#title' => t('Zoom out text'),
      '#description' => 'The text set on the zoom out button.',
      '#default_value' => $this->getOption('zoomOutText', '-'),
    );
    $form['options']['zoomInTitle'] = array(
      '#type' => 'textfield',
      '#title' => t('Zoom in title'),
      '#description' => 'The title set on the zoom in button.',
      '#default_value' => $this->getOption('zoomInTitle', 'Zoom in'),
    );
    $form['options']['zoomOutTitle'] = array(
      '#type' => 'textfield',
      '#title' => t('Zoom out title'),
      '#description' => 'The title set on the zoom out button.',
      '#default_value' => $this->getOption('zoomOutTitle', 'Zoom out'),
    );
  }
}
