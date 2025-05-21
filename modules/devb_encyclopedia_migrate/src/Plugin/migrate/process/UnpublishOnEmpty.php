<?php

namespace Drupal\devb_encyclopedia_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Set status field based on emptiness of a given source field.
 *
 * @MigrateProcessPlugin(
 *   id = "unpublish_on_empty"
 * )
 */
class UnpublishOnEmpty extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    return (empty($value)) ? 0 : 1;
  }

}
