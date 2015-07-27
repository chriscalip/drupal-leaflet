<?php
/**
 * @file
 * Layer: Field.
 */

namespace Drupal\leaflet_field\Plugin\Layer\Field;

use Drupal\leaflet\Component\Annotation\LeafletPlugin;
use Drupal\leaflet\Types\Layer;


/**
 * Class Field.
 *
 * @LeafletPlugin(
 *  id = "Field",
 *  description = "Provides a layer where data are geocoded from provided options."
 * )
 *
 */
class Field extends Layer {
  /**
   * @inheritDoc
   */
  public function dependencies() {
    return array('geocoder');
  }

  /**
   * @inheritDoc
   */
  public function optionsForm(&$form, &$form_state) {
    $geocoder_handlers = array();
    foreach (geocoder_handler_info() as $name => $handler) {
      $geocoder_handlers[$name] = $handler['title'];
    }

    $form['options']['geocoder_handler'] = array(
      '#type' => 'select',
      '#title' => t('Geocoder handler'),
      '#options' => $geocoder_handlers,
      '#required' => TRUE,
      '#default_value' => $this->getOption('geocoder_handler', 'google'),
    );

    $form['options']['geocoder_cache'] = array(
      '#type' => 'select',
      '#title' => t('Geocoder cache type'),
      '#description' => t('Type of geocoder cache to use'),
      '#options' => array(
        0 => t('No caching'),
        1 => t('Static-cache but no persistent-cache'),
        2 => t('Both static-cache and persistent-cache'),
      ),
      '#default_value' => $this->getOption('geocoder_cache', 0),
    );

    $fields = $this->getOption('fields', array(array()));
    if (!empty($fields[0])) {
      $fields[] = array();
    }

    foreach ($fields as $index => $field) {
      $form['options']['fields'][$index] = array(
        '#type' => 'fieldset',
        '#title' => ($field == end($fields)) ? t('Add a new feature') : 'Feature ' . $index,
        '#collapsible' => TRUE,
        '#collapsed' => ($field == end($fields)) ? TRUE : FALSE,
        'title' => array(
          '#title' => 'Title',
          '#type' => 'textfield',
          '#default_value' => isset($fields[$index]['title']) ? $fields[$index]['title'] : '',
        ),
        'description' => array(
          '#title' => 'Description',
          '#type' => 'textarea',
          '#default_value' => isset($fields[$index]['description']) ? $fields[$index]['description'] : '',
        ),
        'address' => array(
          '#title' => 'Address',
          '#type' => 'textarea',
          '#default_value' => isset($fields[$index]['address']) ? $fields[$index]['address'] : '',
        ),
        'wkt' => array(
          '#type' => 'textfield',
          '#title' => 'WKT',
          '#disabled' => TRUE,
          '#default_value' => isset($fields[$index]['wkt']) ? $fields[$index]['wkt'] : '',
        ),
      );
    }
  }

  /**
   * @inheritDoc
   */
  public function optionsFormSubmit($form, &$form_state) {
    $fields = $form_state['values']['options']['fields'];

    $geocoder_handler = $form_state['values']['options']['geocoder_handler'];
    $geocoder_cache = $form_state['values']['options']['geocoder_cache'];

    // This is for optimizing the source rendering in JS.
    // It converts all the address fields of the source to WKT.
    foreach ($fields as $index => &$field) {
      if (isset($field['address']) && !empty($field['address'])) {
        $geocoder = geocoder($geocoder_handler, $field['address'], array(), $geocoder_cache);
        if (is_object($geocoder)) {
          $field['wkt'] = $geocoder->out('wkt');
        }
        else {
          unset($field['wkt']);
        }
      }
      else {
        unset($fields[$index]);
      }
    }

    $form_state['values']['options']['fields'] = $fields;
    parent::optionsFormSubmit($form, $form_state);
  }

  /**
   * @inheritDoc
   */
  public function getJS() {
    $js = parent::getJS();
    $features = $this->getGeojsonFeatures();

    if (!empty($features)) {
      $js['opt']['geojson_data'] = array(
        'type' => 'FeatureCollection',
        'features' => $features,
      );
    }

    unset($js['opt']['fields']);
    return $js;
  }

  /**
   * Compute the GeoJSON features array.
   *
   * @return array
   *   The geojson array.
   */
  protected function getGeojsonFeatures() {
    $features = array();
    foreach ($this->getOption('fields', array()) as $field) {
      $feature = array(
        'type' => 'Feature',
      );
      if (isset($field['title']) && !empty($field['title'])) {
        $feature['properties']['name'] = $field['title'];
      }
      if (isset($field['description']) && !empty($field['description'])) {
        $feature['properties']['description'] = $field['description'];
      }

      $json = FALSE;
      if (isset($field['wkt']) && !empty($field['wkt'])) {
        geophp_load();
        $json = \geoPHP::load($field['wkt'], 'wkt')->out('json');
      }
      else {
        if (isset($field['address']) && !empty($field['address'])) {
          $geocoder = geocoder($this->getOption('geocoder_handler', 'google'), $field['address'], array(), $this->getOption('geocoder_cache', 2));
          if (is_object($geocoder)) {
            $json = $geocoder->out('json');
          }
        }
      }

      if ($json) {
        $feature['geometry'] = json_decode($json, TRUE);
        $features[] = $feature;
      }
    }

    return $features;
  }
}
