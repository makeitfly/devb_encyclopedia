<?php

namespace Drupal\devb_encyclopedia\Plugin\search_api\processor;

use Drupal\node\NodeInterface;
use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\IndexInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;

/**
 * Adds subtype hierarchy to content type search field.
 *
 * @SearchApiProcessor(
 *   id = "content_type_and_subtype_hierarchy",
 *   label = @Translation("Content type & subtype hierarchy"),
 *   description = @Translation("Allows the indexing of subtype data for the content type field."),
 *   stages = {
 *     "add_properties" = 0
 *   },
 *   locked = false,
 *   hidden = false,
 * )
 */
class ContentTypeAndSubtypeHierarchy extends ProcessorPluginBase {

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
        'label' => $this->t('Content type & subtype'),
        'description' => $this->t('The node content type & subtype'),
        'type' => 'string',
        'processor_id' => $this->getPluginId(),
        'hidden' => FALSE,
        'is_list' => TRUE,
      ];
      $properties['content_type_and_subtype'] = new ProcessorProperty($definition);
    }

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item): void {
    $datasourceId = $item->getDatasourceId();
    if ($datasourceId === 'entity:node') {
      // Retrieve node data and add to index field value.
      $node = $item->getOriginalObject()?->getValue();
      if ($node instanceof NodeInterface) {
        $fields = $this->getFieldsHelper()->filterForPropertyPath($item->getFields(), $item->getDatasourceId(), 'content_type_and_subtype');
        foreach ($fields as $field) {
          $field->addValue($node->bundle());
          if ($node->hasField('field_subtype') && !$node->get('field_subtype')->isEmpty()) {
            $subtypes = $node->get('field_subtype')->getValue();
            $subtypes = array_map(static function ($value) {
              return $value['value'];
            }, $subtypes);
            foreach ($subtypes as $subtype) {
              $field->addValue($subtype);
            }
          }
        }
      }
    }
  }

}
