<?php

namespace Drupal\devb_encyclopedia;

use Drupal\devb_encyclopedia\Entity\EncyclopediaArt;
use Drupal\devb_encyclopedia\Entity\EncyclopediaConcept;
use Drupal\devb_encyclopedia\Entity\EncyclopediaDocument;
use Drupal\devb_encyclopedia\Entity\EncyclopediaEvent;
use Drupal\devb_encyclopedia\Entity\EncyclopediaLocation;
use Drupal\devb_encyclopedia\Entity\EncyclopediaMonument;
use Drupal\devb_encyclopedia\Entity\EncyclopediaOrganisation;
use Drupal\devb_encyclopedia\Entity\EncyclopediaPerson;
use Drupal\devb_encyclopedia\Entity\EncyclopediaPublication;
use Drupal\devb_encyclopedia\Entity\EncyclopediaRegion;

/**
 * Provides logic for encyclopedia-specific node bundles.
 */
class EncyclopediaBundles {

  /**
   * Get encyclopedia bundle classes.
   *
   * @return array
   *   List of node bundles and associated object classes.
   */
  public static function getBundleClasses(): array {
    return self::$bundles;
  }

  /**
   * List of node bundles and associated object classes.
   *
   * @var array
   */
  protected static array $bundles = [
    'encyclopedia_concept' => EncyclopediaConcept::class,
    'encyclopedia_person' => EncyclopediaPerson::class,
    'encyclopedia_organisation' => EncyclopediaOrganisation::class,
    'encyclopedia_event' => EncyclopediaEvent::class,
    'encyclopedia_publication' => EncyclopediaPublication::class,
    'encyclopedia_document' => EncyclopediaDocument::class,
    'encyclopedia_artwork' => EncyclopediaArt::class,
    'encyclopedia_monument' => EncyclopediaMonument::class,
    'encyclopedia_location' => EncyclopediaLocation::class,
    'encyclopedia_region' => EncyclopediaRegion::class,
  ];

  /**
   * Get encyclopedia node bundle IDs.
   *
   * @return array
   *   List of encyclopedia node bundle ID / names.
   */
  public static function getBundles(): array {
    return array_keys(self::$bundles);
  }

}
