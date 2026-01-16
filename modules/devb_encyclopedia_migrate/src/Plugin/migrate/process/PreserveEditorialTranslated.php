<?php

declare(strict_types=1);

namespace Drupal\devb_encyclopedia_migrate\Plugin\migrate\process;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\Attribute\MigrateProcess;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateLookupInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Preserves the 'translated' value from existing editorial_info field items.
 */
#[MigrateProcess(id: 'preserve_editorial_translated')]
class PreserveEditorialTranslated extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * Tracks the current delta per entity during sub_process iteration.
   */
  protected static array $deltaTracker = [];

  /**
   * Constructs a PreserveEditorialTranslated plugin.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\migrate\MigrateLookupInterface $migrateLookup
   *   The migrate lookup service.
   * @param \Drupal\migrate\Plugin\MigrationInterface|null $migration
   *   The current migration.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected MigrateLookupInterface $migrateLookup,
    protected ?MigrationInterface $migration = NULL,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, ?MigrationInterface $migration = NULL): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('migrate.lookup'),
      $migration,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property): int {
    $nodegoatId = $row->getSourceProperty('source/nodegoat_id');
    if (empty($nodegoatId) || !$this->migration) {
      return 0;
    }

    $currentDelta = $this->getCurrentDelta($nodegoatId);
    $entityId = $this->lookupEntityId($nodegoatId);
    if (!$entityId) {
      return 0;
    }

    return $this->getTranslatedValue($entityId, $currentDelta);
  }

  /**
   * Gets and increments the current delta for an entity.
   *
   * @param int $nodegoatId
   *   The given nodegoat ID.
   *
   * @return int
   *   Returns the delta.
   */
  protected function getCurrentDelta(int $nodegoatId): int {
    if (!isset(self::$deltaTracker[$nodegoatId])) {
      self::$deltaTracker[$nodegoatId] = 0;
    }
    else {
      self::$deltaTracker[$nodegoatId]++;
    }

    return self::$deltaTracker[$nodegoatId];
  }

  /**
   * Looks up the entity ID from the migration map.
   *
   * @param int $nodegoatId
   *   The given nodegoat ID.
   *
   * @return int|null
   *   Returns the lookup entity ID if present.
   */
  protected function lookupEntityId(int $nodegoatId): ?int {
    try {
      $result = $this->migrateLookup->lookup($this->migration->id(), [$nodegoatId]);
      return !empty($result) ? (int) reset($result)['nid'] : NULL;
    }
    catch (\Exception) {
      return NULL;
    }
  }

  /**
   * Gets the translated value for a specific delta.
   *
   * @param int $entityId
   *   The given entity ID.
   * @param int $delta
   *   The given delta.
   *
   * @return int
   *   Returns the 'translated' value.
   */
  protected function getTranslatedValue(int $entityId, int $delta): int {
    try {
      /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
      $entity = $this->entityTypeManager->getStorage('node')->loadUnchanged($entityId);
      if (!$entity?->hasField('field_editorial_info')) {
        return 0;
      }

      $fieldList = $entity->get('field_editorial_info');
      if ($fieldList->isEmpty() || !isset($fieldList[$delta])) {
        return 0;
      }

      return (int) ($fieldList[$delta]->translated ?? 0);
    }
    catch (\Exception) {
      return 0;
    }
  }

}
