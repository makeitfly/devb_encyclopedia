<?php

namespace Drupal\devb_encyclopedia\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Derivative class that provides the menu links for the theme filters.
 */
class EncyclopediaThemeMenuLink extends DeriverBase implements ContainerDeriverInterface {

  /**
   * Creates an EncyclopediaThemeMenuLink instance.
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

    $term_storage = $this->entityTypeManager->getStorage('taxonomy_term');
    $terms = $term_storage->loadTree('themes', 0, 1, TRUE);
    foreach ($terms as $term) {
      $links['devb_encyclopedia.encyclopedia_theme_filter.' . $term->id()] = [
        'title' => $term->label(),
        'route_name' => 'view.encyclopedia_overview.page_search_overview',
        'options' => [
          'query' => [
            'f[0]' => 'theme:' . $term?->id(),
          ],
        ],
      ] + $base_plugin_definition;
    }

    return $links;
  }

}
