<?php
/**
 * @file
 * Interface ObjectInterface.
 */

namespace Drupal\leaflet\Types;
use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Interface ObjectInterface.
 */
interface ObjectInterface extends PluginInspectionInterface {
  /**
   * Return a list of default properties.
   *
   * @return array
   *   The default properties for this class.
   */
  public function defaultProperties();

  /**
   * Initializes the object.
   */
  public function init();

  /**
   * The type of this object.
   *
   * @return string|FALSE
   *   The object type or FALSE on failure.
   */
  public function getType();

  /**
   * @TODO was does this?
   *
   * @param string|array $parents
   *   @TODO Define how this has to look like if it is an array.
   */
  public function clearOption($parents);

  /**
   * Return the options array.
   *
   * @return array
   */
  public function getOptions();

  /**
   * Set the options array.
   *
   * @param array $options
   *   The options array.
   */
  public function setOptions(array $options = array());

  /**
   * Returns an option.
   *
   * @param string|array $parents
   *   @TODO Define how this has to look like if it is an array.
   * @param mixed $default_value
   *   The default value to return if the option isn't set. Set to NULL if not
   *   defined.
   *
   * @return mixed
   *   The value of the option or the defined default value.
   */
  public function getOption($parents, $default_value = NULL);

  /**
   * Set an option.
   *
   * @param string|array $parents
   *   @TODO Define how this has to look like if it is an array.
   *
   * @param mixed $value
   *   The value to set.
   */
  public function setOption($parents, $value);

  /**
   * Provides the options form to configure this object.
   *
   * @param array $form
   *   The form array by reference.
   * @param array $form_state
   *   The form_state array by reference.
   */
  public function optionsForm(&$form, &$form_state);

  /**
   * Validation callback for the options form.
   *
   * @param array $form
   *   The form array.
   * @param array $form_state
   *   The form_state array by reference.
   */
  public function optionsFormValidate($form, &$form_state);

  /**
   * Submit callback for the options form.
   *
   * @param array $form
   *   The form array.
   * @param array $form_state
   *   The form_state array by reference.
   */
  public function optionsFormSubmit($form, &$form_state);

  /**
   * Returns a list of attachments for building the render array.
   *
   * @return array
   *   The attachments to add.
   */
  public function attached();

  /**
   * Defines dependencies.
   *
   * @TODO Define how this has to look like.
   *
   * @return array
   *   The dependencies.
   */
  public function dependencies();

  /**
   * Whether or not this object has to be processed asynchronously.
   *
   * If true the map this object relates to won't be processes right away by
   * Drupals behaviour attach.
   *
   * @return bool
   *   Whether or not this object has to be processed asynchronously.
   */
  public function isAsynchronous();

  /**
   * Invoked before an objects render array is built.
   *
   * Mostly invoked by the map object.
   *
   * @param array $build
   *   The array with the build information.
   * @param \Drupal\leaflet\Types\ObjectInterface $context
   *   The context of the build. Mostly the map object.
   */
  public function preBuild(array &$build, ObjectInterface $context = NULL);

  /**
   * Invoked after an objects render array is built.
   *
   * Mostly invoked by the map object.
   *
   * @param array $build
   *   The array with the build information.
   * @param \Drupal\leaflet\Types\ObjectInterface $context
   *   The context of the build. Mostly the map object.
   */
  public function postBuild(array &$build, ObjectInterface $context = NULL);

  /**
   * Return an object, CTools Exportable.
   *
   * @return \StdClass
   */
  public function getExport();

  /**
   * Return the object configuration.
   *
   * @return array
   */
  public function getConfiguration();

  /**
   * Return an array of OL objects indexed by their type.
   *
   * @param string $type
   * @return array
   */
  public function getObjects($type = NULL);

  /**
   * Returns an array with the maps this object is attached on.
   *
   * @return array
   *   An array of map objects this object is attached on. Keyed by the map
   *   machine name.
   */
  public function getParents();

  /**
   * Return the module that provides this plugin.
   *
   * @return string
   */
  public function getProvider();

  /**
   * Returns the path to the plugin directory.
   *
   * @return string
   */
  public function getClassDirectory();

  /**
   * Returns the path to the class file.
   *
   * @return string
   */
  public function getClassPath();

  /**
   * Return the Collection object linked to the object.
   *
   * @return Collection
   */
  public function getCollection();

  /**
   * Return the JS to insert in the page when building the object.
   *
   * @return array
   */
  public function getJS();

  /**
   * Set the weight of an object.
   *
   * @param int $weight
   * @return void
   */
  public function setWeight($weight);

  /**
   * Get the weight of an object.
   *
   * @return int
   */
  public function getWeight();

  /**
   * Return a flat array containing leaflet Objects from the options array.
   *
   * @return Object[]
   */
  public function optionsToObjects();
}
