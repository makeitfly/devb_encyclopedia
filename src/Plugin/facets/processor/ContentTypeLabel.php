<?php

namespace Drupal\devb_encyclopedia\Plugin\facets\processor;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\TypedData\TranslatableInterface;
use Drupal\devb_encyclopedia\EncyclopediaBundles;
use Drupal\facets\FacetInterface;
use Drupal\facets\Processor\BuildProcessorInterface;
use Drupal\facets\Processor\ProcessorPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Transforms the content type key to its label.
 *
 * @FacetsProcessor(
 *   id = "content_type_subtype_label",
 *   label = @Translation("Transform content type bundle to label"),
 *   description = @Translation("Display the content type label."),
 *   stages = {
 *     "build" = 5
 *   }
 * )
 */
class ContentTypeLabel extends ProcessorPluginBase implements BuildProcessorInterface, ContainerFactoryPluginInterface {

  /**
   * Constructs a new object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   *   The language manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(
    array $configuration,
    string $plugin_id,
    $plugin_definition,
    protected LanguageManagerInterface $languageManager,
    protected EntityTypeManagerInterface $entityTypeManager
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
      $container->get('language_manager'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(FacetInterface $facet, array $results) {
    $language_interface = $this->languageManager->getCurrentLanguage();

    /** @var \Drupal\facets\Result\ResultInterface $result */
    $bundles = [];
    foreach ($results as $result) {
      if (in_array($result->getRawValue(), EncyclopediaBundles::getBundles())) {
        $bundles[$result->getRawValue()] = $result->getRawValue();
      }
    }

    // Load all indexed entities of this type.
    $bundle_entities = $this->entityTypeManager
      ->getStorage('node_type')
      ->loadMultiple($bundles);

    // Loop over all results.
    foreach ($results as $result) {
      $raw_value = $result->getRawValue();
      if (in_array($raw_value, $bundles)) {
        $node_type = $bundle_entities[$raw_value];
        if ($node_type instanceof TranslatableInterface && $node_type->hasTranslation($language_interface->getId())) {
          $node_type = $node_type->getTranslation($language_interface->getId());
        }
        $facet->addCacheableDependency($node_type);
        // Overwrite the result's display value.
        $result->setDisplayValue($node_type->label());
      }
    }

    // Return the results with the new display values.
    return $results;
  }

  /**
   * {@inheritdoc}
   */
  public function supportsFacet(FacetInterface $facet): bool {
    return $facet->getFieldIdentifier() == 'content_type_and_subtype';
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts(): array {
    return Cache::mergeContexts(parent::getCacheContexts(), ['languages:language_interface']);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge(): int {
    // This will work unless the Search API Query uses "wrong" caching. Ideally
    // we would set a cache tag to invalidate the cache whenever a translatable
    // entity is added or changed. But there's no tag in drupal yet.
    return Cache::PERMANENT;
  }

}
