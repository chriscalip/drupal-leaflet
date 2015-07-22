<?php

/**
 * Class for tab content of type "leaflet_map" - this is for rendering a map as tab
 * content.
 */
class LeafletMap extends QuickContent {

  /**
   * @inheritdoc
   */
  public static function getType() {
    return 'Leaflet-map';
  }

  /**
   * @inheritdoc
   */
  public function optionsForm($delta, $qt) {
    $tab = $this->settings;
    $form = array();

    $form['Leaflet-map']['map'] = array(
      '#type' => 'select',
      '#title' => t('Leaflet map'),
      '#options' => \Drupal\leaflet\Leaflet::loadAllAsOptions('Map'),
      "#empty_option" => t('- Select a map -'),
      '#default_value' => isset($tab['leaflet_map']) ? $tab['leaflet_map'] : '',
    );
    return $form;
  }

  /**
   * @inheritdoc
   */
  public function render($hide_empty = FALSE, $args = array()) {
    if ($this->rendered_content) {
      return $this->rendered_content;
    }
    $item = $this->settings;

    $output = array(
      '#type' => 'leaflet',
      '#map' => $item['map']
    );

    $this->rendered_content = $output;
    return $output;
  }

  /**
   * @inheritdoc
   */
  public function getAjaxKeys() {
    return array('Leaflet-map');
  }
}
