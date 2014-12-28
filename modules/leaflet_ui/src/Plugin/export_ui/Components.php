<?php
/**
 * @file
 * Class leaflet_components_ui.
 */

namespace Drupal\leaflet\UI;

/**
 * Class leaflet_components_ui.
 */
class Components extends \LeafletObjectsUI {

  /**
   * {@inheritdoc}
   */
  public function hook_menu(&$items) {
    parent::hook_menu($items);
    $items['admin/structure/leaflet/components']['type'] = MENU_LOCAL_TASK;
    $items['admin/structure/leaflet/components']['weight'] = 3;
  }

}
