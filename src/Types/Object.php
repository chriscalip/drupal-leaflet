<?php
/**
 * @file
 * Class Object.
 */

namespace Drupal\leaflet\Types;
use Drupal\Component\Plugin\PluginBase;
use Drupal\leaflet\Config;
use Drupal\leaflet\leaflet;
use Drupal\leaflet\Types\Collection;
use Drupal\leaflet\Types\ObjectInterface;

/**
 * Class Object.
 */
abstract class Object extends PluginBase implements ObjectInterface {
  /**
   * The unique machine name.
   *
   * @var string
   */
  public $machine_name;

  /**
   * The human readable name.
   *
   * @var string
   */
  public $name;

  /**
   * A short description.
   *
   * @var string
   */
  public $description;

  /**
   * @var int
   */
  protected $weight = -1;

  /**
   * @var string
   */
  public $factory_service = NULL;

  /**
   * The array containing the options.
   *
   * @var array
   */
  protected $options = array();

  /**
   * @var Collection
   */
  protected $collection;

  /**
   * Holds all the attachment used by this object.
   *
   * @var array
   */
  protected $attached = array(
    'js' => array(),
    'css' => array(),
    'library' => array(),
    'libraries_load' => array(),
  );

  /**
   * {@inheritdoc}
   */
  public function defaultProperties() {
    return array(
      'machine_name' => NULL,
      'name' => NULL,
      'description' => NULL,
      'options' => array(),
      'factory_service' => NULL,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function init() {
    // Mash the provided configuration with the defaults.
    foreach ($this->defaultProperties() as $property => $value) {
      if (isset($this->configuration[$property])) {
        $this->{$property} = $this->configuration[$property];
      }
    }

    // We need to ensure the object has a proper machine name.
    if (empty($this->machine_name)) {
      $this->machine_name = drupal_html_id($this->getType() . '-' . time());
    }
  }

  /**
   * {@inheritdoc}
   *
   * @TODO What is this return? If it is the form, why is form by reference?
   */
  public function optionsForm(&$form, &$form_state) {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function optionsFormValidate($form, &$form_state) {}

  /**
   * {@inheritdoc}
   */
  public function optionsFormSubmit($form, &$form_state) {
    if (isset($form_state['values']['options'])) {
      $options = array_merge((array) $this->getOptions(), (array) $form_state['values']['options']);
      $this->setOptions($options);
    }

    $form_state['item'] = $this->getExport();
  }

  /**
   * {@inheritdoc}
   */
  public function preBuild(array &$build, ObjectInterface $context = NULL) {
    foreach ($this->getCollection()->getFlatList() as $object) {
      if ($object !== $this) {
        $object->preBuild($build, $this);
      }
    }

    drupal_alter('leaflet_object_preprocess', $build, $this);
  }

  /**
   * {@inheritdoc}
   */
  public function postBuild(array &$build, ObjectInterface $context = NULL) {
    foreach ($this->getCollection()->getFlatList() as $object) {
      if ($object !== $this) {
        $object->postBuild($build, $context);
      }
    }

    drupal_alter('leaflet_object_postprocess', $build, $this);
  }

  /**
   * {@inheritdoc}
   */
  public function build() {}

  /**
   * {@inheritdoc}
   */
  public function clearOption($parents) {
    $ref = &$this->options;

    if (is_string($parents)) {
      $parents = array($parents);
    }

    $last = end($parents);
    reset($parents);
    foreach ($parents as $parent) {
      if (isset($ref) && !is_array($ref)) {
        $ref = array();
      }
      if ($last == $parent) {
        unset($ref[$parent]);
      }
      else {
        if (isset($ref[$parent])) {
          $ref = &$ref[$parent];
        }
        else {
          break;
        }
      }
    }

    // Invalidate the Collection so it gets rebuilt with new options.
    $this->collection = NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setOption($parents, $value = NULL) {
    $ref = &$this->options;

    if (is_string($parents)) {
      $parents = array($parents);
    }

    foreach ($parents as $parent) {
      if (isset($ref) && !is_array($ref)) {
        $ref = array();
      }
      $ref = &$ref[$parent];
    }
    $ref = $value;

    // Invalidate the Collection so it gets rebuilt with new options.
    $this->collection = NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getOptions() {
    return $this->options;
  }

  /**
   * {@inheritdoc}
   */
  public function setOptions(array $options = array()) {
    $this->options = $options;

    // Invalidate the Collection so it gets rebuilt with new options.
    $this->collection = NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getExport() {
    $configuration = $this->getConfiguration();
    $options = $this->getOptions();

    $options = array_map_recursive('_floatval_if_numeric', (array) $options);
    $options = removeEmptyElements((array) $options);
    $configuration['options'] = $options;

    return (object) $configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function getOption($parents, $default_value = NULL) {
    if (is_string($parents)) {
      $parents = array($parents);
    }

    if (is_array($parents)) {
      $notfound = FALSE;

      if (!isset($this->options)) {
        $notfound = TRUE;
        $parents = array();
        $options = array();
      } else {
        $options = $this->options;
      }

      foreach ($parents as $parent) {
        if (isset($options[$parent])) {
          $options = $options[$parent];
        }
        else {
          $notfound = TRUE;
          break;
        }
      }
      if (!$notfound) {
        return $options;
      }
    }

    if (is_null($default_value)) {
      return FALSE;
    }

    return $default_value;
  }

  /**
   * {@inheritdoc}
   */
  public function attached() {
    if ($plugin = $this->getPluginDefinition()) {
      $path = $this->getClassDirectory();

      $jsdir = $path . '/js';
      $cssdir = $path . '/css';
      if (file_exists($jsdir)) {
        foreach (file_scan_directory($jsdir, '/.*\.js$/') as $file) {
          $this->attached['js'][$file->uri] = array(
            'data' => $file->uri,
            'type' => 'file',
            'group' => Config::get('leaflet.js_css.group'),
            'weight' => Config::get('leaflet.js_css.weight'),
          );
        }
      }
      if (file_exists($cssdir)) {
        foreach (file_scan_directory($cssdir, '/.*\.css$/') as $file) {
          $this->attached['css'][$file->uri] = array(
            'data' => $file->uri,
            'type' => 'file',
            'group' => Config::get('leaflet.js_css.group'),
            'weight' => Config::get('leaflet.js_css.weight'),
          );
        }
      }
    }

    return $this->attached;
  }

  /**
   * {@inheritdoc}
   */
  public function getObjects($type = NULL) {
    return array_values($this->getCollection()->getObjects($type));
  }

  /**
   * {@inheritdoc}
   */
  public function getParents() {
    $parents = array();

    foreach (leaflet::loadAll('Map') as $map) {
      foreach ($map->getObjects($this->getType()) as $object) {
        if ($object->machine_name == $this->machine_name) {
          $parents[$map->machine_name] = $map;
        }
      }
    }

    return $parents;
  }

  /**
   * {@inheritdoc}
   */
  public function dependencies() {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function getProvider() {
    $class = explode('\\', $this->pluginDefinition['class']);
    return $class[1];
  }

  /**
   * {@inheritdoc}
   */
  public function getClassDirectory() {
    $class = explode('\\', $this->pluginDefinition['class']);
    return drupal_get_path('module', $this->getProvider()) . '/src/' . implode('/', array_slice($class, 2, -1));
  }

  /**
   * {@inheritdoc}
   */
  public function getClassPath() {
    $class = explode('\\', $this->pluginDefinition['class']);
    return drupal_get_path('module', $this->getProvider()) . '/src/' . implode('/', array_slice($class, 2)) . '.php';
  }

  /**
   * {@inheritdoc}
   */
  public function isAsynchronous() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    $class = explode('\\', get_class($this));
    return $class[3];
  }

  /**
   * {@inheritdoc}
   */
  public function getCollection() {
    if (is_null($this->collection) || !($this->collection instanceof Collection)) {
      $this->collection = \Drupal::service('leaflet.Types')->createInstance('Collection');
      $this->collection->import($this->optionsToObjects());
      $this->getCollection()->append($this);
    }
    return $this->collection;
  }

  /**
   * {@inheritdoc}
   */
  public function optionsToObjects() {
    $import = array();

    foreach (leaflet::getPluginTypes() as $type) {
      foreach ($this->getOption($type . 's', array()) as $weight => $object) {
        if ($merge_object = leaflet::load($type, $object)) {
          $merge_object->setWeight($weight);
          $import[] = $merge_object;
        }
      }
    }

    return $import;
  }

  /**
   * {@inheritdoc}
   */
  public function getJS() {
    $export = $this->getExport();

foreach(leaflet::getPluginTypes() as $type) {
      unset($export->options[$type . 's']);
    }

    $js = array(
      'mn' => $export->machine_name,
      'fs' => $export->factory_service,
    );

    if (!empty($export->options)) {
      $js['opt'] = $export->options;
    }

    return $js;
  }

  /**
   * {@inheritdoc}
   */
  public function setWeight($weight) {
    $this->weight = $weight;
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight() {
    return intval($this->weight);
  }
}
