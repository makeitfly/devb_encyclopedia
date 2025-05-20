<?php

namespace Drupal\devb_encyclopedia\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\EntityReferenceFieldItemListInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\devb_encyclopedia\Plugin\Field\FieldType\EncyclopediaImage;

/**
 * Plugin for the 'Blazy' formatter for encyclopedia image galleries.
 */
#[FieldFormatter(
  id: 'encyclopedia_blazy_gallery',
  label: new TranslatableMarkup('Encyclopedia media gallery - Blazy'),
  description: new TranslatableMarkup('Display the encyclopedia gallery images via Blazy.'),
  field_types: [
    'encyclopedia_image',
  ],
)]
class EncyclopediaBlazyImageGalleryFormatter extends EncyclopediaBlazyImageFormatterBase {

  /**
   * {@inheritdoc}
   */
  protected function getEntitiesToView(EntityReferenceFieldItemListInterface $items, $langcode): array {
    // Filter image items to view in gallery. Only show images that have a
    // gallery order value set.
    $items->filter(function ($item) {
      if ($item instanceof EncyclopediaImage) {
        return $item->getOrderInGallery() && $item->getOrderInGallery() >= 0;
      }
      return FALSE;
    });

    // Sort image items based on the "order in gallery" value coming from
    // NodeGoat.
    $sort_items = clone $items;
    $sort_items_array = $sort_items->getIterator()?->getArrayCopy();
    if (is_array($sort_items_array)) {
      usort($sort_items_array, static function ($a, $b) {
        return $a->getOrderInGallery() - $b->getOrderInGallery();
      });
      foreach ($sort_items_array as $delta => $value) {
        $items->set($delta, $value);
      }
    }

    return parent::getEntitiesToView($items, $langcode);
  }

}
