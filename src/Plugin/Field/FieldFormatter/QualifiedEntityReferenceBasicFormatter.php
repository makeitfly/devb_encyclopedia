<?php

namespace Drupal\devb_encyclopedia\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceLabelFormatter;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Basic formatter for qualified_entity_reference field type.
 */
#[FieldFormatter(
  id: 'qualified_entity_reference_basic_formatter',
  label: new TranslatableMarkup('Entity reference with qualification info.'),
  description: new TranslatableMarkup('Display the referenced entity label with qualification info.'),
  field_types: [
    'qualified_entity_reference',
  ],
)]
class QualifiedEntityReferenceBasicFormatter extends EntityReferenceLabelFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = parent::viewElements($items, $langcode);
    $values = $items->getValue();

    foreach ($elements as $delta => $entity) {
      $referenced_entity = $entity['#options']['entity'];
      if ($referenced_entity instanceof EntityPublishedInterface && !$referenced_entity->isPublished()) {
        unset($elements[$delta]);
      }
      else {
        $qualification = $values[$delta]['qualification'];
        if ($qualification) {
          $elements[$delta]['#suffix'] = ' (' . $qualification . ')';
        }
      }
    }

    return $elements;
  }

}
