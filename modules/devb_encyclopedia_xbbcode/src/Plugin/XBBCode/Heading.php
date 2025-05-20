<?php

namespace Drupal\devb_encyclopedia_xbbcode\Plugin\XBBCode;

use Drupal\xbbcode\Parser\Tree\TagElementInterface;
use Drupal\xbbcode\Plugin\TagPluginBase;
use Drupal\xbbcode\TagProcessResult;

/**
 * Renders NodeGoat heading BBCode tags.
 *
 * @XBBCodeTag(
 *   id = "heading",
 *   label = @Translation("Heading"),
 *   description = @Translation("Formats text headings."),
 *   sample = @Translation("[{{ name }}=1]Heading[/{{ name }}]"),
 *   name = "h",
 * )
 */
class Heading extends TagPluginBase {

  /**
   * {@inheritdoc}
   */
  public function doProcess(TagElementInterface $tag): TagProcessResult {
    $heading_num = (int) $tag->getOption();
    $heading_num++;
    $content = $tag->getContent();
    return new TagProcessResult("<h{$heading_num}>{$content}</h{$heading_num}>");
  }

}
