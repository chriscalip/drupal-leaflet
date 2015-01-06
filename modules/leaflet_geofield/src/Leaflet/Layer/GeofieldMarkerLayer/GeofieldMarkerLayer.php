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
    $form['options']['popupSettings'] = array(
      '#type' => 'fieldset',
      '#title' => t('Popup Settings'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    );
    $form['options']['popupSettings']['show'] = array(
      '#title' => t('Popup'),
      '#description' => t('Show a popup when locations are clicked.'),
      '#type' => 'checkbox',
      '#default_value' => $this->getOption(array('popupSettings', 'show'), 1),
    );
    $form['options']['popupSettings']['text'] = array(
      '#title' => t('Popup text'),
      '#description' => t('Enter the text for the popup. Tokens are supported.'),
      '#type' => 'textfield',
      '#default_value' => $this->getOption(array('popupSettings', 'text'), ''),
    );
    if (module_exists('token')) {
      $entities = entity_get_info();
      $visible_options = array();
      $entity_options = array();
      foreach ($entities as $key => $entity) {
        $entity_options[$key] = $entity['label'];
        $visible_options[] = array('value' => $key);
      }
      $form['options']['popupSettings']['token_entity'] = array(
        '#title' => t('Select an Entity Type that tokens will be based.'),
        '#type' => 'select',
        '#options' => array('_none' => 'None') + $entity_options,
        '#default_value' => '_none',
      );
      $form['options']['popupSettings']['token_help'] = array(
        '#type' => 'container',
        '#theme' => 'token_tree',
        // @TODO how change the value of array('node') to whatever is
        // selected at token_entity e.g if selected is comment
        // value of token_types is array('comment')
        '#token_types' => array_keys($entity_options),
        // token_help form item only shows if selected option of token_entity
        // is not _none
        '#states' => array(
          'visible' => array(
            ':input[name="options[popupSettings][token_entity]"]' => $visible_options
          ),
        ),
      );
    }
    $form['options']['markerSettings'] = array(
      '#type' => 'fieldset',
      '#title' => t('Marker Settings'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#description' => t('Based on settings based on http://leafletjs.com/reference.html#marker'),
    );
    // the following options are based on http://leafletjs.com/reference.html#marker
    $form['options']['markerSettings']['clickable'] = array(
      '#type' => 'checkbox',
      '#title' => t('clickable'),
      '#default_value' => $this->getOption(array('markerSettings', 'clickable'), 1),
      '#description' => t("   If false, the marker will not emit mouse events and will act as a part of the underlying map."),
    );
    $form['options']['markerSettings']['draggable'] = array(
      '#type' => 'checkbox',
      '#title' => t('draggable'),
      '#default_value' => $this->getOption(array('markerSettings', 'draggable'), 0),
      '#description' => t("Whether the marker is draggable with mouse/touch or not."),
    );
    $form['options']['markerSettings']['keyboard'] = array(
      '#type' => 'checkbox',
      '#title' => t('keyboard'),
      '#default_value' => $this->getOption(array('markerSettings', 'keyboard'), 1),
      '#description' => t("Whether the marker can be tabbed to with a keyboard and clicked by pressing enter."),
    );
    $form['options']['markerSettings']['title'] = array(
      '#title' => t('title'),
      '#description' => t('Text for the browser tooltip that appear on marker hover (no tooltip by default).'),
      '#type' => 'textfield',
      '#default_value' => $this->getOption(array('markerSettings', 'title'), ''),
    );
    $form['options']['markerSettings']['alt'] = array(
      '#title' => t('alt'),
      '#description' => t('Text for the browser tooltip that appear on marker hover (no tooltip by default).'),
      '#type' => 'textfield',
      '#default_value' => $this->getOption(array('markerSettings', 'alt'), ''),
    );
    $form['options']['markerSettings']['zIndexOffset'] = array(
      '#title' => t('zIndexOffset'),
      '#description' => t('By default, marker images zIndex is set automatically based on its latitude. Use this option if you want to put the marker on top of all others (or below), specifying a high value like 1000 (or high negative value, respectively).'),
      '#type' => 'textfield',
      '#default_value' => $this->getOption(array('markerSettings', 'zIndexOffset'), 0),
    );
    $form['options']['markerSettings']['opacity'] = array(
      '#title' => t('opacity'),
      '#description' => t('The opacity of the marker.'),
      '#type' => 'textfield',
      '#default_value' => $this->getOption(array('markerSettings', 'opacity'), 1),
    );
    $form['options']['markerSettings']['riseOnHover'] = array(
      '#type' => 'checkbox',
      '#title' => t('riseOnHover'),
      '#default_value' => $this->getOption(array('markerSettings', 'riseOnHover'), 0),
      '#description' => t("If true, the marker will get on top of others when you hover the mouse over it."),
    );
    $form['options']['markerSettings']['riseOffset'] = array(
      '#title' => t('riseOffset'),
      '#description' => t('The z-index offset used for the riseOnHover feature.'),
      '#type' => 'textfield',
      '#default_value' => $this->getOption(array('markerSettings', 'riseOffset'), 250),
    );
  }

}
