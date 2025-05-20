<?php

namespace Drupal\devb_encyclopedia\Entity;

/**
 * Common interface for DEVB encyclopedia items.
 */
interface EncyclopediaItemInterface {

  /**
   * Get the label for the type of encyclopedia item this node represents.
   *
   * @return string
   *   Label for the encyclopedia item type.
   */
  public function getEncyclopediaTypeLabel(): string;

}
