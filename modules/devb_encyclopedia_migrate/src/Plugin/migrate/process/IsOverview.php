<?php

namespace Drupal\devb_encyclopedia_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Processes whether a record is an overview or not.
 *
 * @MigrateProcessPlugin(
 *   id = "is_overview"
 * )
 */
class IsOverview extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $value = trim($value);
    $value = strip_tags($value);
    return empty($value) ? 0 : 1;
  }

}
