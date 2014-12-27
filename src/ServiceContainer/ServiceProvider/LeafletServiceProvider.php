<?php
/**
 * @file
 * Contains \Drupal\leaflet\RenderCache\ServiceProvider\RenderCacheServiceProvider
 */

namespace Drupal\leaflet\ServiceContainer\ServiceProvider;

use Drupal\service_container\ServiceContainer\ServiceProvider\ServiceContainerServiceProvider;

/**
 * Provides leaflet service definitions.
 */
class LeafletServiceProvider extends ServiceContainerServiceProvider {

  /**
   * {@inheritdoc}
   */
  public function getContainerDefinition() {
    $services = array();
    $parameters = array();

    $services['service_container'] = array(
      'class' => '\Drupal\service_container\DependencyInjection\Container',
    );

    $services['leaflet.manager'] = array(
      'class' => '\Drupal\service_container\Plugin\ContainerAwarePluginManager',
      'arguments' => array(
        'leaflet.manager.internal.'
      ),
      'calls' => array(
        array(
          'setContainer',
          array(
            '@service_container'
          )
        )
      )
    );

    $services['leaflet.manager.internal.error'] = array(
      'class' => '\Drupal\leaflet\Types\Error',
      'arguments' => array('@logger.channel.default')
    );

    $services['leaflet.manager.internal.collection'] = array(
      'class' => '\Drupal\leaflet\Types\Collection'
    );

    foreach(leaflet_ctools_plugin_type() as $plugin_type => $data) {
      $plugin_type = drupal_strtolower($plugin_type);
      $services['leaflet.' . $plugin_type] = array();
      $parameters['service_container.plugin_managers']['ctools']['leaflet.' . $plugin_type] = array(
        'owner' => 'leaflet',
        'type' => drupal_ucfirst($plugin_type)
      );
    }

    return array(
      'parameters' => $parameters,
      'services' => $services,
    );
  }
}
