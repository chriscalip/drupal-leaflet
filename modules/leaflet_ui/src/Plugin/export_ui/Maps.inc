<?php
/**
 * @file
 * CTools Export UI plugin definition for maps.
 */

/**
 * CTools Export UI plugin definition.
 */
function leaflet_ui_Maps_ctools_export_ui() {
  return array(
    'schema' => 'leaflet_maps',
    'access' => 'administer leaflet',
    'menu' => array(
      'menu prefix' => 'admin/structure/leaflet',
      'menu item' => 'maps',
      'menu title' => 'Maps',
      'menu description' => 'Administer Leaflet maps presets.',
    ),

    'handler' => array(
      'class' => '\\Drupal\\leaflet\\UI\\Maps',
      'file' => 'Maps.php',
    ),

    'use wizard' => TRUE,
    'form info' => array(
      'order' => array(
        'start' => t('Administrative settings'),
        'settings' => t('Map settings'),
        'layers' => t('Layers'),
        'preview' => t('Preview'),
      ),
      'forms' => array(
        'start' => array(
          'form id' => 'leaflet_map_form_start',
        ),
        'settings' => array(
          'form id' => 'leaflet_map_form_settings',
        ),
        'layers' => array(
          'form id' => 'leaflet_map_form_layers',
        ),
        'preview' => array(
          'form id' => 'leaflet_map_form_preview',
        ),
      ),
      'wrapper' => 'leaflet_objects_ui_form_wrapper',
    ),

    'export' => array(
      'admin_title' => 'name',
      'admin_description' => 'description',
    ),

    'title singular' => t('map'),
    'title plural' => t('maps'),
    'title singular proper' => t('Leaflet map preset'),
    'title plural proper' => t('Leaflet maps presets'),

    'strings' => array(
      'confirmation' => array(
        'add' => array(
          'success' => t('Map saved.'),
        ),
        'delete' => array(
          'success' => t('Map was deleted.'),
        ),
      ),
    ),
  );
}

/**
 * Map base config form handler.
 */
function leaflet_map_form_start($form, &$form_state) {
  $class = new Drupal\leaflet\UI\Maps();
  $class->init($form_state['plugin']);
  $class->edit_form($form, $form_state);
  return $form;
}

/**
 * Map base config form validation handler.
 */
function leaflet_map_form_start_validate($form, &$form_state) {
  $class = new Drupal\leaflet\UI\Maps();
  $class->init($form_state['plugin']);
  $class->edit_form_validate($form, $form_state);
}

/**
 * Map base config form submit handler.
 */
function leaflet_map_form_start_submit($form, &$form_state) {
  $class = new Drupal\leaflet\UI\Maps();
  $class->init($form_state['plugin']);
  $class->edit_form_submit($form, $form_state);
}

/**
 * Map settings config form handler.
 */
function leaflet_map_form_settings($form, &$form_state) {
  if (!isset($form_state['item']->factory_service)) {
    $form_state['item']->factory_service = 'leaflet.map.internal.Map';
  }
  $map = leaflet_object_load('Map', $form_state['item']);
  $map->optionsForm($form, $form_state);
  $form['options']['#tree'] = TRUE;

  return $form;
}

/**
 * Map settings config form validation handler.
 */
function leaflet_map_form_settings_validate($form, &$form_state) {
  $item = $form_state['item'];
  if (empty($item->factory_service)) {
    $item->factory_service = 'leaflet.map.internal.Map';
  }
  if (isset($form_state['values']['options'])) {
    $form_state['item']->options = array_merge((array) $form_state['item']->options, (array) $form_state['values']['options']);
  }

  $map = leaflet_object_load('Map', $item);
  $map->optionsFormValidate($form, $form_state);
}

/**
 * Map settings config form submit handler.
 */
function leaflet_map_form_settings_submit($form, &$form_state) {
  // todo: fix.
  if (empty($form_state['item']->factory_service)) {
    $form_state['item']->factory_service = 'leaflet.map.internal.Map';
  }
  if (isset($form_state['values']['options'])) {
    $form_state['item']->options = array_merge((array) $form_state['item']->options, (array) $form_state['values']['options']);
  }

  $map = leaflet_object_load('Map', $form_state['item']);
  $map->optionsFormSubmit($form, $form_state);
}

/**
 * Map layers config form handler.
 */
function leaflet_map_form_layers($form, &$form_state) {
  $all_layers = ctools_export_crud_load_all('leaflet_layers');
  $map = leaflet_object_load('Map', $form_state['item']);

  $layers = array();
  foreach ($map->getLayers() as $layer) {
    if (isset($all_layers[$layer->machine_name])) {
      $layers[$layer->machine_name] = $all_layers[$layer->machine_name];
      $layers[$layer->machine_name]->enable = 1;
      unset($all_layers[$layer->machine_name]);
    }
  }

  $layers = array_merge($layers, $all_layers);

  $data = array();

  $i = 0;
  foreach ($layers as $machine_name => $layer) {
    $data[$machine_name] = array(
      'name' => $layer->name,
      'machine_name' => $layer->machine_name,
      'description' => $layer->description,
      'factory_service' => $layer->factory_service,
      'weight' => $i++,
      'enable' => isset($layer->enable) ? $layer->enable : 0,
      'default' => 1,
    );
  }

  $rows = array();
  $row_elements = array();
  foreach ($data as $id => $entry) {
    $rows[$id] = array(
      'data' => array(
        array('class', array('entry-cross')),
        array(
          'data' => array(
            '#type' => 'weight',
            '#title' => t('Weight'),
            '#title_display' => 'invisible',
            '#default_value' => $entry['weight'],
            '#parents' => array('layers', $id, 'weight'),
            '#attributes' => array(
              'class' => array('entry-order-weight'),
            ),
          )),
        array(
          'data' => array(
            '#type' => 'checkbox',
            '#title' => t('Enable'),
            '#title_display' => 'invisible',
            '#default_value' => $entry['enable'],
            '#parents' => array('layers', $id, 'enable'),
          )),
        check_plain($entry['name']),
        check_plain($entry['machine_name']),
        check_plain($entry['description']),
        check_plain($entry['factory_service']),
        l(t('Edit'), 'admin/structure/leaflet/layers/list/' . $entry['machine_name'] . '/edit/options',
          array(
            'query' => array(
              'destination' => current_path(),
            ),
          )
        ),
      ),
      'class' => array('draggable'),
    );
    // Build rows of the form elements in the table.
    $row_elements[$id] = array(
      'weight' => &$rows[$id]['data'][1]['data'],
      'enabled' => &$rows[$id]['data'][2]['data'],
    );
  }

  $form['layers'] = array(
    '#type' => 'fieldset',
    '#title' => 'Layers',
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
  );

  // Add the table to the form.
  $form['layers']['table_layers'] = array(
    '#theme' => 'table',
    // The row form elements need to be processed and build,
    // therefore pass them as element children.
    'elements' => $row_elements,
    '#header' => array(
      // We need two empty columns for the weigth field and the cross.
      array('data' => NULL, 'colspan' => 2),
      t('Enabled'),
      t('Name'),
      t('Machine name'),
      t('Description'),
      t('Factory service'),
      t('Operations'),
    ),
    '#rows' => $rows,
    '#empty' => t('There are no entries available.'),
    '#attributes' => array('id' => 'entry-order-layers'),
  );
  drupal_add_tabledrag('entry-order-layers', 'order', 'sibling', 'entry-order-weight');

  return $form;
}

/**
 * Map layers config form submit handler.
 */
function leaflet_map_form_layers_submit($form, &$form_state) {
  $layers = $form_state['values']['layers'];

  $layers_enabled = array();
  foreach ($layers as $id => $data) {
    if ($data['enable'] == 1) {
      $layers_enabled[$data['weight']] = $id;
    }
  }

  ksort($layers_enabled);

  $sources = $form_state['values']['sources'];

  $sources_enabled = array();
  foreach ($sources as $id => $data) {
    if ($data['enable'] == 1) {
      $sources_enabled[$data['weight']] = $id;
    }
  }

  ksort($sources_enabled);

  $form_state['item']->options['layers'] = array_values($layers_enabled);
  $form_state['item']->options['sources'] = array_values($sources_enabled);
}

/**
 * Map preview form handler.
 */
function leaflet_map_form_preview($form, &$form_state) {
  $map = leaflet_object_load('Map', $form_state['item']);
  $form['preview'] = $map->build();

  return $form;
}

/**
 * Map preview config form submit handler.
 */
function leaflet_map_form_preview_submit($form, &$form_state) {

}

/**
 * Helper function to sort an array by item weight.
 */
function _leaflet_maps_sort_by_weight($a, $b) {
  if (isset($a['weight']) && isset($b['weight'])) {
    return $a['weight'] < $b['weight'] ? -1 : 1;
  }
  return 0;
}
