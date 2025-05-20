<?php

namespace Drupal\devb_encyclopedia\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Link;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\devb_encyclopedia\Plugin\Field\FieldType\EditorialInfo;

/**
 * Provides basic formatting for editorial info field.
 */
#[FieldFormatter(
  id: 'editorial_info_basic_formatter',
  label: new TranslatableMarkup('Basic editorial info formatter'),
  description: new TranslatableMarkup('Basic editorial info formatter'),
  field_types: [
    'editorial_info',
  ],
)]
class EditorialInfoBasicFormatter extends EditorialInfoFormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];
    $sorted_field_data = $this->sortEditorialInfo($items->getValue());
    $sorted_field_data_keys = array_keys($sorted_field_data);

    foreach ($sorted_field_data_keys as $delta) {
      $item = $items->get($delta);
      if ($item instanceof EditorialInfo) {
        $elements[$delta] = [
          '#prefix' => '<p class="editorial-info">',
          '#suffix' => '</p>',
          '#cache' => [
            'contexts' => [
              'languages:' . LanguageInterface::TYPE_INTERFACE,
            ],
          ],
        ];
        $elements[$delta] += $this->viewElement($item, $langcode);
      }
    }

    return $elements;
  }

  /**
   * Builds a renderable array for a single editorial info item.
   *
   * @param \Drupal\devb_encyclopedia\Plugin\Field\FieldType\EditorialInfo $editorial_info
   *   The editorial info field item.
   * @param string $langcode
   *   The language that should be used to render the field.
   *
   * @return array
   *   A render array.
   */
  protected function viewElement(EditorialInfo $editorial_info, string $langcode): array {
    $author = $editorial_info->getAuthor();
    $date_prefix = '';
    if ($editorial_info->getDate()) {
      $date_prefix = $editorial_info->getDate() . ': ';
    }
    $value = $date_prefix . $author;

    // Wrap value in link, if available.
    if ($editorial_info->getLink()) {
      try {
        $url = Url::fromUri($editorial_info->getLink());
        $value .= ' ' . Link::fromTextAndUrl($this->t('(pdf)'), $url)->toString();
      }
      catch (\InvalidArgumentException $e) {
        // Suppress the exception and fall back to value without link.
      }
    }
    $element['value'] = [
      '#markup' => $value,
    ];
    return $element;
  }

}
