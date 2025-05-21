<?php

namespace Drupal\devb_encyclopedia\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Link;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\devb_encyclopedia\Plugin\Field\FieldType\Archive;

/**
 * Provides basic formatting for archive field.
 */
#[FieldFormatter(
  id: 'archive_formatter',
  label: new TranslatableMarkup('Basic archive formatter'),
  description: new TranslatableMarkup('Basic archive formatter'),
  field_types: [
    'archive',
  ],
)]
class ArchiveFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];
    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#prefix' => '<p class="archive">',
        '#suffix' => '</p>',
        '#cache' => [
          'contexts' => [
            'languages:' . LanguageInterface::TYPE_INTERFACE,
          ],
        ],
      ];
      $elements[$delta] += $this->viewElement($item, $langcode);
    }

    return $elements;
  }

  /**
   * Builds a renderable array for a single archive item.
   *
   * @param \Drupal\devb_encyclopedia\Plugin\Field\FieldType\Archive $archive
   *   The archive field item.
   * @param string $langcode
   *   The language that should be used to render the field.
   *
   * @return array
   *   A render array.
   */
  protected function viewElement(Archive $archive, string $langcode): array {
    // Check if there is an inventory link.
    if (!$archive->getInventory()) {
      $element = [
        '#type' => 'inline_template',
        '#template' => '<span>– {{ repository }}</span>: {{ reference }}',
        '#context' => [
          'repository' => $archive->getRepository(),
          'reference' => $archive->getReference(),
        ],
      ];
    }
    else {
      $element = [
        '#type' => 'inline_template',
        '#template' => '<span>– {{ repository }}</span>: {{ reference }}',
        '#context' => [
          'repository' => $archive->getRepository(),
          'reference' => Link::fromTextAndUrl($archive->getReference(), Url::fromUri($archive->getInventory())),
        ],
      ];
    }
    return $element;
  }

}
