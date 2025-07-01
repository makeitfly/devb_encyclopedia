<?php

namespace Drupal\devb_encyclopedia\Plugin\Field\FieldType;

use Drupal\Core\Field\Attribute\FieldType;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\file\Plugin\Field\FieldType\FileFieldItemList;
use Drupal\image\Plugin\Field\FieldType\ImageItem;

/**
 * Plugin implementation of the 'encyclopedia_image' field type.
 */
#[FieldType(
  id: "encyclopedia_image",
  label: new TranslatableMarkup("Encyclopedia image"),
  description: new TranslatableMarkup("References an encyclopedia record image"),
  category: 'devb_encyclopedia',
  default_widget: "encyclopedia_image_widget",
  default_formatter: "encyclopedia_image_formatter",
  list_class: FileFieldItemList::class,
  constraints: [
    'ReferenceAccess' => [],
    'FileValidation' => [],
  ],
  column_groups: [
    'file' => [
      'label' => new TranslatableMarkup("File"),
      'columns' => ['target_id', 'width', 'height'],
      'require_all_groups_for_translation' => TRUE,
    ],
    'alt' => [
      'label' => new TranslatableMarkup("Alt"),
      'translatable' => TRUE,
    ],
    'title' => [
      'label' => new TranslatableMarkup("Title"),
      'translatable' => TRUE,
    ],
  ],
)]
class EncyclopediaImage extends ImageItem {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    $schema = parent::schema($field_definition);
    $schema['columns']['copyright'] = [
      'description' => "Image copyright information",
      'type' => 'varchar',
      'length' => 1024,
    ];
    $schema['columns']['orientation'] = [
      'description' => "Image orientation information",
      'type' => 'varchar_ascii',
      'length' => 12,
    ];
    $schema['columns']['main_image'] = [
      'description' => "Image is main image?",
      'type' => 'int',
      'size' => 'tiny',
    ];
    $schema['columns']['order_text'] = [
      'description' => "Image order in text",
      'type' => 'int',
      'size' => 'tiny',
    ];
    $schema['columns']['order_gallery'] = [
      'description' => "Image order in gallery",
      'type' => 'int',
      'size' => 'tiny',
    ];
    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    $properties = parent::propertyDefinitions($field_definition);

    $properties['copyright'] = DataDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t("Image title text, for the image's 'title' attribute."));

    $properties['orientation'] = DataDefinition::create('string')
      ->setLabel(t('Orientation'))
      ->setDescription(t("Image orientation (portrait or landscape)."));

    $properties['main_image'] = DataDefinition::create('boolean')
      ->setLabel(t('Main image'))
      ->setDescription(t("Image is main image."));

    $properties['order_text'] = DataDefinition::create('integer')
      ->setLabel(t('Order in text'))
      ->setDescription(t("Image order in text."))
      ->setRequired(FALSE);

    $properties['order_gallery'] = DataDefinition::create('integer')
      ->setLabel(t('Order in gallery'))
      ->setDescription(t("Image order in gallery."))
      ->setRequired(FALSE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition): array {
    $values = parent::generateSampleValue($field_definition);
    $values['copyright'] = '2022 ADVN';
    $values['orientation'] = 'landscape';
    $values['main_image'] = TRUE;
    $values['order_text'] = 1;
    $values['order_gallery'] = -1;
    return $values;
  }

  /**
   * Get the copyright value.
   *
   * @return string|null
   *   The copyright value.
   */
  public function getCopyright(): ?string {
    try {
      return $this->get('copyright')->getValue();
    }
    catch (\Exception $ex) {
    }
    return NULL;
  }

  /**
   * Get the image orientation.
   *
   * @return string|null
   *   The image orientation.
   */
  public function getOrientation(): ?string {
    try {
      return $this->get('orientation')->getValue();
    }
    catch (\Exception $ex) {
    }
    return NULL;
  }

  /**
   * Check if this is the main image.
   *
   * @return bool
   *   Whether this is the main image.
   */
  public function isMainImage(): bool {
    try {
      return (bool) $this->get('main_image')->getValue();
    }
    catch (\Exception $ex) {
    }
    return FALSE;
  }

  /**
   * Get image order in text.
   *
   * @return int|null
   *   The image order in text.
   */
  public function getOrderInText(): ?int {
    try {
      return $this->get('order_text')->getValue();
    }
    catch (\Exception $ex) {
    }
    return NULL;
  }

  /**
   * Get image order in gallery.
   *
   * @return int|null
   *   The image order in gallery.
   */
  public function getOrderInGallery(): ?int {
    try {
      return $this->get('order_gallery')->getValue();
    }
    catch (\Exception $ex) {
    }
    return NULL;
  }

}
