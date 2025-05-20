<?php

namespace Drupal\devb_encyclopedia\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\image\Plugin\Field\FieldFormatter\ImageFormatter;

/**
 * Plugin implementation of the 'encyclopedia_image_formatter' formatter.
 */
#[FieldFormatter(
  id: 'encyclopedia_image_formatter',
  label: new TranslatableMarkup('Encyclopedia Image'),
  field_types: [
    'encyclopedia_image',
  ],
)]
class EncyclopediaImageFormatter extends ImageFormatter {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'show_copyright' => TRUE,
      'show_caption' => TRUE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $element = parent::settingsForm($form, $form_state);

    $element['show_copyright'] = [
      '#title' => $this->t('Show image copyright'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('show_copyright'),
    ];
    $element['show_caption'] = [
      '#title' => $this->t('Show image caption'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('show_caption'),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = [];

    $copyright_value = $this->getSetting('show_copyright') ? $this->t('True') : $this->t('False');
    $summary[] = $this->t('Show copyright: @value', ['@value' => $copyright_value]);

    $caption_value = $this->getSetting('show_caption') ? $this->t('True') : $this->t('False');
    $summary[] = $this->t('Show caption: @value', ['@value' => $caption_value]);
    return array_merge($summary, parent::settingsSummary());
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $new_elements = [];
    $elements = parent::viewElements($items, $langcode);
    $files = $this->getEntitiesToView($items, $langcode);

    // Early opt-out if the field is empty.
    if (empty($files)) {
      return $elements;
    }

    foreach ($files as $delta => $file) {
      $new_elements[$delta]['image'] = $elements[$delta];
      if (($this->getSetting('show_copyright') || $this->getSetting('show_caption')) && (!empty($items[$delta]->copyright) || !empty($items[$delta]->alt))) {
        $new_elements[$delta]['copy_and_caption'] = [
          '#type' => 'container',
          '#attributes' => [
            'class' => ['copy-caption-wrapper'],
          ],
        ];
      }
      if ($this->getSetting('show_copyright') && !empty($items[$delta]->copyright)) {
        $new_elements[$delta]['copy_and_caption']['copyright'] = [
          '#type' => 'inline_template',
          '#template' => '<span class="copyright"><em>&copy; {{ copyright|raw }}</em></span>',
          '#context' => [
            'copyright' => $items[$delta]->copyright,
          ],
        ];
      }
      if ($this->getSetting('show_copyright') && $this->getSetting('show_caption') && !empty($items[$delta]->copyright) && !empty($items[$delta]->alt)) {
        $new_elements[$delta]['copy_and_caption']['divider'] = [
          '#markup' => ' - ',
        ];
      }
      if ($this->getSetting('show_caption') && !empty($items[$delta]->alt)) {
        $new_elements[$delta]['copy_and_caption']['caption'] = [
          '#type' => 'inline_template',
          '#template' => '<span class="caption"><em>{{ caption|raw }}</em></span>',
          '#context' => [
            'caption' => $items[$delta]->alt,
          ],
        ];
      }
    }

    return $new_elements;
  }

}
