<?php
/**
 * @file
 * Control: InlineJS.
 */

namespace Drupal\leaflet\Plugin\Control\InlineJS;
use Drupal\leaflet\Component\Annotation\LeafletPlugin;
use Drupal\leaflet\Types\Control;

/**
 * Class InlineJS
 *
 * @LeafletPlugin(
 *   id = "InlineJS"
 * )
 */
class InlineJS extends Control {

  /**
   * {@inheritdoc}
   */
  public function optionsForm(&$form, &$form_state) {
    $attached = array();

    if (module_exists('ace_editor')) {
      $attached = array(
        'library' => array(
          array('ace_editor', 'ace-editor'),
        ),
        'js' => array(
          drupal_get_path('module', 'leaflet') . '/js/leaflet.editor.js',
        ),
      );
    }
    else {
      drupal_set_message(t('To get syntax highlighting, you should install the module <a href="@url1">ace_editor</a> and its <a href="@url2">library</a>.', array('@url1' => 'http://drupal.org/project/ace_editor', '@url2' => 'http://ace.c9.io/')), 'warning');
    }

    $form['options']['javascript'] = array(
      '#type' => 'textarea',
      '#title' => t('Javascript'),
      '#description' => t('Javascript to evaluate. The available variable is: <em>data</em>. You must create the leaflet variable <em>control</em>.'),
      '#rows' => 15,
      '#default_value' => $this->getOption('javascript'),
      '#attributes' => array(
        'data-editor' => 'javascript',
      ),
      '#attached' => $attached,
    );
  }

}
