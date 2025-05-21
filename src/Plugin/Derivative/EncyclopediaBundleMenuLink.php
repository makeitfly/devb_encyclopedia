<?php

namespace Drupal\devb_encyclopedia\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\devb_encyclopedia\EncyclopediaBundles;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Derivative class that provides the menu links for the bundle filters.
 */
class EncyclopediaBundleMenuLink extends DeriverBase implements ContainerDeriverInterface {

  /**
   * Creates an EncyclopediaBundleMenuLink instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(protected EntityTypeManagerInterface $entityTypeManager) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition): array {
    $links = [];

    $bundles = EncyclopediaBundles::getBundles();
    $weight = 0;

    $node_storage = $this->entityTypeManager->getStorage('node');
    $node_type_storage = $this->entityTypeManager->getStorage('node_type');
    foreach ($bundles as $bundle_name) {
      $num_nodes = $node_storage->getQuery()
        ->condition('type', $bundle_name)
        ->condition('status', 1)
        ->accessCheck()
        ->count()
        ->execute();
      if ($num_nodes > 0) {
        $node_type = $node_type_storage->load($bundle_name);
        $links['devb_encyclopedia.encyclopedia_bundle_filter.' . $bundle_name] = [
          'title' => $node_type?->label(),
          'route_name' => 'view.encyclopedia_overview.page_search_overview',
          'options' => [
            'query' => [
              'f[0]' => 'content_type:' . $bundle_name,
            ],
          ],
          'weight' => $weight,
        ] + $base_plugin_definition;
      }

      $weight++;
    }

    return $links;
  }

}
