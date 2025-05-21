<?php

namespace Drupal\devb_encyclopedia\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Attribute\FieldWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Plugin implementation of the 'archive_widget' widget.
 */
#[FieldWidget(
  id: 'archive_widget',
  label: new TranslatableMarkup('Archive widget'),
  field_types: ['archive'],
)]
class ArchiveWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    $element += [
      '#type' => 'details',
      '#collapsible' => FALSE,
      '#open' => TRUE,
    ];

    $element['repository'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Repository'),
      '#default_value' => $items[$delta]->repository ?: '',
      '#required' => FALSE,
    ];

    $element['inventory'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Inventory'),
      '#default_value' => $items[$delta]->inventory ?: '',
      '#required' => FALSE,
    ];

    $element['reference'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Reference'),
      '#default_value' => $items[$delta]->reference ?: '',
      '#required' => FALSE,
    ];

    return $element;
  }

}
