<?php
/**
 * @file
 * Class leaflet_layers_ui.
 */

namespace Drupal\leaflet\UI;

/**
 * Class leaflet_layers_ui.
 */
class LeafletLayers extends \LeafletObjects {

  /**
   * {@inheritdoc}
   */
  public function hook_menu(&$items) {
    parent::hook_menu($items);
    $items['admin/structure/leaflet/layers']['type'] = MENU_LOCAL_TASK;
    $items['admin/structure/leaflet/layers']['weight'] = -5;
  }

}
