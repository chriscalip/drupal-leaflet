<?php

/**
 * @file
 * Leaflet Geofield widget functions.
 */

/**
 * Implements hook_field_widget_info().
 */
function leaflet_geofield_field_widget_info() {
  // Provide geofield widget.
  $widgets = array();

  $widgets['leaflet_geofield'] = array(
    'label' => t('Leaflet Map'),
    'field types' => array('geofield'),
    'behaviors' => array(
      'multiple values' => FIELD_BEHAVIOR_CUSTOM,
    ),
  );

  return $widgets;
}

/**
 * Implements hook_field_widget_settings_form().
 *
 * Bases on leaflet_field_widget_settings_form()
 *
 * @see leaflet_field_widget_settings_form()
 */
function leaflet_geofield_field_widget_settings_form($field, $instance) {
  $widget = $instance['widget'];
  $settings = $widget['settings'];
  $form = array();

  // Get preset options, filtered to those which have the GeoField component.
  ctools_include('export');
  $maps = ctools_export_crud_load_all('leaflet_maps');
  $map_options = array();
  foreach ($maps as $machine_name => $data) {
    /** @var \Drupal\leaflet\Map\Map $map */
    $map = leaflet_object_load('Map', $data);
    foreach ($map->getComponents() as $component) {
      if ($component instanceof \Drupal\leaflet\Component\Geofield) {
        $map_options[$machine_name] = $data->name;
      }
    }
  }

  if (empty($map_options)) {
    form_set_error('leaflet_map', "Error: You have no compatible leaflet maps. Make sure that at least one map has the 'GeoField' component enabled.");
  }

  $form['leaflet_map'] = array(
    '#type' => 'select',
    '#title' => t('Leaflet Map'),
    '#default_value' => isset($settings['leaflet_map']) ? $settings['leaflet_map'] : 'leaflet_geofield_map_geofield',
    '#options' => $map_options,
    '#description' => t('Select which Leaflet map you would like to use. Only maps which have the GeoField behavior may be selected. If your preferred map is not here, add the GeoField behavior to it first. The "Draw Features" bahavior is incompatible - presets with this behavior are not shown.'),
  );
  $form['data_storage'] = array(
    '#type' => 'radios',
    '#title' => t('Storage Options'),
    '#description' => t('Should the widget only allow simple features (points, lines, or polygons), or should the widget allow for complex features? Note that changing this setting from complex to simple after data has been entered can lead to data loss.'),
    '#options' => array(
      'single' => 'Store each simple feature as a separate field.',
      'collection' => 'Store as a single collection.',
    ),
    '#default_value' => (isset($settings['data_storage'])) ? $settings['data_storage'] : 'single',
  );
  $form['feature_types'] = array(
    '#title' => t('Available Features'),
    '#type' => 'checkboxes',
    '#options' => array(
      'point' => t('Point'),
      'path' => t('Path'),
      'polygon' => t('Polygon'),
    ),
    '#description' => t('Select what features are available to draw.'),
    '#default_value' => isset($settings['feature_types']) ? $settings['feature_types'] : array('point' => 'point', 'path' => 'path', 'polygon' => 'polygon'),
  );
  $form['allow_edit'] = array(
    '#title' => t('Allow shape modification'),
    '#type' => 'checkbox',
    '#description' => t('Can you edit and delete shapes.'),
    '#default_value' => isset($settings['allow_edit']) ? $settings['allow_edit'] : 1,
  );

  // Add optional Geocoder support.
  $use_geocoder = isset($settings['use_geocoder']) ? $settings['use_geocoder'] : 0;
  $geocoder_form = array(
    '#type' => 'fieldset',
    '#title' => t('Geocoder settings'),
  );
  if (module_exists('geocoder')) {
    $geocoder_form['use_geocoder'] = array(
      '#type' => 'checkbox',
      '#title' => t('Enable geocoding of location data'),
      '#default_value' => $use_geocoder,
      // Can't nest this in a fieldset element without affecting data storage so
      // instead hardcode one.
      '#prefix' => '<fieldset><legend><span="fieldset-legend">' . t('Geocoder settings') . '</span></legend><div class="fieldset-wrapper">',
    );

    // Load the Geocoder widget settings.
    module_load_include('inc', 'geocoder', 'geocoder.widget');
    $new = geocoder_field_widget_settings_form($field, $instance);

    $new['geocoder_field']['#options'][$field['field_name']] = t('Add extra field');

    // Show the geocoder fields only if geocoder is selected.
    leaflet_widget_add_states($new, ':input[name="instance[widget][settings][use_geocoder]"]');

    // Close the fieldset we opened in the #prefix to use_geocoder.
    $element_children = element_children($new);
    $new[end($element_children)]['#suffix'] = '</div></fieldset>';
    $geocoder_form += $new;
  }
  else {
    $geocoder_form['add_geocoder'] = array(
      '#markup' => t('Optionally, install the <a href="http//drupal.org/project/geocoder">Geocoder</a> module and add an <a href="http://drupal.org/project/addressfield">Address field</a> to enable mapping by address.'),
    );
  }
  $form += $geocoder_form;
  return $form;
}

/**
 * Implements hook_field_widget_form().
 */
function leaflet_geofield_field_widget_form(&$form, &$form_state, $field, $instance, $langcode, $items, $delta, $element) {
  $widget = $instance['widget'];
  $parents = $form['#parents'];
  $field_name = $field['field_name'];
  $id_prefix = implode('-', array_merge($parents, array($field_name, $delta)));
  $wrapper_id = drupal_html_id($id_prefix . '-use-geocoder-wrapper');

  $settings_defaults = array(
    'leaflet_map' => 'leaflet_geofield_map_geofield',
    'data_storage' => 'single',
    'feature_types' => array(
      'point' => 'point',
      'path' => 'path',
      'polygon' => 'polygon',
    ),
    'allow_edit' => 1,
  );
  $settings = array_merge($settings_defaults, $widget['settings']);

  $leaflet_map_id = !empty($instance['widget']['settings']['leaflet_map']) ? $instance['widget']['settings']['leaflet_map'] : 'leaflet_geofield_map_geofield';

  /** @var \Drupal\leaflet\Map\Map $map */
  $map = leaflet_object_load('Map', $leaflet_map_id);
  foreach ($map->getComponents() as $component) {
    // Configure component based on the widget settings.
    if ($component instanceof \Drupal\leaflet\Component\Geofield) {
      $component->setOption('dataType', array('WKT' => 'WKT'));
      $component->setOption('dataProjection', 'EPSG:4326');

      // Conversion from geofield to leaflet geofield feature types.
      $feature_types = $settings['feature_types'];
      if (!empty($feature_types['path'])) {
        $feature_types['LineString'] = 'LineString';
        unset($feature_types['path']);
      }
      if (!empty($feature_types['polygon'])) {
        $feature_types['Polygon'] = 'Polygon';
        unset($feature_types['polygon']);
      }
      if (!empty($feature_types['point'])) {
        $feature_types['Point'] = 'Point';
        unset($feature_types['point']);
      }
      $component->setOption('typeOfFeature', $feature_types);

      $action_feature = array('draw' => 'draw');
      if (!empty($settings['allow_edit'])) {
        $action_feature['modify'] = 'modify';
      }
      $component->setOption('actionFeature', $action_feature);

      $component->setOption('initialData', $items);
      $component->setOption('featureLimit', $field['cardinality']);
      $component->setOption('inputFieldName', 'geom');
    }
  }

  $map_build = $map->build();
  if ($map_build) {
    $element += $map_build;
  }
  // Remove the #value key to ensure it's written by the form handler.
  unset($element['geom']['#value']);

  $element['geom']['#required'] = isset($instance['required']) ? $instance['required'] : FALSE;
  $element['input_format']['#value'] = GEOFIELD_INPUT_WKT;
  $element['#data_storage'] = (!empty($settings['data_storage'])) ? $settings['data_storage'] : 'collection';

  // Attach the widget and field settings so they can be accesses by JS and
  // validate functions.
  $element['#widget_settings'] = $settings;
  $element['#widget_settings']['allow_edit'] = (bool) $settings['allow_edit'];
  $element['#widget_settings']['feature_types'] = array();
  foreach ($settings['feature_types'] as $feature => $feature_setting) {
    if ($feature_setting) {
      $element['#widget_settings']['feature_types'][] = $feature;
    }
  }
  $element['#widget_settings']['cardinality'] = $field['cardinality'];

  // Make sure we set the input-format to WKT so geofield knows how to process
  // it.
  $element['input_format'] = array(
    '#type' => 'value',
    '#value' => GEOFIELD_INPUT_WKT,
  );

  // Time to deal with optional geocoder integration
  // Conditionally add geocoder button.
  $is_settings_form = isset($form['#title']) && $form['#title'] == t('Default value');
  if (!$is_settings_form && !empty($settings['use_geocoder']) && !empty($settings['geocoder_field'])) {
    if ($settings['geocoder_field'] == $element['#field_name']) {
      // Extra field.
      $element['geocoder_input'] = array(
        '#type' => 'textfield',
        '#title' => t('Geocode'),
        '#description' => t('Enter the place to geocode.'),
      );
      $label = $element['geocoder_input']['#title'];
    }
    elseif ($field = field_info_instance($instance['entity_type'], $settings['geocoder_field'], $instance['bundle'])) {
      $label = $field['label'];
    }
    else {
      switch ($settings['geocoder_field']) {
        case 'title':
          $label = t('Title');
          break;

        case 'name':
          $label = t('Name');
          break;

        default:
          $label = $settings['geocoder_field'];
      }
    }

    $element['#prefix'] = '<div id="' . $wrapper_id . '">';
    $element['#suffix'] = '</div>';

    $element['use_geocoder'] = array(
      '#type' => 'submit',
      '#name' => strtr($id_prefix, '-', '_') . '_use_geocoder',
      '#value' => t('Find using @field field', array('@field' => $label)),
      '#attributes' => array('class' => array('field-use-geocoder-submit')),
      // Avoid validation errors for e.g. required fields but do pass the value
      // of the geocoder field.
      '#limit_validation_errors' => array(
        array_merge($parents, array($field_name, $langcode)),
        array($settings['geocoder_field'], $langcode),
      ),
      '#ajax' => array(
        'callback' => 'leaflet_widget_geocode',
        'wrapper' => $wrapper_id,
        'effect' => 'fade',
      ),
      '#submit' => array('leaflet_use_geocoder_submit'),
    );
  }

  // Add the element to an array, because it's the format that
  // FIELD_BEHAVIOR_CUSTOM expects.
  $full_element = array($element);

  // Override the element_validate as we need to deal with deltas.
  unset($full_element[0]['#element_validate']);
  $full_element['#element_validate'][] = 'leaflet_geofield_widget_element_validate';

  return $full_element;
}

/**
 * Implements hook_geocoder_geocode_values_alter().
 */
function leaflet_geofield_geocoder_geocode_values_alter(&$source_field_values, &$field_info, $handler_settings, $field_instance) {
  // If this is an leaflet_geofield pointing to its extra field adjust the
  // field values and mock a text field.
  if (isset($field_instance['widget']['settings']['geocoder_field']) && $field_instance['widget']['type'] == 'leaflet_geofield' && $field_instance['widget']['settings']['geocoder_field'] == $field_info['field_name']) {
    if (isset($source_field_values[0]['geocoder_input'])) {
      $source_field_values = array(
        array('value' => $source_field_values[0]['geocoder_input']),
      );
      $field_info = array('type' => 'text');
    }
  }
}
