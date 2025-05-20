<?php

namespace Drupal\devb_encyclopedia\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Plugin for the 'Blazy' formatter for encyclopedia image galleries.
 */
#[FieldFormatter(
  id: 'encyclopedia_blazy_image',
  label: new TranslatableMarkup('Encyclopedia Blazy image'),
  description: new TranslatableMarkup('Display the encyclopedia images via Blazy.'),
  field_types: [
    'encyclopedia_image',
  ],
)]
class EncyclopediaBlazyImageFormatter extends EncyclopediaBlazyImageFormatterBase {}
