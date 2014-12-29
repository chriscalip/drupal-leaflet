<?php
/**
 * @file
 * CTools Export UI plugin definition for components.
 */

/**
 * CTools Export UI plugin definition.
 */
function leaflet_ui_LeafletComponents_ctools_export_ui() {
  return array(
    'schema' => 'leaflet_components',
    'access' => 'administer leaflet',
    'menu' => array(
      'menu prefix' => 'admin/structure/leaflet',
      'menu item' => 'components',
      'menu title' => 'Components',
      'menu description' => 'Administer leaflet components.',
    ),

    'handler' => array(
      'class' => '\\Drupal\\leaflet\\UI\\LeafletComponents',
      'file' => 'LeafletComponents.php',
    ),

    'export' => array(
      'admin_title' => 'name',
      'admin_description' => 'description',
    ),

    'use wizard' => TRUE,
    'form info' => array(
      'order' => array(
        'start' => t('Administrative settings'),
        'type' => t('Component type'),
        'options' => t('Component type options'),
      ),
      'forms' => array(
        'start' => array(
          'form id' => 'leaflet_component_form_start',
        ),
        'type' => array(
          'form id' => 'leaflet_component_form_type',
        ),
        'options' => array(
          'form id' => 'leaflet_component_form_options',
        ),
      ),
      'wrapper' => 'leaflet_objects_ui_form_wrapper',
    ),

    'title singular' => t('component'),
    'title plural' => t('components'),
    'title singular proper' => t('leaflet component preset'),
    'title plural proper' => t('leaflet component presets'),

    'strings' => array(
      'confirmation' => array(
        'add' => array(
          'success' => t('Component saved.'),
        ),
        'delete' => array(
          'success' => t('Component was deleted.'),
        ),
      ),
    ),
  );
}

/**
 * Component base config form handler.
 */
function leaflet_component_form_start($form, &$form_state) {
  $class = new Drupal\leaflet\UI\Components();
  $class->init($form_state['plugin']);
  $class->edit_form($form, $form_state);

  $maps = ctools_export_crud_load_all('leaflet_maps');
  $options = array('' => 'Dont attach');
  foreach ($maps as $machine_name => $data) {
    $map = leaflet_object_load('Map', $machine_name);
    if (property_exists($map, 'disabled') && ($map->disabled == 1 || $map->disabled == TRUE)) {
      continue;
    }
    $options[$machine_name] = $data->name;
  }

  $form['attachToMap'] = array(
    '#type' => 'select',
    '#title' => 'Add this to a map ?',
    '#description' => 'Select the map to add this object to.',
    '#options' => $options,
  );

  return $form;
}

/**
 * Component base config form validation handler.
 */
function leaflet_component_form_start_validate($form, &$form_state) {
  $class = new Drupal\leaflet\UI\Components();
  $class->init($form_state['plugin']);
  $class->edit_form_validate($form, $form_state);
}

/**
 * Component base config form submit handler.
 */
function leaflet_component_form_start_submit($form, &$form_state) {
  $class = new Drupal\leaflet\UI\Components();
  $class->init($form_state['plugin']);
  $class->edit_form_submit($form, $form_state);

  if ($form_state['values']['attachToMap']) {
    $map = ctools_export_crud_load('leaflet_maps', $form_state['values']['attachToMap']);
    $map->options['components'][] = $form_state['item']->machine_name;
    ctools_export_crud_save('leaflet_maps', $map);
  }
}

/**
 * Component type config form handler.
 */
function leaflet_component_form_type($form, &$form_state) {
  $form['factory_service'] = array(
    '#type' => 'select',
    '#title' => t('Component Type'),
    '#default_value' => isset($form_state['item']->factory_service) ? $form_state['item']->factory_service : '',
    '#description' => t('Select the type of component.'),
    '#options' => Leaflet::getLeafletObjectsOptions('Component'),
    '#required' => TRUE,
  );

  return $form;
}

/**
 * Component type config form submit handler.
 */
function leaflet_component_form_type_submit($form, &$form_state) {
  $form_state['item']->factory_service = $form_state['values']['factory_service'];
}

/**
 * Component options form handler.
 */
function leaflet_component_form_options($form, &$form_state) {
  $component = leaflet_object_load('Component', $form_state['item']);
  $component->optionsForm($form, $form_state);
  $form['options']['#tree'] = TRUE;

  return $form;
}

/**
 * Component options form validation handler.
 */
function leaflet_component_form_options_validate($form, &$form_state) {
  $component = leaflet_object_load('Component', $form_state['item']);
  $component->optionsFormValidate($form, $form_state);
}

/**
 * Component options form submit handler.
 */
function leaflet_component_form_options_submit($form, &$form_state) {
  if (isset($form_state['values']['options'])) {
    $form_state['item']->options = array_merge((array) $form_state['item']->options, (array) $form_state['values']['options']);
  }
  $component = leaflet_object_load('Component', $form_state['item']);
  $component->optionsFormSubmit($form, $form_state);
}
