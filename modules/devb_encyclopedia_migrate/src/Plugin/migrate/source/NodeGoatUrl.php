<?php

declare(strict_types=1);

namespace Drupal\devb_encyclopedia_migrate\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\migrate_plus\Plugin\migrate\source\Url;

/**
 * Source plugin for retrieving data via NodeGoat URLs.
 *
 * @MigrateSource(
 *   id = "nodegoat_url"
 * )
 */
class NodeGoatUrl extends Url {

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row): bool {
    $return = parent::prepareRow($row);
    // Get the full object subs, but without the object sub ID as key, since
    // that is dynamic, and thus cannot be used in our migration definition.
    $raw_data = $row->getSourceProperty('raw');
    $object_subs = [];
    if (is_array($raw_data)) {
      $object_subs_raw = $raw_data['object_subs'];
      if (!empty($object_subs_raw)) {
        foreach ($object_subs_raw as $object_sub_raw_data) {
          $object_sub_details_id = $object_sub_raw_data['object_sub']['object_sub_details_id'];
          $object_subs[$object_sub_details_id][] = $object_sub_raw_data;
        }
      }
    }
    $row->setSourceProperty('object_subs', $object_subs);
    return $return;
  }

}
