<?php

namespace Drupal\devb_encyclopedia\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Attribute\FieldWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Plugin implementation of the 'editorial_info_default' widget.
 */
#[FieldWidget(
  id: 'editorial_info_default',
  label: new TranslatableMarkup('Default editorial info field widget'),
  field_types: ['editorial_info'],
)]
class EditorialInfoDefaultWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    $element += [
      '#type' => 'details',
      '#collapsible' => FALSE,
      '#open' => TRUE,
    ];

    $element['date'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Date'),
      '#default_value' => $items[$delta]->date ?: '',
      '#size' => 20,
      '#required' => FALSE,
    ];

    $element['author'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Author'),
      '#default_value' => $items[$delta]->author ?: '',
      '#required' => FALSE,
    ];

    $element['operation'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Operation'),
      '#default_value' => $items[$delta]->operation ?: '',
      '#required' => FALSE,
    ];

    $element['link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link'),
      '#default_value' => $items[$delta]->link ?: '',
      '#required' => FALSE,
    ];

    $element['description'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Description'),
      '#default_value' => $items[$delta]->description ?: '',
      '#required' => FALSE,
    ];

    $element['can_be_translated'] = [
      '#type' => 'hidden',
      '#default_value' => $items[$delta]->can_be_translated ?: 0,
    ];

    $element['translated'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Translated'),
      '#default_value' => $items[$delta]->translated ?: 0,
      '#required' => FALSE,
    ];

    return $element;
  }

}
