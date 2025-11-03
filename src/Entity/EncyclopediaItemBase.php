<?php

namespace Drupal\devb_encyclopedia\Entity;

use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;
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

  /**
   * {@inheritdoc}
   */
  public function getMainImage(?string $image_style = NULL): ?string {
    $images = $this->get('field_images')->getValue();
    foreach ($images as $image) {
      if ($image['main_image'] === '1') {
        $fid = $image['target_id'];
        if ($fid) {
          $imageUrl = File::load($fid)?->getFileUri();
          if ($image_style) {
            $imageUrl = ImageStyle::load($image_style)?->buildUrl($imageUrl);
          }
          return $imageUrl;
        }
      }
    }
    return NULL;
  }

}
