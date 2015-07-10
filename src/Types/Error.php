<?php
/**
 * @file
 * Contains class Error.
 */

namespace Drupal\leaflet\Types;

use Drupal\leaflet\Component\Annotation\LeafletPlugin;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\leaflet\Types\Object;
use Drupal\service_container\Messenger\MessengerInterface;

/**
 * Class Error.
 *
 * @LeafletPlugin(
 *   id = "Error",
 *   arguments = {
 *     "@logger.channel.default",
 *     "@messenger"
 *   }
 * )
 *
 * Dummy class to avoid breaking the whole processing if a plugin class is
 * missing.
 */
class Error extends Object {

  /**
   * @var string
   */
  public $errorMessage;

  /**
   * The loggerChannel service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $loggerChannel;

  /**
   * The messenger service.
   *
   * @var \Drupal\service_container\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * {@inheritdoc}
   */
  public function __construct($configuration, $plugin_id, $plugin_definition, LoggerChannelInterface $logger_channel, MessengerInterface $messenger) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->loggerChannel = $logger_channel;
    $this->messenger = $messenger;

    foreach ($this->defaultProperties() as $property => $value) {
      $this->{$property} = $value;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function defaultProperties() {
    $properties = parent::defaultProperties();
    $properties['errorMessage'] = 'Error while loading @type @machine_name having service @service.';
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function init() {
    foreach ($this->configuration as $property => $value) {
      $this->{$property} = $this->configuration[$property];
    }

    if (isset($this->configuration['options'])) {
      $this->options = array_replace_recursive((array) $this->options, (array) $this->configuration['options']);
    }

    $this->loggerChannel->error($this->getMessage(), array('channel' => 'leaflet'));
    $this->messenger->addMessage($this->getMessage(), 'error', FALSE);
  }

  /**
   * {@inheritdoc}
   */
  public function getMessage() {
    $machine_name = isset($this->machine_name) ? $this->machine_name : 'undefined';
    $service = isset($this->factory_service) ? $this->factory_service : 'undefined';
    $type = isset($this->type) ? $this->type : 'undefined';

    return t($this->errorMessage, array(
      '@machine_name' => $machine_name,
      '@service' => $service,
      '@type' => $type,
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return 'Error';
  }
}
