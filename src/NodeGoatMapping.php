<?php

namespace Drupal\devb_encyclopedia;

/**
 * Provides NodeGoat mapping functionality.
 */
class NodeGoatMapping {

  /**
   * Get mapping for NodeGoat data types with Drupal node bundles.
   *
   * @todo use ThirdPartySettings API to configure NodeGoat data type ID on
   * Node bundle config entities dynamically.
   *
   * @return array
   *   List keyed by NodeGoat data type ID and corresponding Drupal node bundle.
   */
  public static function dataTypes(): array {
    return [
      953 => 'encyclopedia_person',
      959 => 'encyclopedia_organisation',
      962 => 'encyclopedia_publication',
      965 => 'encyclopedia_event',
      967 => 'encyclopedia_monument',
      1064 => 'encyclopedia_artwork',
      966 => 'encyclopedia_location',
      1037 => 'encyclopedia_region',
      987 => 'encyclopedia_concept',
      1066 => 'encyclopedia_document',
    ];
  }

}
