<?php
/**
 * @file
 * Map: LMap.
 */

namespace Drupal\leaflet\Plugin\Map\LMap;
use Drupal\leaflet\Component\Annotation\LeafletPlugin;
use Drupal\leaflet\Config;
use Drupal\leaflet\Types\Map;

/**
 * Class LMap.
 *
 * @LeafletPlugin(
 *   id = "LMap"
 * )
 */
class LMap extends Map {

  /**
   * {@inheritdoc}
   */
  public function optionsForm(&$form, &$form_state) {
    $form['options']['ui'] = array(
      '#type' => 'fieldset',
      '#title' => t('Size of the map'),
      'width' => array(
        '#type' => 'textfield',
        '#title' => 'Width',
        '#default_value' => $this->getOption('width', 'auto'),
        '#parents' => array('options', 'width'),
      ),
      'height' => array(
        '#type' => 'textfield',
        '#title' => 'height',
        '#default_value' => $this->getOption('height', '300px'),
        '#parents' => array('options', 'height'),
      ),
    );

    $form['options']['view'] = array(
      '#type' => 'fieldset',
      '#title' => t('View: center'),
      '#tree' => TRUE,
    );

    if ($this->machine_name != Config::get('leaflet.edit_view_map')) {
      $map = \Drupal\leaflet\Leaflet::load('Map', Config::get('leaflet.edit_view_map'));
      if ($view = $this->getOption('view')) {
        $map->setOption('view', $view);
      }

      $form['options']['view']['map'] = array(
        '#type' => 'leaflet',
        '#description' => t('You can drag this map with your mouse.'),
        '#map' => $map,
      );
    }

    $form['options']['view']['center'] = array(
      '#tree' => TRUE,
      'lat' => array(
        '#type' => 'textfield',
        '#title' => 'Latitude',
        '#default_value' => $this->getOption(array('view', 'center', 'lat'), 0),
      ),
      'lon' => array(
        '#type' => 'textfield',
        '#title' => 'Longitude',
        '#default_value' => $this->getOption(array('view', 'center', 'lat'), 0),
      ),
    );
    $form['options']['view']['zoom'] = array(
      '#type' => 'textfield',
      '#title' => 'Zoom',
      '#default_value' => $this->getOption(array('view', 'zoom'), 0),
    );
    $form['options']['view']['minZoom'] = array(
      '#type' => 'textfield',
      '#title' => 'Min zoom',
      '#default_value' => $this->getOption(array('view', 'minZoom'), 0),
    );
    $form['options']['view']['maxZoom'] = array(
      '#type' => 'textfield',
      '#title' => 'Max zoom',
      '#default_value' => $this->getOption(array('view', 'maxZoom'), 0),
    );
    $form['options']['view']['limit_extent'] = array(
      '#type' => 'select',
      '#title' => t('Limit to extent'),
      '#empty_option' => t('Disabled'),
      '#empty_value' => '',
      '#options' => array('custom' => 'Custom extent', 'projection' => 'Map projection'),
      '#description' => t('If enabled navigation on the map is limited to the give extent.'),
      '#default_value' => $this->getOption(array('view', 'limit_extent'), FALSE),
    );
    $form['options']['view']['extent'] = array(
      '#type' => 'textfield',
      '#title' => t('Extent [minx, miny, maxx, maxy]'),
      '#default_value' => $this->getOption(array('view', 'extent'), ''),
      '#states' => array(
        'visible' => array(
          ':input[name="options[view][limit_extent]"]' => array('value' => 'custom'),
        ),
      ),
    );

    $data = array();
    $map_options = $this->getOptions();
    /* @var \Drupal\leaflet\Types\Object $object */
    foreach ($this->getCollection()->getFlatList() as $object) {
      $weight = 0;
      if (isset($map_options['capabilities']['options']['table'][$object->getMachineName()])) {
        $weight = array_search($object->getMachineName(), array_keys($map_options['capabilities']['options']['table']));
      }
      $data[$object->machine_name] = array(
        'name' => $object->name,
        'machine_name' => $object->machine_name,
        'text' => isset($map_options['capabilities']['options']['table'][$object->getMachineName()]) ? $map_options['capabilities']['options']['table'][$object->getMachineName()] : $object->getPluginDescription(),
        'weight' => $weight,
        'enabled' => isset($map_options['capabilities']['options']['table'][$object->getMachineName()]) ? TRUE : FALSE,
        'default' => 1,
      );
    }

    uasort($data, function($a, $b) {
      if ($a['enabled'] > $b['enabled']) {
        return -1;
      } else if ($a['enabled'] < $b['enabled']) {
        return 1;
      }
      if ($a['weight'] < $b['weight']) {
        return -1;
      } else if ($a['weight'] > $b['weight']) {
        return 1;
      }
      return 0;
    });

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
              '#attributes' => array(
                'class' => array('entry-order-weight'),
              ),
            )),
          array(
            'data' => array(
              '#type' => 'hidden',
              '#default_value' => $entry['machine_name'],
            )),
          array(
            'data' => array(
              '#type' => 'checkbox',
              '#title' => t('Enable'),
              '#title_display' => 'invisible',
              '#default_value' => $entry['enabled'],
            )),
          array(
            'data' => array(
              '#type' => 'textfield',
              '#title' => t('Text'),
              '#title_display' => 'invisible',
              '#default_value' => $entry['text'],
              '#maxlength' => 256
            )
          ),
          check_plain($entry['name']),
          check_plain($entry['machine_name']),
        ),
        'class' => array('draggable'),
      );
      // Build rows of the form elements in the table.
      $row_elements[$id] = array(
        'weight' => &$rows[$id]['data'][1]['data'],
        'machine_name' => &$rows[$id]['data'][2]['data'],
        'enabled' => &$rows[$id]['data'][3]['data'],
        'text' => &$rows[$id]['data'][4]['data'],
      );
    }

    $form['options']['capabilities'] = array(
      '#type' => 'fieldset',
      '#title' => 'Map description and capabilities',
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      'enabled' => array(
        '#type' => 'checkbox',
        '#title' => t('Enable map capabilities ?'),
        '#default_value' => (bool) $this->getOption(array('capabilities'), FALSE),
      ),
      'options' => array(
        '#type' => 'container',
        '#states' => array(
          'visible' => array(
            ':input[name="options[capabilities][enabled]"]' => array('checked' => TRUE),
          ),
        ),
        'container_type' => array(
          '#type' => 'select',
          '#title' => t('Container type'),
          '#options' => array(
            'fieldset' => 'Fieldset',
            'container' => 'Simple div'
          ),
          '#default_value' => $this->getOption(array('capabilities', 'options', 'container_type'), 'fieldset'),
        ),
        'title' => array(
          '#type' => 'textfield',
          '#title' => t('Title'),
          '#description' => t('Show a title ? Empty to disable.'),
          '#default_value' => $this->getOption(array('capabilities', 'options', 'title'), t('Map capabilities')),
          '#states' => array(
            'visible' => array(
              ':input[name="options[capabilities][options][container_type]"]' => array('value' => 'fieldset'),
            ),
          ),
        ),
        'description' => array(
          '#type' => 'textfield',
          '#title' => t('Description'),
          '#description' => t('Show a description ? Empty to disable.'),
          '#default_value' => $this->getOption(array('capabilities', 'options', 'description'), t('Description')),
          '#states' => array(
            'visible' => array(
              ':input[name="options[capabilities][options][container_type]"]' => array('value' => 'fieldset'),
            ),
          ),
        ),
        'collapsible' => array(
          '#type' => 'checkbox',
          '#title' => t('Collapsible'),
          '#default_value' => (bool) $this->getOption(array('capabilities', 'options', 'collapsible'), TRUE),
          '#states' => array(
            'visible' => array(
              ':input[name="options[capabilities][options][container_type]"]' => array('value' => 'fieldset'),
            ),
          ),
        ),
        'collapsed' => array(
          '#type' => 'checkbox',
          '#title' => t('Collapsed'),
          '#default_value' => (bool) $this->getOption(array('capabilities', 'options', 'collapsed'), TRUE),
          '#states' => array(
            'visible' => array(
              ':input[name="options[capabilities][options][container_type]"]' => array('value' => 'fieldset'),
            ),
          ),
        ),
      )
    );

    // Add the table to the form.
    $form['options']['capabilities']['options']['table'] = array(
      '#theme' => 'table',
      // The row form elements need to be processed and build,
      // therefore pass them as element children.
      'elements' => $row_elements,
      '#header' => array(
        // We need two empty columns for the weigth field and the cross.
        array('data' => NULL, 'colspan' => 2),
        array('data' => t('Enabled'), 'colspan' => 2),
        array('data' => t('Description'), 'colspan' => 1),
        t('Name'),
        t('Machine name'),
      ),
      '#rows' => $rows,
      '#empty' => t('There are no entries available.'),
      '#attributes' => array('id' => 'entry-order-objects'),
    );
    drupal_add_tabledrag('entry-order-objects', 'order', 'sibling', 'entry-order-weight');
  }

  /**
   * {@inheritdoc}
   */
  public function optionsFormSubmit($form, &$form_state) {
    $capabilities = array();
    if (isset($form_state['values']['options']['capabilities']['enabled']) && (bool) $form_state['values']['options']['capabilities']['enabled'] == TRUE) {
      $elements = (array) $form_state['values']['options']['capabilities']['options']['table']['elements'];

      uasort($elements, function($a, $b) {
        return $a['weight'] - $b['weight'];
      });

      foreach($elements as $data) {
        if ((bool) $data['enabled'] == TRUE && !empty($data['text'])) {
          $capabilities[$data['machine_name']] = $data['text'];
        }
      }
      $form_state['values']['options']['capabilities']['options']['table'] = $capabilities;
    } else {
      $this->clearOption('capabilities');
      unset($form_state['values']['options']['capabilities']);
    }

    parent::optionsFormSubmit($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function attached() {
    $attached = parent::attached();
    // TODO: Leaflet settings form by default to debug mode.
    $variant = NULL;
    if (Config::get('leaflet.debug') == TRUE) {
      $variant = 'debug';
    };
    $attached['libraries_load']['leaflet'] = array('leaflet', $variant);
    return $attached;
  }
}
