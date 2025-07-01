<?php

namespace Drupal\devb_encyclopedia\Plugin\Field\FieldType;

use Drupal\Core\Field\Attribute\FieldType;
use Drupal\Core\Field\EntityReferenceFieldItemList;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataReferenceTargetDefinition;

/**
 * Field to reference entities in a qualified way.
 *
 * This means the relationship type will be defined through an additional field.
 * E.g. Person A related to Person B with the qualification "is father of".
 */
#[FieldType(
  id: "qualified_entity_reference",
  label: new TranslatableMarkup("Qualified Entity Reference"),
  description: new TranslatableMarkup("Reference to an entity, with an additional relationship qualification."),
  category: 'devb_encyclopedia',
  default_widget: "qualified_entity_reference_autocomplete",
  default_formatter: "qualified_entity_reference_basic_formatter",
  list_class: EntityReferenceFieldItemList::class,
)]
class QualifiedEntityReference extends EntityReferenceItem {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    $properties = parent::propertyDefinitions($field_definition);

    $properties['qualification'] = DataReferenceTargetDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Qualification'))
      ->setDescription(new TranslatableMarkup('Qualification of the entity reference.'))
      ->setRequired(FALSE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    $schema = parent::schema($field_definition);
    $schema['columns']['qualification'] = [
      'description' => 'The ID of the inspection type.',
      'type' => 'varchar',
      'length' => 255,
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function getPreconfiguredOptions(): array {
    // Do not preconfigure any qualified_entity_reference variants, since that
    // would clutter the new field select box with duplicate labels for each
    // common reference type.
    return [];
  }

}
