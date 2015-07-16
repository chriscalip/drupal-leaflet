<?php
/**
 * @file
 * This file holds the functions for the main leaflet Admin settings.
 *
 * @ingroup leaflet
 */

/**
 * Menu callback; displays the leaflet module settings page.
 *
 * @see system_settings_form()
 */
function leaflet_ui_admin_settings($form, &$form_state) {
  $options = array();
  foreach (\Drupal\leaflet\Leaflet::loadAllExportable('Map') as $machine_name => $data) {
    if (!is_object($data) || (property_exists($data, 'disabled') && ($data->disabled == 1 || $data->disabled == TRUE))) {
      continue;
    }
    $options[$machine_name] = $data->name;
  }

  $map = \Drupal\leaflet\Config::get('leaflet.default_ui_map');
  if (isset($form_state['values']['leaflet_default_map'])) {
    $map = $form_state['values']['leaflet_default_map'];
  }

  // @todo: Is this setting really needed ?
  $form['leaflet_default_map'] = array(
    '#type' => 'select',
    '#title' => t('Default UI map'),
    '#options' => $options,
    '#default_value' => $map,
    '#ajax' => array(
      'callback' => '_leaflet_ajax_reload_default_map',
      'method' => 'replace',
      'wrapper' => 'default_map_ajax',
      'effect' => 'fade',
    ),
  );

  $form['default_ui_map'] = array(
    '#prefix' => '<div id="default_map_ajax">',
    '#suffix' => '</div>',
    '#type' => 'leaflet',
    '#map' => $map,
  );

  $form['debug'] = array(
    '#type' => 'checkbox',
    '#title' => t('Debug mode'),
    '#description' => t("Enable this option when creating objects. Hit F12 in Chrome or Firefox to see the debugging informations. Doesn't fully work in IE yet."),
    '#default_value' => \Drupal\leaflet\Config::get('leaflet.debug'),
  );

  // This is for preventing 'system/ajax' as destination when using AJAX maps.
  // See: http://drupal.stackexchange.com/questions/86958/get-current-url-arguments-inside-ajax-callback
  $form['current_path'] = array('#type' => 'hidden', '#value' => current_path());

  $form['plugin-info'] = array(
    '#type' => 'fieldset',
    '#title' => 'Plugin information',
    '#collapsible' => TRUE,
    '#collapsed' => TRUE
  );

  foreach(\Drupal\leaflet\Leaflet::getPluginTypes() as $type) {
    $rows = array();

    $form['plugin-info'][$type . '-group'] = array(
      '#type' => 'fieldset',
      '#title' => $type,
      '#collapsed' => TRUE,
      '#collapsible' => TRUE
    );

    foreach(\Drupal::service('leaflet.' . $type)->getDefinitions() as $service => $definition) {
      $definition += array('arguments' => array('NULL'));

      /* @var \Drupal\leaflet\Types\Object $instance */
      $instance = \Drupal::service('leaflet.' . $type)->createInstance($definition['id']);
      $attached = $instance->attached();

      $sizes = array('js' => 0, 'css' => 0);
      foreach(array_keys($sizes) as $size_type) {
        foreach($attached[$size_type] as $index => $data) {
          $sizes[$size_type] += filesize($data['data']);
        }
      }

      if (count($attached['js']) != 0) {
        $sizes['js'] .= ' bytes.<br/>(' . count($attached['js']) . ' file(s))';
      }
      if (count($attached['css']) != 0) {
        $sizes['css'] .= 'bytes.<br/>(' . count($attached['css']) . ' file(s))';
      }

      $class = array(
        'Class: ' . $definition['class'],
        'Classfile: ' . $instance->getClassPath(),
      );

      $rows[] = array(
        $instance->getProvider(),
        $instance->getBaseId(),
        implode('<br/>', $class),
        implode('|', $definition['arguments']),
        $sizes['js'],
        $sizes['css'],
      );
    }

    $header = array(
      'Module provider',
      'ID',
      'Class',
      'Arguments',
      'JS size',
      'CSS size'
    );
    $table = theme('table', array('header' => $header, 'rows' => $rows, 'empty' => t('No plugin available.')));

    $form['plugin-info'][$type . '-group']['table'] = array(
      '#markup' => $table
    );
  }

  $library = \Drupal\leaflet\Leaflet::getLibrary();
  if ($library['installed'] == TRUE) {
    $rows = array();
    $rows1 = array('name', 'vendor url', 'download url', 'library path', 'version');
    $header = array('Property', 'Value');
    foreach($rows1 as $row) {
      $rows[] = array(
        $row,
        $library[$row]
      );
    }
    $output = theme('table', array('header' => $header, 'rows' => $rows));
  } else {
    $output = t('Leaflet library was not found. To install it, <a href="@url">download it manually</a> or use <em>drush dl-leaflet</em>. The library directory must be named <em>leaflet3</em> to be recognized by this module.', array('@url' => $library['download url']));
  }

  $form['library-fieldset'] = array(
    '#type' => 'fieldset',
    '#title' => 'Library information',
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    'version' => array(
      '#markup' => $output
    )
  );

  $form['buttons'] = array(
    'submit' => array(
      '#type' => 'submit',
      '#value' => 'Save configuration',
    ),
  );

  return $form;
}

/**
 * @TODO: Document.
 */
function _leaflet_ajax_reload_default_map($form, &$form_state) {
  // This is for preventing 'system/ajax' as destination when using AJAX maps.
  // See: http://drupal.stackexchange.com/questions/86958/get-current-url-arguments-inside-ajax-callback
  $_SESSION['current_path'] = $form_state['input']['current_path'];
  return $form['default_ui_map'];
}

function leaflet_ui_admin_settings_submit($form, &$form_state) {
  $values = $form_state['values'];
  \Drupal\leaflet\Config::set('leaflet.debug', $values['debug']);
  drupal_set_message(t('The configuration options have been saved.'));
}
