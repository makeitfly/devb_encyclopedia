<?php

namespace Drupal\devb_encyclopedia\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;

/**
 * Provides basic functionality for editorial info field formatting.
 */
abstract class EditorialInfoFormatterBase extends FormatterBase {

  /**
   * Sort field data array based on date.
   *
   * @param array $field_data
   *   The field data.
   *
   * @return array
   *   The sorted field data, sorted on publication date.
   */
  public function sortEditorialInfo(array $field_data): array {
    // Sort items in array with most recent on top.
    uasort($field_data, static function (array $a, array $b) {
      $a_date = (int) $a['date'] ?? 0;
      $b_date = (int) $b['date'] ?? 0;
      return $a_date - $b_date;
    });

    if (!empty($field_data)) {
      return $field_data;
    }
    return [];
  }

}
