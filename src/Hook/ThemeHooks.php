<?php

declare(strict_types=1);

namespace Drupal\devb_encyclopedia\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Implements theme hooks.
 */
class ThemeHooks {

  /**
   * Implements hook_theme().
   */
  #[Hook('theme')]
  public function theme() : array {
    return [
      'quote_texts' => [
        'variables' => [
          'authors' => NULL,
          'title' => NULL,
          'description' => NULL,
          'year' => NULL,
          'link' => NULL,
          'consulted_on' => NULL,
        ],
      ],
    ];
  }

}
