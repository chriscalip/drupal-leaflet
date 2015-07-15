<?php
/**
 * @file
 * Class Collection.
 */

namespace Drupal\leaflet\Types;

use Drupal\leaflet\Component\Annotation\LeafletPlugin;
use Drupal\Component\Plugin\PluginBase;
use Drupal\leaflet\Leaflet;
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
   * Import a flat list of Leaflet Objects.
   *
   * @param array ObjectInterface[]
   */
  public function import(array $import = array()) {
    foreach ($import as $object) {
      /* @var Object $object */
      $this->merge($object->getCollection());
    }
  }

  /**
   * Add object to this collection.
   *
   * @param ObjectInterface $object
   *   Object instance to add to this collection.
   */
  public function append(ObjectInterface $object) {
    $this->delete($object);
    $this->objects[] = $object;
  }

  /**
   * Add object to this collection.
   *
   * @param ObjectInterface $object
   *   Object instance to add to this collection.
   */
  public function prepend(ObjectInterface $object) {
    $this->delete($object);
    array_unshift($this->objects, $object);
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
      $list = array();
      foreach ($this->getFlatList() as $object) {
        $list[$object->getType()][] = $object;
      }
      return array_change_key_case($list, CASE_LOWER);
    }

    return $this->getFlatList($type);
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
    $list = $this->objects;

    if ($type != NULL) {
      $type = drupal_strtolower($type);
      $list = array_filter($this->objects, function($obj) use ($type) {
        /* @var Object $obj */
        return drupal_strtolower($obj->getType()) == $type;
      });
    }

    uasort($list, function($a, $b) {
      return $a->getWeight() - $b->getWeight();
    });

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
      $this->prepend($object);
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
    return array_change_key_case($export, CASE_LOWER);
  }
}
