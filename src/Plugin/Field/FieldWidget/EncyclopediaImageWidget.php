<?php

namespace Drupal\devb_encyclopedia\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Attribute\FieldWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\image_widget_crop\Plugin\Field\FieldWidget\ImageCropWidget;

/**
 * Plugin implementation of the 'encyclopedia_image_widget' widget.
 */
#[FieldWidget(
  id: 'encyclopedia_image_widget',
  label: new TranslatableMarkup('Encyclopedia Image'),
  field_types: ['encyclopedia_image'],
)]
class EncyclopediaImageWidget extends ImageCropWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    /** @var \Drupal\Core\Entity\Display\EntityFormDisplayInterface $formDisplay */
    $formDisplay = $form_state->getStorage()['form_display'];
    $formMode = $formDisplay->getMode();

    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $element['copyright'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Copyright'),
      '#default_value' => $items[$delta]->copyright ?? '',
      '#required' => FALSE,
      '#attributes' => ['readonly' => $formMode === 'cropping'],
      '#wrapper_attributes' => $formMode === 'cropping' ? ['class' => ['hidden']] : [],
    ];

    $element['orientation'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Orientation'),
      '#default_value' => $items[$delta]->orientation ?? '',
      '#required' => FALSE,
      '#attributes' => ['readonly' => $formMode === 'cropping'],
      '#wrapper_attributes' => $formMode === 'cropping' ? ['class' => ['hidden']] : [],
    ];
    $element['main_image'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Main image?'),
      '#default_value' => $items[$delta]->main_image ?? FALSE,
      '#required' => FALSE,
      '#attributes' => ['readonly' => $formMode === 'cropping'],
      '#wrapper_attributes' => $formMode === 'cropping' ? ['class' => ['hidden']] : [],
    ];
    $element['order_text'] = [
      '#type' => 'number',
      '#title' => $this->t('Order in text'),
      '#default_value' => $items[$delta]->order_text ?? NULL,
      '#required' => FALSE,
      '#attributes' => ['readonly' => $formMode === 'cropping'],
      '#wrapper_attributes' => $formMode === 'cropping' ? ['class' => ['hidden']] : [],
    ];
    $element['order_gallery'] = [
      '#type' => 'number',
      '#title' => $this->t('Order in gallery'),
      '#default_value' => $items[$delta]->order_gallery ?? NULL,
      '#required' => FALSE,
      '#attributes' => ['readonly' => $formMode === 'cropping'],
      '#wrapper_attributes' => $formMode === 'cropping' ? ['class' => ['hidden']] : [],
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state): array {
    foreach ($values as $delta => $value) {
      if ($value['order_text'] === "") {
        $values[$delta]['order_text'] = NULL;
      }
      if ($value['order_gallery'] === "") {
        $values[$delta]['order_gallery'] = NULL;
      }
    }
    return parent::massageFormValues($values, $form, $form_state);
  }

  /**
   * {@inheritDoc}
   */
  public static function process($element, FormStateInterface $form_state, $form): array {
    /** @var \Drupal\Core\Entity\Display\EntityFormDisplayInterface $formDisplay */
    $formDisplay = $form_state->getStorage()['form_display'];
    $formMode = $formDisplay->getMode();

    $element = parent::process($element, $form_state, $form);

    // Remove some fields from the form if we are in cropping mode.
    $element['alt']['#attributes'] = ['readonly' => $formMode === 'cropping'];
    $element['title']['#attributes'] = ['readonly' => $formMode === 'cropping'];

    if ($formMode === 'cropping') {
      $element['alt']['#wrapper_attributes'] = ['class' => ['hidden']];
      $element['title']['#wrapper_attributes'] = ['class' => ['hidden']];
    }

    $element['remove_button']['#access'] = $formMode !== 'cropping';
    $element['upload_button']['#access'] = $formMode !== 'cropping';
    $element['_weight']['#access'] = $formMode !== 'cropping';
    $element['upload']['#access'] = $formMode !== 'cropping';

    return $element;
  }

}
