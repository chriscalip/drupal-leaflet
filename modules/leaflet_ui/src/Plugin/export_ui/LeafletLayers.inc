<?php
/**
 * @file
 * CTools Export UI plugin definition for layers.
 */

/**
 * CTools Export UI plugin definition.
 */
function leaflet_ui_LeafletLayers_ctools_export_ui() {
  return array(
    'schema' => 'leaflet_layers',
    'access' => 'administer leaflet',
    'menu' => array(
      'menu prefix' => 'admin/structure/leaflet',
      'menu item' => 'layers',
      'menu title' => 'Layers',
      'menu description' => 'Administer Leaflet layers presets.',
    ),

    'handler' => array(
      'class' => '\\Drupal\\leaflet_ui\\UI\\LeafletLayers',
      'file' => 'LeafletLayers.php',
    ),

    'use wizard' => TRUE,
    'form info' => array(
      'order' => array(
        'start' => t('Administrative settings'),
        'type' => t('Layer type'),
        'style' => t('Layer style'),
        'options' => t('Layer type options'),
      ),
      'forms' => array(
        'start' => array(
          'form id' => 'leaflet_layer_form_start',
        ),
        'type' => array(
          'form id' => 'leaflet_layer_form_type',
        ),
        'style' => array(
          'form id' => 'leaflet_layer_form_style',
        ),
        'options' => array(
          'form id' => 'leaflet_layer_form_options',
        ),
      ),
      'wrapper' => 'leaflet_objects_ui_form_wrapper',
    ),

    'export' => array(
      'admin_title' => 'name',
      'admin_description' => 'description',
    ),

    'title singular' => t('layer'),
    'title plural' => t('layers'),
    'title singular proper' => t('Leaflet preset'),
    'title plural proper' => t('Leaflet Layers presets'),

    'strings' => array(
      'confirmation' => array(
        'add' => array(
          'success' => t('saved.'),
        ),
        'delete' => array(
          'success' => t('was deleted.'),
        ),
      ),
    ),
  );
}

/**
 * Layer base config form handler.
 */
function leaflet_layer_form_start($form, &$form_state) {
  $class = new \Drupal\leaflet_ui\UI\LeafletLayers();
  $class->init($form_state['plugin']);
  $class->edit_form($form, $form_state);

  $options = array();
  foreach (\Drupal\leaflet\Leaflet::loadAllExportable('Map') as $machine_name => $map) {
    if (!is_object($map) || (property_exists($map, 'disabled') && ($map->disabled == 1 || $map->disabled == TRUE))) {
      continue;
    }
    $options[$machine_name] = $map->name;
  }

  $form['attachToMap'] = array(
    '#type' => 'select',
    '#title' => 'Add this to a map ?',
    '#empty_option' => t('- Do no attach -'),
    '#description' => 'Select the map to add this object to.',
    '#options' => $options,
    '#default_value' => isset($form_state['item']->attachToMap) ? $form_state['item']->attachToMap : '',
  );

  return $form;
}

/**
 * Layer base config form validation handler.
 */
function leaflet_layer_form_start_validate($form, &$form_state) {
  $class = new \Drupal\leaflet_ui\UI\LeafletLayers();
  $class->init($form_state['plugin']);
  $class->edit_form_validate($form, $form_state);
}

/**
 * Layer base config form submit handler.
 */
function leaflet_layer_form_start_submit($form, &$form_state) {
  $class = new \Drupal\leaflet_ui\UI\LeafletLayers();
  $class->init($form_state['plugin']);
  $class->edit_form_submit($form, $form_state);
}

/**
 * Layer type config form handler.
 */
function leaflet_layer_form_type($form, &$form_state) {
  $form['factory_service'] = array(
    '#type' => 'select',
    '#title' => t('Type'),
    '#empty_option' => t('- Select a @plugin type -', array('@plugin' => 'Layer')),
    '#default_value' => isset($form_state['item']->factory_service) ? $form_state['item']->factory_service : '',
    '#description' => t('Select the type of layer.'),
    '#options' => \Drupal\leaflet\Leaflet::getLeafletObjectsOptions('Layer'),
    '#required' => TRUE,
  );

  $form['options']['visible'] = array(
    '#type' => 'checkbox',
    '#title' => t('Visible ?'),
    '#default_value' => isset($form_state['item']->options['visible']) ? (bool) $form_state['item']->options['visible'] : TRUE,
    '#description' => t('TODO'),
  );

  return $form;
}

/**
 * Layer type config form submit handler.
 */
function leaflet_layer_form_type_submit($form, &$form_state) {
  $form_state['item']->factory_service = $form_state['values']['factory_service'];
  if (isset($form_state['values']['options'])) {
    $form_state['item']->options = array_merge((array) $form_state['item']->options, (array) $form_state['values']['options']);
  }
}

/**
 * Layer style config form handler.
 */
function leaflet_layer_form_style($form, &$form_state) {
  $form['options'] = array(
    '#tree' => TRUE,
  );

  $form['options']['style'] = array(
    '#type' => 'select',
    '#title' => t('Style'),
    '#empty_option' => t('- Select a Style -'),
    '#default_value' => isset($form_state['item']->options['style']) ? $form_state['item']->options['style'] : '',
    '#description' => t('Select the style.'),
    '#options' => \Drupal\leaflet\Leaflet::loadAllAsOptions('Style'),
  );

  return $form;
}

/**
 * Layer style config form submit handler.
 */
function leaflet_layer_form_style_submit($form, &$form_state) {
  if (isset($form_state['values']['options'])) {
    $form_state['item']->options = array_merge((array) $form_state['item']->options, (array) $form_state['values']['options']);
  }
}

/**
 * Layer options config form handler.
 */
function leaflet_layer_form_options($form, &$form_state) {
  if (($layer = \Drupal\leaflet\Leaflet::load('Layer', $form_state['item'])) == TRUE) {
    $layer->optionsForm($form, $form_state);
    $form['options']['#tree'] = TRUE;
  }

  return $form;
}

/**
 * Layer options config form validation handler.
 */
function leaflet_layer_form_options_validate($form, &$form_state) {
  if (($layer = \Drupal\leaflet\Leaflet::load('Layer', $form_state['item'])) == TRUE) {
    $layer->optionsFormValidate($form, $form_state);
  }
}

/**
 * Layer options config form submit handler.
 */
function leaflet_layer_form_options_submit($form, &$form_state) {
  if (isset($form_state['values']['options'])) {
    $form_state['item']->options = array_merge((array) $form_state['item']->options, (array) $form_state['values']['options']);
  }

  if (($layer = \Drupal\leaflet\Leaflet::load('Layer', $form_state['item'])) == TRUE) {
    if (!empty($form_state['item']->attachToMap) && ($map = \Drupal\leaflet\Leaflet::load('map', $form_state['item']->attachToMap)) == TRUE) {
      $map->getCollection()->append($layer);
      \Drupal\leaflet\Leaflet::save($map);
      unset($form_state['item']->attachToMap);
    }

    $layer->optionsFormSubmit($form, $form_state);
  }
}
