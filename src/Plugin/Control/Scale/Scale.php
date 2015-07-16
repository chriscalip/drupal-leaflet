<?php
/**
 * @file
 * Control: Scale.
 */

namespace Drupal\leaflet\Plugin\Control\Scale;
use Drupal\leaflet\Component\Annotation\LeafletPlugin;
use Drupal\leaflet\Types\Control;

/**
 * Class Scale.
 *
 * @LeafletPlugin(
 *   id = "Scale",
 *   description = "A Simple scale control that shows the scale of the current center of screen in metric and imperial system."
 * )
 */
class Scale extends Control {
  /**
   * {@inheritdoc}
   */
  public function optionsForm(&$form, &$form_state) {
    $form['options']['position'] = array(
      '#type' => 'select',
      '#title' => t('Position'),
      '#description' => 'The position of the control (one of the map corners).',
      '#default_value' => $this->getOption('position', 'bottomleft'),
      '#options' => array(
        'topleft' => 'Top left',
        'topright' => 'Top right',
        'bottomright' => 'Bottom right',
        'bottomleft' => 'Bottom left',
      )
    );
    $form['options']['maxWidth'] = array(
      '#type' => 'textfield',
      '#title' => t('Max width'),
      '#description' => 'Maximum width of the control in pixels. The width is set dynamically to show round values (e.g. 100, 200, 500).',
      '#default_value' => $this->getOption('maxWidth'),
    );
    $form['options']['metric'] = array(
      '#type' => 'checkbox',
      '#title' => t('Metric'),
      '#description' => 'Whether to show the metric scale line (m/km).',
      '#default_value' => $this->getOption('metric'),
    );
    $form['options']['imperial'] = array(
      '#type' => 'checkbox',
      '#title' => t('Imperial'),
      '#description' => 'Whether to show the imperial scale line (mi/ft).',
      '#default_value' => $this->getOption('imperial'),
    );
    $form['options']['updateWhenIdle'] = array(
      '#type' => 'checkbox',
      '#title' => t('Update when idle'),
      '#description' => 'If true, the control is updated on moveend, otherwise it\'s always up-to-date (updated on move).',
      '#default_value' => $this->getOption('updateWhenIdle'),
    );
  }
}
