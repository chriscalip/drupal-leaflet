<?php
/**
 * @file
 * Map: LMap.
 */

namespace Drupal\leaflet\Plugin\Map\LMap;
use Drupal\leaflet\Component\Annotation\LeafletPlugin;
use Drupal\leaflet\Config;
use Drupal\leaflet\Types\Map;

/**
 * Class LMap.
 *
 * @LeafletPlugin(
 *   id = "LMap"
 * )
 */
class LMap extends Map {

  /**
   * {@inheritdoc}
   */
  public function optionsForm(&$form, &$form_state) {
    $form['options']['ui'] = array(
      '#type' => 'fieldset',
      '#title' => t('Size of the map'),
      'width' => array(
        '#type' => 'textfield',
        '#title' => 'Width',
        '#default_value' => $this->getOption('width', 'auto'),
        '#parents' => array('options', 'width'),
      ),
      'height' => array(
        '#type' => 'textfield',
        '#title' => 'height',
        '#default_value' => $this->getOption('height', '300px'),
        '#parents' => array('options', 'height'),
      ),
    );

    $form['options']['view'] = array(
      '#type' => 'fieldset',
      '#title' => t('View: center'),
      '#tree' => TRUE,
    );

    if ($this->machine_name != Config::get('leaflet.edit_view_map')) {
      $map = \Drupal\leaflet\Leaflet::load('Map', Config::get('leaflet.edit_view_map'));
      if ($view = $this->getOption('view')) {
        $map->setOption('view', $view);
      }

      $form['options']['view']['map'] = array(
        '#type' => 'leaflet',
        '#description' => t('You can drag this map with your mouse.'),
        '#map' => $map,
      );
    }

    $form['options']['view']['center'] = array(
      '#tree' => TRUE,
      'lat' => array(
        '#type' => 'textfield',
        '#title' => 'Latitude',
        '#default_value' => $this->getOption(array('view', 'center', 'lat'), 0),
      ),
      'lon' => array(
        '#type' => 'textfield',
        '#title' => 'Longitude',
        '#default_value' => $this->getOption(array('view', 'center', 'lat'), 0),
      ),
    );
    $form['options']['view']['zoom'] = array(
      '#type' => 'textfield',
      '#title' => 'Zoom',
      '#default_value' => $this->getOption(array('view', 'zoom'), 0),
    );
    $form['options']['view']['minZoom'] = array(
      '#type' => 'textfield',
      '#title' => 'Min zoom',
      '#default_value' => $this->getOption(array('view', 'minZoom'), 0),
    );
    $form['options']['view']['maxZoom'] = array(
      '#type' => 'textfield',
      '#title' => 'Max zoom',
      '#default_value' => $this->getOption(array('view', 'maxZoom'), 0),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function attached() {
    $attached = parent::attached();
    // TODO: Leaflet settings form by default to debug mode.
    $variant = NULL;
    if (Config::get('leaflet.debug') == TRUE) {
      $variant = 'debug';
    };
    $attached['libraries_load']['leaflet'] = array('leaflet', $variant);
    return $attached;
  }
}
