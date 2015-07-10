<?php
/**
 * @file
 * Contains Leaflet
 */

namespace Drupal\leaflet;
use Drupal\leaflet\Types\Error;
use Drupal\leaflet\Types\Object;
use Drupal\leaflet\Types\ObjectInterface;

/**
 * Class Leaflet.
 */
class Leaflet {

  /**
   * Gets a list of available plugin types.
   *
   * @param string $object_type
   *   The plugin type.
   *
   * @return array
   *   Leaflet objects types options.
   */
  public static function getOLObjectsOptions($object_type) {
    $options = array();
    $service_basename = 'leaflet.' . $object_type;
    foreach (\Drupal::service($service_basename)->getDefinitions() as $service => $data) {
      $name = isset($data['label']) ? $data['label'] : $data['id'];
      $options[$service_basename . ':' . $data['id']] = $name;
    }
    asort($options);
    return $options;
  }

  /**
   * Gets a list of Leaflet exportable.
   *
   * @param string $type
   *   The plugin .
   *
   * @return array
   *   Leaflet object instance.
   */
  public static function loadAllAsOptions($type = NULL) {
    $options = array();
    $type = drupal_ucfirst(drupal_strtolower($type));
    foreach (Leaflet::loadAllExportable($type) as $machine_name => $data) {
      if (is_object($data)) {
        $options[$machine_name] = $data->name;
      }
    }
    return $options;
  }

  /**
   * Load a CTools exportable.
   *
   * @param string $object_type
   *   Type of object to load: map|layer|source|control|interaction|style|component
   * @param string $export_id
   *   The exportable id
   *
   * @return array
   */
  public static function loadExportable($object_type, $export_id) {
    ctools_include('export');
    return ctools_export_crud_load('leaflet_' . drupal_strtolower(check_plain($object_type)) . 's', $export_id);
  }

  /**
   * Gets all available OL objects.
   *
   * @param string $type
   *   The plugin type
   * @return array
   *   Array of Leaflet CTools object instances. (stdClass)
   */
  public static function loadAllExportable($type = NULL) {
    ctools_include('export');
    $exports = ctools_export_crud_load_all('leaflet_' . drupal_strtolower(check_plain($type)) . 's');
    uasort($exports, function($a, $b) {
      return strcmp($a->name, $b->name);
    });
    return $exports;
  }

  /**
   * Create an object instance for an export.
   *
   * @param string $object_type
   *   The object type to look up. See leaflet_object_types() for a list of
   *   available object types.
   * @param array|string|object $export
   *   The exported object.
   *
   * @return Object|Error
   *   Returns the instance of the requested object or an instance of
   *   Error on error.
   */
  public static function load($object_type = NULL, $export) {
    $object_type = drupal_ucfirst(drupal_strtolower(check_plain($object_type)));

    /* @var \Drupal\leaflet\Types\Object $object */
    $object = NULL;
    $configuration = array();

    if (is_array($export)) {
      $configuration = $export;
    }
    if (is_object($export) && ($export instanceof \StdClass)) {
      $array_object = new \ArrayObject($export);
      $configuration = $array_object->getArrayCopy();
    }
    if (is_object($export) && ($export instanceof ObjectInterface)) {
      $configuration = $export->getConfiguration();
    }
    if (is_string($export)) {
      $configuration = (array) Leaflet::loadExportable($object_type, $export);
    }

    if (is_array($configuration) && isset($configuration['factory_service'])) {
      // Bail out if the base service can't be found - likely due a registry
      // rebuild.
      if (!\Drupal::hasService('leaflet.Types')) {
        return NULL;
      }
      list($plugin_manager_id, $plugin_id) = explode(':', $configuration['factory_service'], 2);
      if (\Drupal::hasService($plugin_manager_id)) {
        $plugin_manager = \Drupal::service($plugin_manager_id);
        if ($plugin_manager->hasDefinition($plugin_id)) {
          $object = $plugin_manager->createInstance($plugin_id, $configuration);
        } else {
          $configuration += array(
            'type' => $object_type,
            'errorMessage' => 'Unable to load @type @machine_name',
          );
          $object = \Drupal::service('leaflet.Types')->createInstance('Error', $configuration);
        }
      }
      else {
        $configuration += array(
          'type' => $object_type,
          'errorMessage' => 'Service <em>@service</em> doesn\'t exists, unable to load @type @machine_name',
        );
        $object = \Drupal::service('leaflet.Types')->createInstance('Error', $configuration);
      }
    }
    else {
      $configuration += array(
        'type' => $object_type,
        'machine_name' => $export,
        'errorMessage' => 'Unable to load CTools exportable @type @machine_name.',
      );
      $object = \Drupal::service('leaflet.Types')->createInstance('Error', $configuration);
    }

    if (isset($configuration['disabled']) && (bool) $configuration['disabled'] == 1) {
      $object->disabled = 1;
    }

    $object->init();

    return $object;
  }

  /**
   * Load all objects.
   *
   * @param null $type
   *   Type of object to load: map|layer|source|control|interaction|style|component
   * @return \Drupal\leaflet\Types\Object[]
   */
  public static function loadAll($object_type = NULL) {
    $objects = array();
    foreach (Leaflet::loadAllExportable($object_type) as $exportable) {
      if (is_object($exportable)) {
        $objects[$exportable->machine_name] = Leaflet::load($object_type, $exportable);
      }
    }
    return $objects;
  }

  public static function save(Object $object) {
    ctools_include('export');
    $configuration = $object->getConfiguration();
    $export = $object->getExport();
    ctools_export_crud_save($configuration['table'], $export);
  }

  /**
   * Return the list of Leaflet plugins type this module provides.
   *
   * @param array $filter
   *   The values to filter out of the result array.
   *
   * @return array
   */
  public static function getPluginTypes(array $filter = array()) {
    $plugins = array();

    foreach(\Drupal::getContainer()->getDefinitions() as $id => $definition) {
      $id = explode(".", drupal_strtolower($id));
      if (count($id) == 2) {
        if ($id[0] == 'leaflet') {
          if (isset($definition['tags']) && (0 === strpos($definition['tags'][0]['plugin_manager_definition']['directory'], 'Plugin/'))) {
            $plugins[$id[1]] = $id[1];
          }
        }
      }
    }

    asort($plugins);

    return array_udiff(array_values($plugins), $filter, 'strcasecmp');
  }

  /**
   * Return information about the Leaflet library if installed.
   *
   * @return array|false
   */
  public static function getLibrary() {
    return libraries_detect('leaflet');
  }

  /**
   * @TODO describe what this does.
   *
   * @param callable $func
   *   Function to call.
   * @param array $arr
   *   The array to process.
   *
   * @return array
   *   The processed array
   */
  public static function array_map_recursive($func, array $arr) {
    /*
    // This is the PHP Version >= 5.5
    // $func must be a callable.
    array_walk_recursive($arr, function(&$v) use ($func) {
      $v = $func($v);
    });
    return $arr;
    */
    foreach ($arr as $key => $value) {
      if (is_array($arr[$key])) {
        $arr[$key] = self::array_map_recursive($func, $arr[$key]);
      }
      else {
        $arr[$key] = call_user_func($func, $arr[$key]);
      }
    }
    return $arr;
  }

  /**
   * Ensures a value is of type float if it is a numeric value.
   *
   * @param mixed $var
   *   The value to type cast if possible.
   *
   * @return float|mixed
   *   The variable - casted to type float if possible.
   */
  public static function floatval_if_numeric($var) {
    if (is_numeric($var)) {
      return is_float($var) ? floatval($var) : intval($var);
    }
    return $var;
  }

  /**
   * Recursively removes empty values from an array.
   *
   * Empty means empty($value) AND not 0.
   *
   * @TODO could we use array_walk_recursive($array, 'array_filter');?
   *
   * @param array $array
   *   The array to clean.
   *
   * @return array
   *   The cleaned array.
   */
  public static function removeEmptyElements($array) {
    foreach ($array as $key => $value) {
      if (empty($value) && $value != 0) {
        unset($array[$key]);
      }
      elseif (is_array($value)) {
        $array[$key] = self::removeEmptyElements($value);
      }
    }
    return $array;
  }

}
