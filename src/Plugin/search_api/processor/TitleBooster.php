<?php

namespace Drupal\devb_encyclopedia\Plugin\search_api\processor;

use Drupal\node\NodeInterface;
use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\IndexInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;

/**
 * Adds title boosting field logic.
 *
 * @SearchApiProcessor(
 *   id = "title_booster",
 *   label = @Translation("Title booster"),
 *   description = @Translation("Title booster processor"),
 *   stages = {
 *     "add_properties" = 0
 *   },
 *   locked = false,
 *   hidden = false,
 * )
 */
class TitleBooster extends ProcessorPluginBase {

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
        'label' => $this->t('Title booster'),
        'description' => $this->t('Boosts single word titles when relevant'),
        'type' => 'string',
        'processor_id' => $this->getPluginId(),
        'hidden' => FALSE,
        'is_list' => TRUE,
      ];
      $properties['title_booster'] = new ProcessorProperty($definition);
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
        $fields = $this->getFieldsHelper()->filterForPropertyPath($item->getFields(), $item->getDatasourceId(), 'title_booster');
        foreach ($fields as $field) {
          $node_title = $node->label();
          if (!str_contains(trim($node_title), ' ')) {
            $field->addValue($node_title);
          }
        }
      }
    }
  }

}
