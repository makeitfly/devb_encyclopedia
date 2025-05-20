<?php

namespace Drupal\devb_encyclopedia_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Processes a date in the format Y-m-d to a YMDDate field value.
 *
 * @MigrateProcessPlugin(
 *   id = "ymd_date"
 * )
 */
class YMDDate extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (is_array($value)) {
      $value = array_map(function ($item) {
        return $this->processDate($item);
      }, $value);
    }
    else {
      $value = $this->processDate($value);
    }
    return $value;
  }

  /**
   * Transform the source data.
   *
   * @param string|null $source
   *   The date in format Y-m-d.
   *
   * @return string
   *   The date in YMD field format YYYYMMDD.
   */
  protected function processDate(?string $source = NULL): string {
    if (!$source) {
      return '';
    }
    // We expect max string length in format Y-m-d. If longer, ignore.
    if (strlen($source) > 10) {
      return '';
    }
    // Extra validation: check if year does not start with a zero, and contains
    // 4 digitsm to not result in invalid dates.
    $source_year = substr($source, 0, 4);
    if (str_starts_with($source, "0") || !preg_match('@(\d{4})@', $source_year)) {
      return '';
    }

    $result = str_replace('-', '', $source);
    $result = str_pad($result, 8, '0', STR_PAD_RIGHT);
    return $result;
  }

}
