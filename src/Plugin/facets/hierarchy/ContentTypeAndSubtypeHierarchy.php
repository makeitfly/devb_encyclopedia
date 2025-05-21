<?php

namespace Drupal\devb_encyclopedia\Plugin\facets\hierarchy;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\facets\Hierarchy\HierarchyPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the Content type and subtype hierarchy class.
 *
 * @FacetsHierarchy(
 *   id = "content_type_and_subtype",
 *   label = @Translation("Content type & subtype hierarchy"),
 *   description = @Translation("Hierarchy structure for DEVB Encyclopedia.")
 * )
 */
class ContentTypeAndSubtypeHierarchy extends HierarchyPluginBase {

  /**
   * Constructs a ContentTypeAndSubtypeHierarchy object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    protected EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getParentIds($id): array {
    $query = \Drupal::database()->select('node__field_subtype', 'nfs');
    $query->condition('nfs.field_subtype_value', $id);
    $query->addField('nfs', 'bundle');
    $result = $query->distinct()->execute()->fetchCol();
    return $result ?? [];
  }

  /**
   * {@inheritdoc}
   */
  public function getNestedChildIds($id): array {
    $query = \Drupal::database()->select('node__field_subtype', 'nfs');
    $query->condition('nfs.bundle', $id);
    $query->addField('nfs', 'field_subtype_value');
    $result = $query->distinct()->execute()->fetchCol();
    return $result ?? [];
  }

  /**
   * {@inheritdoc}
   */
  public function getChildIds(array $ids): array {
    $parents = [];
    foreach ($ids as $id) {
      $parents[$id] = $this->getNestedChildIds($id);
    }
    $parents = array_filter($parents);
    return $parents;
  }

}
