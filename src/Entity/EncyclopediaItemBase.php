<?php

namespace Drupal\devb_encyclopedia\Entity;

use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;

/**
 * Base class for encyclopedia item nodes.
 */
class EncyclopediaItemBase extends Node implements EncyclopediaItemInterface {

  /**
   * {@inheritdoc}
   */
  public function getEncyclopediaTypeLabel(): string {
    $bundle = $this->bundle();
    $node_type = NodeType::load($bundle);
    return $node_type->label();
  }

}
