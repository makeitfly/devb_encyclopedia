<?php

namespace Drupal\devb_encyclopedia\Plugin\Field\FieldType;

use Drupal\Core\Field\Attribute\FieldType;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'archive' field type.
 */
#[FieldType(
  id: "archive",
  label: new TranslatableMarkup("Archive"),
  description: new TranslatableMarkup("Archive details for an encyclopedia record"),
  category: 'devb_encyclopedia',
  default_widget: "archive_widget",
  default_formatter: "archive_formatter",
)]
class Archive extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    return [
      'columns' => [
        'repository' => [
          'type' => 'varchar',
          'length' => 255,
        ],
        'inventory' => [
          'type' => 'varchar',
          'length' => 2048,
        ],
        'reference' => [
          'type' => 'varchar',
          'length' => 255,
        ],
      ],
      'indexes' => [
        'repository' => [
          'repository',
        ],
        'inventory' => [
          'inventory',
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    $properties = [];
    $properties['repository'] = DataDefinition::create('string')
      ->setLabel(t('The archive repository'))
      ->setRequired(FALSE);
    $properties['inventory'] = DataDefinition::create('uri')
      ->setLabel(t('The archive inventory URI'))
      ->setRequired(FALSE);
    $properties['reference'] = DataDefinition::create('string')
      ->setLabel(t('The archive reference'))
      ->setRequired(FALSE);
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty(): bool {
    return empty($this->getRepository()) && empty($this->getInventory()) && empty($this->getReference());
  }

  /**
   * Get the archive repository info.
   *
   * @return string|null
   *   The archive repository info.
   */
  public function getRepository(): ?string {
    try {
      return $this->get('repository')->getValue();
    }
    catch (\Exception $ex) {
    }
    return NULL;
  }

  /**
   * Get the archive inventory URI.
   *
   * @return string|null
   *   The archive inventory URI.
   */
  public function getInventory(): ?string {
    try {
      return $this->get('inventory')->getValue();
    }
    catch (\Exception $ex) {
    }
    return NULL;
  }

  /**
   * Get the archive reference info.
   *
   * @return string|null
   *   The archive reference info.
   */
  public function getReference(): ?string {
    try {
      return $this->get('reference')->getValue();
    }
    catch (\Exception $ex) {
    }
    return NULL;
  }

}
