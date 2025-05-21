<?php

namespace Drupal\devb_encyclopedia\Plugin\Field\FieldFormatter;

use Drupal\blazy\Plugin\Field\FieldFormatter\BlazyImageFormatter;

/**
 * Base class for DEVB specific implementations with blazy image formatter.
 */
class EncyclopediaBlazyImageFormatterBase extends BlazyImageFormatter {

  /**
   * {@inheritdoc}
   */
  public function buildElements(array &$build, $files, $langcode): void {
    parent::buildElements($build, $files, $langcode);

    foreach ($build['items'] as $delta => $build_item) {
      if (isset($build_item['#delta'])) {
        $item = $build_item['#item'];
        // Alter the lightbox caption to include both copyright and alt text.
        if ($item->copyright) {
          $caption = '&copy; ' . $item->copyright;
          if ($item->alt) {
            $caption .= ' - ' . $item->alt;
          }
          $build['items'][$delta]['#build']['settings']['box_caption'] = 'custom';
          $build['items'][$delta]['#build']['settings']['box_caption_custom'] = $caption;
        }
      }
    }
  }

}
