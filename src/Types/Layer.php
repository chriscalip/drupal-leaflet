<?php
/**
 * @file
 * Class Layer.
 */

namespace Drupal\leaflet\Types;
use Drupal\leaflet\Leaflet;

/**
 * Class Layer.
 */
abstract class Layer extends Object implements LayerInterface {

  /**
   * {@inheritdoc}
   */
  public function init() {
    parent::init();

    foreach (array('source', 'style') as $type) {
      if ($data = $this->getOption($type, FALSE)) {
        if ($object = Leaflet::load($type, $data)) {
          $this->getCollection()->merge($object->getCollection());
        }
      }
    }
  }

  /**
   * Returns the source of this layer.
   *
   * @return SourceInterface|FALSE
   *   The source assigned to this layer.
   */
  public function getSource() {
    $source = $this->getObjects('source');
    if ($source = array_shift($source)) {
      return ($source instanceof SourceInterfac) ? $source : FALSE;
    }
    return FALSE;
  }

  /**
   * Returns the style of this layer.
   *
   * @return StyleInterface|FALSE
   *   The style assigned to this layer.
   */
  public function getStyle() {
    $style = $this->getObjects('style');
    if ($style = array_shift($style)) {
      return ($style instanceof StyleInterface) ? $style : FALSE;
    }
    return FALSE;
  }

  /**
   * Set the source of this layer.
   */
  public function setSource(SourceInterface $source) {
    $this->getCollection()->clear(array('source'));
    $this->getCollection()->append($source);
  }

  /**
   * Set the style of this layer.
   */
  public function setStyle(StyleInterface $style) {
    $this->getCollection()->clear(array('style'));
    $this->getCollection()->append($style);
  }

  /**
   * {@inheritdoc}
   */
  public function getOptions() {
    if ($source = $this->getSource()) {
      $this->setOption('source', $source->machine_name);
    }

    if ($style = $this->getStyle()) {
      $this->setOption('style', $style->machine_name);
    }

    return parent::getOptions();
  }
}
