<?php

namespace Drupal\devb_encyclopedia\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\devb_encyclopedia\Plugin\Field\FieldType\EditorialInfo;

/**
 * Provides basic formatting for latest editorial info field data.
 */
#[FieldFormatter(
  id: 'editorial_info_latest_formatter',
  label: new TranslatableMarkup('Latest editorial record'),
  description: new TranslatableMarkup('Displays only the latest editorial record.'),
  field_types: [
    'editorial_info',
  ],
)]
class EditorialInfoLatestFormatter extends EditorialInfoFormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    $options = parent::defaultSettings();
    $options['show_label'] = TRUE;
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $form = parent::settingsForm($form, $form_state);

    $form['show_label'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show label in front of author name'),
      '#default_value' => $this->getSetting('show_label'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = [];
    $setting_value = $this->getSetting('show_label') ? $this->t('True') : $this->t('False');
    $summary[] = $this->t('Show label: @value', ['@value' => $setting_value]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];
    $sorted_field_data = $this->sortEditorialInfo($items->getValue());
    $sorted_field_data_keys = array_keys($sorted_field_data);

    // Get the first item from the array.
    if (!empty($sorted_field_data_keys)) {
      $latest_item_delta = end($sorted_field_data_keys);
      $item = $items->get($latest_item_delta);
      if ($item instanceof EditorialInfo) {
        $elements[0] = [
          '#prefix' => '<p class="editorial-info">',
          '#suffix' => '</p>',
          '#cache' => [
            'contexts' => [
              'languages:' . LanguageInterface::TYPE_INTERFACE,
            ],
          ],
        ];
        $elements[0] += $this->viewElement($item, $langcode);
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
    $element = [];
    if ($editorial_info->getAuthor()) {
      if ($this->getSetting('show_label')) {
        $element['author'] = [
          '#type' => 'inline_template',
          '#template' => '<div class="editorial-info--author">{{ value }}</div>',
          '#context' => [
            'value' => $editorial_info->getAuthor(),
          ],
        ];
      }
      else {
        $element['author'] = [
          '#type' => 'inline_template',
          '#template' => '<div class="editorial-info--author">{{ value }}</div>',
          '#context' => [
            'value' => $editorial_info->getAuthor(),
          ],
        ];
      }
    }
    return $element;
  }

}
