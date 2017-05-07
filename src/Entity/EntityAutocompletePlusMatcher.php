<?php

namespace Drupal\entity_autocomplete_plus\Entity;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Tags;
use Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginManagerInterface;
use Drupal\Core\Entity\EntityAutocompleteMatcher;
use Drupal\Core\Entity\EntityManagerInterface;

/**
 * Matcher class to get autocompletion results for entity reference.
 */
class EntityAutocompletePlusMatcher extends EntityAutocompleteMatcher {

  // Injected EntityManager
  protected $entityManager;

  // The number of matches to return
  protected $n_match = 10;

  /**
   * Constructs a EntityAutocompletePlusMatcher object.
   *
   * @param \Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginManagerInterface $selection_manager
   *   The entity reference selection handler plugin manager.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   */
  public function __construct(SelectionPluginManagerInterface $selection_manager, EntityManagerInterface $entity_manager) {
    $this->selectionManager = $selection_manager;
    $this->entityManager = $entity_manager;
  }

  /*
   * {@inheritdoc]
   */
  public function getMatches($target_type, $selection_handler, $selection_settings, $string = '') {

    $matches = [];

    $options = [
      'target_type' => $target_type,
      'handler' => $selection_handler,
      'handler_settings' => $selection_settings,
    ];
    $handler = $this->selectionManager->getInstance($options);
    $storage_controller = $this->entityManager->getStorage($target_type);

    if (isset($string)) {
      // Get an array of matching entities.
      $match_operator = !empty($selection_settings['match_operator']) ? $selection_settings['match_operator'] : 'CONTAINS';
      $entity_labels = $handler->getReferenceableEntities($string, $match_operator, $this->n_match);

      // Loop through the entities and convert them into autocomplete output.
      foreach ($entity_labels as $values) {
        foreach ($values as $entity_id => $label) {
          // TODO: override the handler as well and only load the entity once
          $info = $this->getEntityInfo($storage_controller, $entity_id);
          $path = $info['path'];
          $key = "$label ($entity_id)"; // probably don't mess with the key in case this is saved verbatim
          $label = "$label - $path ($entity_id)";
          // Strip things like starting/trailing white spaces, line breaks and
          // tags.
          $key = preg_replace('/\s\s+/', ' ', str_replace("\n", '', trim(Html::decodeEntities(strip_tags($key)))));
          // Names containing commas or quotes must be wrapped in quotes.
          $key = Tags::encode($key);
          $matches[] = ['value' => $key, 'label' => $label];
        }
      }
    }

    return $matches;
  }

  /*
   * return information about the entity for use in the matcher UI
   *  - 'path': the Url::toString() representation for the entity
   */
  private function getEntityInfo($storage_controller, $entity_id) {
    $info = [];

    $entity = $storage_controller->load($entity_id);
    $info['path'] = $entity->toUrl()->toString();

    return $info;
  }

}