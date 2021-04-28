<?php

namespace Drupal\entity_autocomplete_plus;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceModifierInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Modify the EntityAutocomplete service provider.
 */
class EntityAutocompletePlusServiceProvider implements ServiceModifierInterface {

  /**
   * The container under construction.
   *
   * @param \Drupal\Core\DependencyInjection\ContainerBuilder $container
   *   The ContainerBuilder whose service definitions can be altered.
   */
  public function alter(ContainerBuilder $container) {

    for ($id = 'entity.autocomplete_matcher'; $container->hasAlias($id); $id = (string) $container->getAlias($id));
    $definition = $container->getDefinition($id);
    $definition->setClass('Drupal\entity_autocomplete_plus\Entity\EntityAutocompletePlusMatcher');
    $definition->setArguments([
      new Reference('plugin.manager.entity_reference_selection'),
      new Reference('entity_type.manager'),
    ]);
    $container->setDefinition($id, $definition);
  }

}
