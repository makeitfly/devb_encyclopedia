<?php

namespace Drupal\devb_encyclopedia_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Sorts Nodegoat media in correct order.
 *
 * @MigrateProcessPlugin(
 *   id = "media_sorter",
 *   handle_multiples = TRUE
 * )
 */
class MediaSorter extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $sort_id = $this->configuration['sort_id'];
    if (is_array($value)) {
      usort($value, static function ($a, $b) use ($sort_id) {
        $a_sort_value = $a['object_sub_definitions'][$sort_id]['object_sub_definition_value'] ?? 0;
        $b_sort_value = $b['object_sub_definitions'][$sort_id]['object_sub_definition_value'] ?? 0;
        return (int) $a_sort_value - (int) $b_sort_value;
      });
    }
    return $value;
  }

}
