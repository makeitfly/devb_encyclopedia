<?php

/**
 * @file
 * Provides token-related functionality for DEVB Encyclopedia module.
 */

declare(strict_types=1);

use Drupal\Core\Render\BubbleableMetadata;
use Drupal\devb_encyclopedia\Plugin\Field\FieldType\EncyclopediaImage;
use Drupal\file\FileInterface;
use Drupal\node\NodeInterface;

/**
 * Implements hook_token_info().
 */
function devb_encyclopedia_token_info(): array {
  $node_tokens = [];
  $node_tokens['main-image-url'] = [
    'name' => t('Url for main image'),
    'description' => t('The URL for the main image'),
    'needs-data' => 'node',
  ];

  return [
    'tokens' => ['node' => $node_tokens],
  ];
}

/**
 * Implements hook_tokens().
 */
function devb_encyclopedia_tokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata): array {
  $replacements = [];

  if ($type === 'node' && !empty($data['node'])) {
    $node = $data['node'];

    foreach ($tokens as $name => $original) {
      if ($name === 'main-image-url') {
        $main_image_url = '';
        if ($node instanceof NodeInterface && $node->hasField('field_images') && !$node->get('field_images')->isEmpty()) {
          $node_images = $node->get('field_images');
          foreach ($node_images as $delta => $node_image) {
            if ($node_image instanceof EncyclopediaImage && $node_image->isMainImage()) {
              $main_image_value = $node->get('field_images')
                ->get($delta)
                ->getValue();
              if ($main_image_value['target_id']) {
                $main_image_file = \Drupal::entityTypeManager()
                  ->getStorage('file')
                  ->load($main_image_value['target_id']);
                if ($main_image_file instanceof FileInterface) {
                  $main_image_url = $main_image_file->createFileUrl(FALSE);
                }
              }
              break;
            }
          }
        }
        $replacements[$original] = $main_image_url;
        $bubbleable_metadata->addCacheableDependency($node);
      }
    }
  }

  return $replacements;
}
