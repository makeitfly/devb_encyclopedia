<?php

namespace Drupal\devb_encyclopedia_xbbcode\Plugin\Filter;

use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;

/**
 * Attempts to correct unclosed pic bbcode tags.
 *
 * @Filter(
 *   id = "filter_close_pic_tags",
 *   title = @Translation("Correct XBBCode pic tags"),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE,
 *   settings = {},
 * )
 */
class ClosePicTagsFilter extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode): FilterProcessResult {
    $text = preg_replace('/\[pic=\d{1,2}](?!\[)/', '$0[/pic]', $text);
    return new FilterProcessResult($text);
  }

}
