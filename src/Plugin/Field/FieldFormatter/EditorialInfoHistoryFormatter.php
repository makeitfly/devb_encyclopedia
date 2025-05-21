<?php

namespace Drupal\devb_encyclopedia\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides basic formatting for latest editorial info field data.
 */
#[FieldFormatter(
  id: 'editorial_info_history_formatter',
  label: new TranslatableMarkup('History editorial record'),
  description: new TranslatableMarkup('Displays the history of editorial records in 1 line.'),
  field_types: [
    'editorial_info',
  ],
)]
class EditorialInfoHistoryFormatter extends EditorialInfoFormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    $options = parent::defaultSettings();
    $options['show_extended'] = FALSE;
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $form = parent::settingsForm($form, $form_state);

    $form['show_extended'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show extended information'),
      '#default_value' => $this->getSetting('show_extended'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = [];
    $setting_value = $this->getSetting('show_extended') ? $this->t('True') : $this->t('False');
    $summary[] = $this->t('Show extended information: @value', ['@value' => $setting_value]);
    return $summary;
  }

  /**
   * {@inheritDoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];
    $parts = [];
    $sortedFieldData = $this->sortEditorialInfo($items->getValue());
    $sortedFieldData = array_reverse($sortedFieldData);

    // We need the first item multiple times, so get it first.
    $first = reset($sortedFieldData);

    if ($first) {
      if (!$first['operation'] && $this->getSetting('show_extended') === FALSE) {
        $elements[0] = [
          '#type' => 'inline_template',
          '#template' => '<div class="editorial-info--author">{{ value }}</div>',
          '#context' => [
            'value' => $first['author'],
          ],
        ];

        return $elements;
      }

      // If we have more than 2 items, we only want to show the first 2.
      if (count($sortedFieldData) > 2) {
        $sortedFieldData = array_slice($sortedFieldData, 0, 2);
      }

      // Check if the first one has an operation, if not, we don't want to show.
      if (empty($sortedFieldData[0]['operation'])) {
        $sortedFieldData = [$sortedFieldData[0]];
      }

      foreach ($sortedFieldData as $item) {
        $parts[] = $this->viewElement($item);
      }

      $elements[0] = [
        '#type' => 'inline_template',
        '#template' => '<div class="editorial-info--author">{{ value }}</div>',
        '#context' => [
          'value' => implode(', ', $parts),
        ],
      ];
    }

    return $elements;
  }

  /**
   * Build the return string for a single element.
   *
   * @param array $item
   *   The item to build the string for.
   *
   * @return string
   *   The string to return.
   */
  private function viewElement(array $item): string {
    if (!$this->getSetting('show_extended')) {
      return $item['author'];
    }

    if ($item['operation']) {
      return sprintf('%s (%s, %s)', $item['author'], $item['date'], $item['operation']);
    }

    return sprintf('%s (%s)', $item['author'], $item['date']);
  }

}
