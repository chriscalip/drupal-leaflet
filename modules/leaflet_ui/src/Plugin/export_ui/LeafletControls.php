<?php
/**
 * @file
 * Class Controls.
 */

namespace Drupal\leaflet_ui\UI;

/**
 * Class Controls.
 * @package Drupal\leaflet_ui\UI
 */
class LeafletControls extends \LeafletObjects {

  /**
   * {@inheritdoc}
   */
  public function hook_menu(&$items) {
    parent::hook_menu($items);
    $items['admin/structure/leaflet/controls']['type'] = MENU_LOCAL_TASK;
    $items['admin/structure/leaflet/controls']['weight'] = 1;
  }

}
