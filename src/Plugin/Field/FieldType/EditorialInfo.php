<?php

namespace Drupal\devb_encyclopedia\Plugin\Field\FieldType;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Field\Attribute\FieldType;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'editorial_info' field type.
 */
#[FieldType(
  id: "editorial_info",
  label: new TranslatableMarkup("Editorial information"),
  description: new TranslatableMarkup("Editorial information regarding an encyclopedia record."),
  category: new TranslatableMarkup("DEVB Encyclopedia"),
  default_widget: "editorial_info_default",
  default_formatter: "editorial_info_basic_formatter",
)]
class EditorialInfo extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    return [
      'columns' => [
        'date' => [
          'type' => 'varchar',
          'length' => 50,
        ],
        'author' => [
          'type' => 'varchar',
          'length' => 255,
        ],
        'operation' => [
          'type' => 'varchar',
          'length' => 255,
        ],
        'link' => [
          'type' => 'varchar',
          'length' => 2048,
        ],
        'description' => [
          'type' => 'text',
          'size' => 'big',
        ],
      ],
      'indexes' => [
        'date' => [
          'date',
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    $properties = [];
    $properties['date'] = DataDefinition::create('string')
      ->setLabel(t('The date of the editorial change.'))
      ->setRequired(FALSE);
    $properties['author'] = DataDefinition::create('string')
      ->setLabel(t('The name of the author making the editorial change.'))
      ->setRequired(FALSE);
    $properties['operation'] = DataDefinition::create('string')
      ->setLabel(t('Description of the editorial change operation.'))
      ->setRequired(FALSE);
    $properties['link'] = DataDefinition::create('string')
      ->setLabel(t('Link URI to editorial document.'))
      ->setRequired(FALSE);
    $properties['description'] = DataDefinition::create('string')
      ->setLabel(t('Description of the editorial change.'))
      ->setRequired(FALSE);
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty(): bool {
    return empty($this->getDate()) && empty($this->getAuthor()) && empty($this->getOperation()) && empty($this->getLink()) && empty($this->getDescription());
  }

  /**
   * Get the editorial change date.
   *
   * @return string|null
   *   The editorial change date.
   */
  public function getDate(): ?string {
    try {
      return $this->get('date')->getValue();
    }
    catch (\Exception $ex) {
    }

    return NULL;
  }

  /**
   * Get the author name.
   *
   * @return string|null
   *   The author name.
   */
  public function getAuthor(): ?string {
    try {
      if ($value = $this->get('author')->getValue()) {
        if (!is_string($value)) {
          return NULL;
        }

        $author = Xss::filter($value);
        // Filter out "no label" output from NodeGoat.
        return str_replace('[L](lbl_no_name)', '', $author);
      }
    }
    catch (\Exception $ex) {
    }

    return NULL;
  }

  /**
   * Get the editorial change operation.
   *
   * @return string|null
   *   The editorial change operation.
   */
  public function getOperation(): ?string {
    try {
      return $this->get('operation')->getValue();
    }
    catch (\Exception $ex) {
    }
  }

  /**
   * Get the document link.
   *
   * @return string|null
   *   The document link.
   */
  public function getLink(): ?string {
    try {
      $linkValue = $this->get('link')->getValue();

      // If the value is only the url, empty it.
      if ($linkValue === 'https://tekst.devb.be'
        || $linkValue === 'https://tekst.devb.be/') {
        $linkValue = '';
      }

      // If we don't have http in the url, add the full url.
      if (!empty($linkValue) && !str_contains($linkValue, 'http')) {
        $linkValue = 'https://tekst.devb.be/' . $linkValue;
      }

      return $linkValue;
    }
    catch (\Exception $ex) {
    }
  }

  /**
   * Get the description.
   *
   * @return string|null
   *   The description.
   */
  public function getDescription(): ?string {
    try {
      return $this->get('description')->getValue();
    }
    catch (\Exception $ex) {
    }
  }

}
