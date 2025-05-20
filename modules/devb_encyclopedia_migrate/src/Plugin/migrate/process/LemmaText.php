<?php

namespace Drupal\devb_encyclopedia_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Processes a lemma text from NodeGoat.
 *
 * @MigrateProcessPlugin(
 *   id = "lemma_text"
 * )
 */
class LemmaText extends ProcessPluginBase {

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
   * Transform the source text.
   *
   * @param string|null $source
   *   The lemma source text.
   *
   * @return string
   *   The lemma text for Drupal.
   */
  protected function processText(?string $source = NULL): string {
    if (!empty($source)) {
      return '<p>' . $source . '</p>';
    }
    return '';
  }

}
