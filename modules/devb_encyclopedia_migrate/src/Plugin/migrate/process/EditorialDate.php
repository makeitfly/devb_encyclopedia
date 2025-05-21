<?php

namespace Drupal\devb_encyclopedia_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Processes an editorial date from NodeGoat.
 *
 * @MigrateProcessPlugin(
 *   id = "editorial_date",
 *   handle_multiples = TRUE
 * )
 */
class EditorialDate extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (is_array($value)) {
      // Concatenate start and end date, unless they are equal.
      $start = $value[0];
      $end = $value[1];
      return ($start === $end) ? $start : $start . ' - ' . $end;
    }
    return $value;
  }

}
