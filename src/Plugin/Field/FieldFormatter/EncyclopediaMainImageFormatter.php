<?php

namespace Drupal\devb_encyclopedia\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\EntityReferenceFieldItemListInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\devb_encyclopedia\Plugin\Field\FieldType\EncyclopediaImage;

/**
 * Plugin implementation of the 'encyclopedia_main_image_formatter' formatter.
 */
#[FieldFormatter(
  id: 'encyclopedia_main_image_formatter',
  label: new TranslatableMarkup('Encyclopedia main image'),
  field_types: [
    'encyclopedia_image',
  ],
)]
class EncyclopediaMainImageFormatter extends EncyclopediaBlazyImageFormatterBase {

  /**
   * {@inheritdoc}
   */
  protected function getEntitiesToView(EntityReferenceFieldItemListInterface $items, $langcode): array {
    $has_main_image = FALSE;
    $entities = parent::getEntitiesToView($items, $langcode);
    foreach ($entities as $delta => $file) {
      $item = $file->_referringItem;
      if ($item instanceof EncyclopediaImage) {
        if ($has_main_image || !$item->isMainImage()) {
          unset($entities[$delta]);
        }
        elseif ($item->isMainImage()) {
          $has_main_image = TRUE;
        }
      }
    }
    return $entities;
  }

}
