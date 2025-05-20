<?php

namespace Drupal\devb_encyclopedia\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceLabelFormatter;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Basic formatter for related persons on encyclopedia entity types.
 */
#[FieldFormatter(
  id: 'related_person_formatter',
  label: new TranslatableMarkup('Related persons formatter.'),
  description: new TranslatableMarkup('Display related persons with relationship info.'),
  field_types: [
    'qualified_entity_reference',
  ],
)]
class RelatedPersonFormatter extends EntityReferenceLabelFormatter {

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
          $elements[$delta]['#prefix'] = $this->t("(is @qualification van)", ['@qualification' => $qualification]) . ' ';
        }
      }
    }

    return $elements;
  }

}
