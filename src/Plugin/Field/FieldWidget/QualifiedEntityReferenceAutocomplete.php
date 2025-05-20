<?php

namespace Drupal\devb_encyclopedia\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Attribute\FieldWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\EntityReferenceAutocompleteWidget;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Widget for selecting entity reference and providing qualification info.
 */
#[FieldWidget(
  id: 'qualified_entity_reference_autocomplete',
  label: new TranslatableMarkup('Entity reference & qualification info'),
  description: new TranslatableMarkup('An autocomplete text field with an associated qualification.'),
  field_types: ['qualified_entity_reference'],
)]
class QualifiedEntityReferenceAutocomplete extends EntityReferenceAutocompleteWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    $element += parent::formElement($items, $delta, $element, $form, $form_state);

    $element['qualification'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Qualification'),
      '#description' => $this->t('The qualification for the entity reference relationship.'),
      '#default_value' => $items[$delta]->qualification ?: '',
      '#weight' => 5,
    ];

    // Render as a fieldset.
    $element += [
      '#type' => 'details',
      '#collapsible' => TRUE,
      '#open' => TRUE,
    ];

    return $element;
  }

}
