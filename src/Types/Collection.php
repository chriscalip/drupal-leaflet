<?php
/**
 * @file
 * Class Collection.
 */

namespace Drupal\leaflet\Types;

use Drupal\leaflet\Component\Annotation\LeafletPlugin;
use Drupal\Component\Plugin\PluginBase;
use Drupal\leaflet\Types\Object;


/**
 * Class Collection.
 *
 * @LeafletPlugin(
 *   id = "Collection"
 * )
 */
class Collection extends PluginBase {

  /**
   * @var array
   *  List of objects in this collection. The items have to be instances of
   * \Drupal\leaflet\Types\Object.
   */
  protected $objects = array();

  /**
   * Add object to this collection.
   *
   * @param ObjectInterface $object
   *   Object instance to add to this collection.
   */
  public function append(ObjectInterface $object) {
    $type = drupal_strtolower($object->getType());
    $this->delete($object);
    $this->objects[$type][$object->machine_name] = $object;

    // If the dependency system is working, we don't need this.
    uasort($this->objects[$type], function($a, $b) {
      return $a->getWeight() - $b->getWeight();
    });
  }

  /**
   * Remove object from this collection.
   *
   * @param \Drupal\leaflet\Types\ObjectInterface $object
   *   Object instance to remove from this collection.
   */
  public function delete(ObjectInterface $object) {
    foreach($this->getFlatList($object->getType()) as $candidate) {
      if ($candidate->machine_name == $object->machine_name) {
        unset($this->objects[$object->getType()][$object->machine_name]);
      }
    }
  }

  /**
   * Remove object type.
   *
   * @param array $types
   */
  public function clear(array $types = array()) {
    foreach($types as $type) {
      unset($this->objects[$type]);
    }
  }

  /**
   * Returns an array with all the attachments of the collection objects.
   *
   * @return array
   *   Array with all the attachments of the collection objects.
   */
  public function getAttached() {
    $attached = array();
    foreach($this->getFlatList() as $object) {
      $object_attached = $object->attached() + array(
          'js' => array(),
          'css' => array(),
          'library' => array(),
          'libraries_load' => array(),
        );
      foreach (array('js', 'css', 'library', 'libraries_load') as $type) {
        foreach ($object_attached[$type] as $data) {
          if (isset($attached[$type])) {
            array_unshift($attached[$type], $data);
          } else {
            $attached[$type] = array($data);
          }
        }
      }
    }
    return $attached;
  }

  /**
   * Array with all the JS settings of the collection objects.
   *
   * @return array
   *   All the JS settings of the collection objects.
   */
  public function getJS() {
    $settings = array();
    foreach($this->getFlatList() as $object) {
      $settings[$object->getType()][] = $object->getJS();
    }

    $settings = array_change_key_case($settings, CASE_LOWER);

    return $settings;
  }

  /**
   * Array with all the collection objects.
   *
   * @param string $type
   *   Type to filter for. If set only a list with objects of this type is
   *   returned.
   *
   * @return array
   *   List of objects of this collection or list of a specific type of objects.
   */
  public function getObjects($type = NULL) {
    if ($type == NULL) {
      return $this->objects;
    }

    $type = drupal_strtolower($type);

    if (isset($this->objects[$type])) {
      return $this->objects[$type];
    }

    return array();
  }

  /**
   * Flat array with all the collection objects.
   *
   * @param string $type
   *   Type to filter for. If set only a list with objects of this type is
   *   returned.
   *
   * @return \Drupal\leaflet\Types\Object[]
   *   List of objects of this collection or list of a specific type of objects.
   */
  public function getFlatList($type = NULL) {
    $list = array();

    if ($type != NULL && isset($this->objects[$type])) {
      foreach ($this->objects[$type] as $object) {
        $list[] = $object;
      }
    }
    else {
      foreach ($this->objects as $objects) {
        foreach ($objects as $object) {
          $list[] = $object;
        }
      }
    }

    return $list;
  }

  /**
   * Merges another collection into this one.
   *
   * @param \Drupal\leaflet\Types\Collection $collection
   *   The collection to merge into this one.
   */
  public function merge(Collection $collection) {
    foreach ($collection->getFlatList() as $object) {
      $this->append($object);
    }
  }

  /**
   * Get the collection as an export array with id's instead of objects.
   *
   * @return array
   */
  public function getExport() {
    $export = array();
    foreach($this->getFlatList() as $object) {
      $export[$object->getType()][] = $object->machine_name;
    }
    return $export;
  }
}
