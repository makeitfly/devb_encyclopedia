<?php

namespace Drupal\devb_encyclopedia_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Fixes file extension for media.
 *
 * @MigrateProcessPlugin(
 *   id = "file_extension_fix",
 * )
 */
class FileExtensionFix extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Check if there is a file extension dot at the end of the filename.
    $dot1 = substr($value, -4, 1);
    $dot2 = substr($value, -5, 1);
    // If not, append the default file extension.
    if ($dot1 !== '.' && $dot2 !== '.') {
      $value .= '.jpg';
    }
    return $value;
  }

}
