<?php
/**
 * @file
 * Class Controls.
 */

namespace Drupal\leaflet\UI;

/**
 * Class Controls.
 * @package Drupal\leaflet\UI
 */
class Controls extends \LeafletObjectsUI {

  /**
   * {@inheritdoc}
   */
  public function hook_menu(&$items) {
    parent::hook_menu($items);
    $items['admin/structure/leaflet/controls']['type'] = MENU_LOCAL_TASK;
    $items['admin/structure/leaflet/controls']['weight'] = 1;
  }

}
