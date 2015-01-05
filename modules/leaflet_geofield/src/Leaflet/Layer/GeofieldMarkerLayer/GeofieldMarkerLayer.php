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
    $form['options']['popupShow'] = array(
      '#title' => t('Popup'),
      '#description' => t('Show a popup when locations are clicked.'),
      '#type' => 'checkbox',
      '#default_value' => $this->getOption('popupShow'),
    );
    $form['options']['popupText'] = array(
      '#title' => t('Popup text'),
      '#description' => t('Enter the text for the popup. Tokens are supported.'),
      '#type' => 'textfield',
      '#default_value' => $this->getOption('popupText'),
    );
    $form['options']['markerSettings'] = array(
      '#type' => 'fieldset',
      '#title' => t('Marker Settings'),
      '#description' => t('Based on settings based on http://leafletjs.com/reference.html#marker')
    );
    // the following options are based on http://leafletjs.com/reference.html#marker
    $form['options']['clickable'] = array(
      '#type' => 'checkbox',
      '#title' => t('clickable'),
      '#default_value' => $this->getOption('clickable', 1),
      '#description' => t("   If false, the marker will not emit mouse events and will act as a part of the underlying map."),
    );
    $form['options']['draggable'] = array(
      '#type' => 'checkbox',
      '#title' => t('draggable'),
      '#default_value' => $this->getOption('draggable', 0),
      '#description' => t("Whether the marker is draggable with mouse/touch or not."),
    );
    $form['options']['keyboard'] = array(
      '#type' => 'checkbox',
      '#title' => t('keyboard'),
      '#default_value' => $this->getOption('keyboard', 1),
      '#description' => t("Whether the marker can be tabbed to with a keyboard and clicked by pressing enter."),
    );
    $form['options']['title'] = array(
      '#title' => t('title'),
      '#description' => t('Text for the browser tooltip that appear on marker hover (no tooltip by default).'),
      '#type' => 'textfield',
      '#default_value' => $this->getOption('title'),
    );
    $form['options']['alt'] = array(
      '#title' => t('alt'),
      '#description' => t('Text for the browser tooltip that appear on marker hover (no tooltip by default).'),
      '#type' => 'textfield',
      '#default_value' => $this->getOption('alt'),
    );
    $form['options']['zIndexOffset'] = array(
      '#title' => t('zIndexOffset'),
      '#description' => t('By default, marker images zIndex is set automatically based on its latitude. Use this option if you want to put the marker on top of all others (or below), specifying a high value like 1000 (or high negative value, respectively).'),
      '#type' => 'textfield',
      '#default_value' => $this->getOption('zIndexOffset', 0),
    );
    $form['options']['opacity'] = array(
      '#title' => t('opacity'),
      '#description' => t('The opacity of the marker.'),
      '#type' => 'textfield',
      '#default_value' => $this->getOption('opacity', 1),
    );
    $form['options']['riseOnHover'] = array(
      '#type' => 'checkbox',
      '#title' => t('riseOnHover'),
      '#default_value' => $this->getOption('riseOnHover', 0),
      '#description' => t("If true, the marker will get on top of others when you hover the mouse over it."),
    );
    $form['options']['riseOffset'] = array(
      '#title' => t('riseOffset'),
      '#description' => t('The z-index offset used for the riseOnHover feature.'),
      '#type' => 'textfield',
      '#default_value' => $this->getOption('riseOffset', 250),
    );
  }

}
