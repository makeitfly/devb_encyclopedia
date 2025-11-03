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

  /**
   * Get the main image file.
   *
   * @param string|null $image_style
   *   The image style to render image as.
   *
   * @return string|null
   */
  public function getMainImage(?string $image_style = NULL): ?string;

}
