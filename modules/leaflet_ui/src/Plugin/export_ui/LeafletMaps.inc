<?php
/**
 * @file
 * CTools Export UI plugin definition for maps.
 */

/**
 * CTools Export UI plugin definition.
 */
function leaflet_ui_LeafletMaps_ctools_export_ui() {
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
      'class' => '\\Drupal\\leaflet_ui\\UI\\LeafletMaps',
      'file' => 'LeafletMaps.php',
    ),

    'use wizard' => TRUE,
    'form info' => array(
      'order' => array(
        'start' => t('Administrative settings'),
        'settings' => t('Map settings'),
        'layers' => t('Layers'),
        'controls' => t('Controls'),
        'components' => t('Components'),
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
        'controls' => array(
          'form id' => 'leaflet_map_form_controls',
        ),
        'components' => array(
          'form id' => 'leaflet_map_form_components',
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
  $class = new Drupal\leaflet_ui\UI\LeafletMaps();
  $class->init($form_state['plugin']);
  $class->edit_form($form, $form_state);
  return $form;
}

/**
 * Map base config form validation handler.
 */
function leaflet_map_form_start_validate($form, &$form_state) {
  $class = new Drupal\leaflet_ui\UI\LeafletMaps();
  $class->init($form_state['plugin']);
  $class->edit_form_validate($form, $form_state);
}

/**
 * Map base config form submit handler.
 */
function leaflet_map_form_start_submit($form, &$form_state) {
  $class = new Drupal\leaflet_ui\UI\LeafletMaps();
  $class->init($form_state['plugin']);
  $class->edit_form_submit($form, $form_state);
}

/**
 * Map settings config form handler.
 */
function leaflet_map_form_settings($form, &$form_state) {
  if (!isset($form_state['item']->factory_service)) {
    $form_state['item']->factory_service = 'leaflet.Map:LMap';
  }
  if (($map = \Drupal\leaflet\Leaflet::load('Map', $form_state['item'])) == TRUE) {
    $map->optionsForm($form, $form_state);
    $form['options']['#tree'] = TRUE;
  }

  return $form;
}

/**
 * Map settings config form validation handler.
 */
function leaflet_map_form_settings_validate($form, &$form_state) {
  $map = \Drupal\leaflet\Leaflet::load('map', $form_state['item']);
  $map->optionsFormValidate($form, $form_state);
}

/**
 * Map settings config form submit handler.
 */
function leaflet_map_form_settings_submit($form, &$form_state) {
  $map = \Drupal\leaflet\Leaflet::load('map', $form_state['item']);

  if (empty($map->factory_service)) {
    $map->factory_service = 'leaflet.Map:LMap';
  }

  $map->optionsFormSubmit($form, $form_state);
}

/**
 * Map layers config form handler.
 */
function leaflet_map_form_layers($form, &$form_state) {
  $all_layers = \Drupal\leaflet\Leaflet::loadAllExportable('Layer');
  if (($map = \Drupal\leaflet\Leaflet::load('Map', $form_state['item'])) == FALSE) {
    return;
  }

  $layers = array();
  foreach ($map->getObjects('layer') as $layer) {
    if (is_object($layer) && isset($all_layers[$layer->machine_name])) {
      $layers[$layer->machine_name] = $all_layers[$layer->machine_name];
      $layers[$layer->machine_name]->enabled = 1;
      unset($all_layers[$layer->machine_name]);
    }
  }

  $layers = array_merge($layers, $all_layers);

  $data = array();

  $i = 0;
  /* @var \Drupal\leaflet\Types\Layer $layer */
  foreach ($layers as $machine_name => $layer) {
    if (is_object($layer)) {
      $data[$machine_name] = array(
        'name' => $layer->name,
        'machine_name' => $layer->machine_name,
        'factory_service' => $layer->factory_service,
        'weight' => $i++,
        'enabled' => isset($layer->enabled) ? $layer->enabled : 0,
        'default' => 1,
      );
    }
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
            '#type' => 'hidden',
            '#default_value' => $entry['machine_name'],
            '#parents' => array('layers', $id, 'machine_name'),
          )),
        array(
          'data' => array(
            '#type' => 'checkbox',
            '#title' => t('Enable'),
            '#title_display' => 'invisible',
            '#default_value' => $entry['enabled'],
            '#parents' => array('layers', $id, 'enabled'),
          )),
        check_plain($entry['name']),
        check_plain($entry['machine_name']),
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
      'machine_name' => &$rows[$id]['data'][3]['data'],
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
      array('data' => t('Enabled'), 'colspan' => 2),
      t('Name'),
      t('Machine name'),
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
  $map = \Drupal\leaflet\Leaflet::load('map', $form_state['item']);

  $layers_enabled = array_filter($form_state['values']['layers'], function($item) {
    return (bool) $item['enabled'];
  });

  $map->setOption('layers', array_keys($layers_enabled));

  $form_state['item'] = $map->getExport();
}

/**
 * Map controls config form handler.
 */
function leaflet_map_form_controls($form, &$form_state) {
  $all_controls = \Drupal\leaflet\Leaflet::loadAllExportable('Control');

  if (!isset($form_state['item']->options['controls'])) {
    $form_state['item']->options['controls'] = array();
  }

  $data = array();

  // Ensure the controls are ordered correctly.
  foreach (array_reverse($form_state['item']->options['controls']) as $machine_name) {
    if (isset($all_controls[$machine_name])) {
      $control = $all_controls[$machine_name];
      $all_controls = array($machine_name => $control) + $all_controls;
    }
  }

  $i = 0;
  foreach ($all_controls as $machine_name => $control) {
    if (is_object($control)) {
      $data[$machine_name] = array(
        'name' => $control->name,
        'machine_name' => $control->machine_name,
        'description' => $control->description,
        'weight' => $i++,
        'enabled' => (int) in_array($control->machine_name, $form_state['item']->options['controls']),
        'default' => 1,
      );
    }
  }

  $rows = array();
  $row_elements = array();
  foreach ($data as $id => $entry) {
    $rows[$id] = array(
      'data' => array(
        array(
          'class',
          array(
            'entry-cross'
          )
        ),
        array(
          'data' => array(
            '#type' => 'weight',
            '#title' => t('Weight'),
            '#title_display' => 'invisible',
            '#default_value' => $entry['weight'],
            '#parents' => array('controls', $id, 'weight'),
            '#attributes' => array(
              'class' => array('entry-order-weight'),
            ),
          )
        ),
        array(
          'data' => array(
            '#type' => 'checkbox',
            '#title' => t('Enable'),
            '#title_display' => 'invisible',
            '#default_value' => $entry['enabled'],
            '#parents' => array('controls', $id, 'enabled'),
          )
        ),
        check_plain($entry['name']),
        check_plain($entry['machine_name']),
        check_plain($entry['description']),
        l(t('Edit'), 'admin/structure/leaflet/controls/list/' . $entry['machine_name'] . '/edit',
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

  $form['options']['#tree'] = TRUE;

  // Add the table to the form.
  $form['controls']['table_controls'] = array(
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
      t('Operations'),
    ),
    '#rows' => $rows,
    '#empty' => t('There are no entries available.'),
    '#attributes' => array('id' => 'entry-order-controls'),
  );
  drupal_add_tabledrag('entry-order-controls', 'order', 'sibling', 'entry-order-weight');

  return $form;
}

/**
 * Map controls config form submit handler.
 */
function leaflet_map_form_controls_submit($form, &$form_state) {
  $map = \Drupal\leaflet\Leaflet::load('map', $form_state['item']);

  $controls_enabled = array_filter($form_state['values']['controls'], function($item) {
    return (bool) $item['enabled'];
  });

  $map->setOption('controls', array_keys($controls_enabled));

  $form_state['item'] = $map->getExport();
}

/**
 * Map components config form handler.
 */
function leaflet_map_form_components($form, &$form_state) {
  $all_components = \Drupal\leaflet\Leaflet::loadAllExportable('Component');
  $data = array();

  // See if we can get rid of these kinds of stuff
  $form_state['item']->options += array(
    'controls' => array(),
    'components' => array()
  );

  // Ensure the components are ordered correctly.
  foreach (array_reverse($form_state['item']->options['components']) as $machine_name) {
    if (isset($all_components[$machine_name])) {
      $component = $all_components[$machine_name];
      $all_components = array($machine_name => $component) + $all_components;
    }
  }

  $i = 0;
  foreach ($all_components as $machine_name => $component) {
    if (is_object($component)) {
      $data[$machine_name] = array(
        'name' => $component->name,
        'machine_name' => $component->machine_name,
        'description' => $component->description,
        'weight' => $i++,
        'enabled' => (int) in_array($component->machine_name, $form_state['item']->options['components']),
        'default' => 1,
      );
    }
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
            '#parents' => array('components', $id, 'weight'),
            '#attributes' => array(
              'class' => array('entry-order-weight'),
            ),
          )),
        array(
          'data' => array(
            '#type' => 'checkbox',
            '#title' => t('Enable'),
            '#title_display' => 'invisible',
            '#default_value' => $entry['enabled'],
            '#parents' => array('components', $id, 'enabled'),
          )),
        check_plain($entry['name']),
        check_plain($entry['machine_name']),
        check_plain($entry['description']),
        l(t('Edit'), 'admin/structure/leaflet/components/list/' . $entry['machine_name'] . '/edit',
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

  $form['options']['#tree'] = TRUE;

  // Add the table to the form.
  $form['components']['table_components'] = array(
    '#theme' => 'table',
    // The row form elements need to be processed and build,
    // therefore pass them as element children.
    'elements' => $row_elements,
    '#header' => array(
      // We need two empty columns for the weight field and the cross.
      array('data' => NULL, 'colspan' => 2),
      t('Enabled'),
      t('Name'),
      t('Machine name'),
      t('Description'),
      t('Operations'),
    ),
    '#rows' => $rows,
    '#empty' => t('There are no entries available.'),
    '#attributes' => array('id' => 'entry-order-components'),
  );
  drupal_add_tabledrag('entry-order-components', 'order', 'sibling', 'entry-order-weight');

  return $form;
}

/**
 * Map components config form submit handler.
 */
function leaflet_map_form_components_submit($form, &$form_state) {
  $map = \Drupal\leaflet\Leaflet::load('map', $form_state['item']);

  $components_enabled = array_filter($form_state['values']['components'], function ($item) {
    return (bool) $item['enabled'];
  });

  $map->setOption('components', array_keys($components_enabled));

  $form_state['item'] = $map->getExport();
}

/**
 * Map preview form handler.
 */
function leaflet_map_form_preview($form, &$form_state) {
  if (($map = \Drupal\leaflet\Leaflet::load('Map', $form_state['item'])) == TRUE) {
    $form['preview'] = $map->build();
  }
  return $form;
}

/**
 * Map preview config form submit handler.
 */
function leaflet_map_form_preview_submit($form, &$form_state) {

}
