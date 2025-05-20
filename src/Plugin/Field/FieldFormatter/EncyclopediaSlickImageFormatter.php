<?php

namespace Drupal\devb_encyclopedia\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\devb_encyclopedia\Plugin\Field\FieldType\EncyclopediaImage;
use Drupal\slick\Plugin\Field\FieldFormatter\SlickImageFormatter;

/**
 * Plugin implementation of the 'Slick Image' formatter for encyclopedia images.
 */
#[FieldFormatter(
  id: 'encyclopedia_slick_image',
  label: new TranslatableMarkup('Encyclopedia - Slick Image'),
  description: new TranslatableMarkup('Display the images as a Slick carousel.'),
  field_types: [
    'encyclopedia_image',
  ],
)]
class EncyclopediaSlickImageFormatter extends SlickImageFormatter {

  /**
   * List of image copyright labels.
   *
   * @var array
   */
  protected array $copyright;

  /**
   * {@inheritdoc}
   */
  public function commonViewElements(FieldItemListInterface $items, $langcode, array $entities = [], array $settings = []): array {
    // Store the copyright labels for each image.
    foreach ($items as $delta => $item) {
      $copyright = '';
      if ($item instanceof EncyclopediaImage) {
        $copyright = $item->getCopyright();
      }
      $this->copyright[$delta] = $copyright;
    }
    return parent::commonViewElements($items, $langcode, $entities, $settings);
  }

  /**
   * {@inheritdoc}
   */
  public function buildElements(array &$build, $files, $langcode): void {
    parent::buildElements($build, $files, $langcode);
    // Prepend the copyright labels for each image to alt text.
    foreach ($build['items'] as $delta => $item) {
      $alt = $item['caption']['alt']['#markup'] ?? '';
      if ($this->copyright[$delta]) {
        $build['items'][$delta]['caption']['alt'] = [
          '#type' => 'inline_template',
          '#template' => '&copy; {{ copyright }} - {{ alt }}',
          '#context' => [
            'copyright' => $this->copyright[$delta],
            'alt' => $alt,
          ],
        ];
      }
    }
  }

}
