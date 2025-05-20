<?php

namespace Drupal\devb_encyclopedia\Plugin\search_api\processor;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\node\NodeInterface;
use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\IndexInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Adds period property for encyclopedia items.
 *
 * @SearchApiProcessor(
 *   id = "period",
 *   label = @Translation("Period values"),
 *   description = @Translation("Allows the indexing of period data for encyclopedia nodes."),
 *   stages = {
 *     "add_properties" = 0,
 *   },
 *   locked = false,
 *   hidden = false,
 * )
 */
class PeriodProperty extends ProcessorPluginBase {

  /**
   * The entity field manager service.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected EntityFieldManagerInterface $entityFieldManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $processor = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $processor->setEntityFieldManager($container->get('entity_field.manager'));
    return $processor;
  }

  /**
   * Sets the entity field manager.
   *
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The entity field manager.
   *
   * @return \Drupal\devb_encyclopedia\Plugin\search_api\processor\PeriodProperty
   *   The processor plugin.
   */
  public function setEntityFieldManager(EntityFieldManagerInterface $entity_field_manager): PeriodProperty {
    $this->entityFieldManager = $entity_field_manager;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function supportsIndex(IndexInterface $index): bool {
    return $index->id() === "encyclopedia";
  }

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(?DatasourceInterface $datasource = NULL): array {
    $properties = [];

    if ($datasource) {
      $definition = [
        'label' => $this->t('Period values'),
        'description' => $this->t('The period data on the node'),
        'type' => 'integer',
        'processor_id' => $this->getPluginId(),
        'hidden' => FALSE,
        'is_list' => TRUE,
      ];
      $properties['period'] = new ProcessorProperty($definition);
    }

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item) {
    $datasourceId = $item->getDatasourceId();
    if ($datasourceId === 'entity:node') {
      // Retrieve node data and add to index field value.
      $node = $item->getOriginalObject()?->getValue();
      if ($node instanceof NodeInterface) {
        $node_fields = $this->entityFieldManager->getFieldDefinitions('node', $node->bundle());
        $date_fields = array_filter($node_fields, static function ($field) {
          return $field instanceof FieldDefinitionInterface && $field->getType() === 'ymd_date_field_type';
        });
        $fields = $this->getFieldsHelper()->filterForPropertyPath($item->getFields(), $item->getDatasourceId(), 'period');
        foreach ($fields as $field) {
          foreach (array_keys($date_fields) as $date_field_name) {
            if ($node->hasField($date_field_name) && !$node->get($date_field_name)->isEmpty()) {
              // Extra year part from YMD field date value.
              $period_field_value = $node->get($date_field_name)->value;
              preg_match('@(\d{4})(\d{2})(\d{2})@', $period_field_value, $match);
              if (isset($match[1])) {
                $period_year = $match[1];
                if (!empty($period_year) && $period_year !== "0") {
                  $field->addValue($period_year);
                }
              }
            }
          }
        }
      }
    }
  }

}
