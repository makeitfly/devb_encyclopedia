<?php

namespace Drupal\devb_encyclopedia_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Processes a string from NodeGoat.
 *
 * @MigrateProcessPlugin(
 *   id = "string_cleanup"
 * )
 */
class StringCleanup extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (is_array($value)) {
      $value = array_map(function ($item) {
        return $this->processText($item);
      }, $value);
    }
    else {
      $value = $this->processText($value);
    }
    return $value;
  }

  /**
   * Cleans up the source text.
   *
   * @param string|null $source
   *   The source text.
   *
   * @return string
   *   The text to be imported in Drupal
   */
  protected function processText(?string $source = NULL): string {
    if (!empty($source)) {
      $text = strip_tags($source);
      $text = trim($text);
      return substr($text, 0, 255);
    }
    return '';
  }

}
