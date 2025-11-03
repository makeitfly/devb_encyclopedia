<?php

namespace Drupal\devb_encyclopedia_xbbcode\Plugin\XBBCode;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\devb_encyclopedia\Entity\EncyclopediaItemBase;
use Drupal\devb_encyclopedia\NodeGoatMapping;
use Drupal\node\NodeInterface;
use Drupal\xbbcode\Parser\Tree\TagElementInterface;
use Drupal\xbbcode\Plugin\RenderTagPlugin;
use Drupal\xbbcode\TagProcessResult;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Renders NodeGoat object BBCode tags.
 *
 * @XBBCodeTag(
 *   id = "object_link",
 *   label = @Translation("Object link"),
 *   description = @Translation("Link to another NodeGoat object"),
 *   sample = @Translation("[{{ name }}=953_621505_1]John Doe[/{{ name }}]"),
 *   name = "object",
 * )
 */
class ObjectLink extends RenderTagPlugin implements ContainerFactoryPluginInterface {

  /**
   * ObjectLink constructor.
   *
   * @param array $configuration
   *   Plugin configuration.
   * @param string $plugin_id
   *   Plugin ID.
   * @param mixed $plugin_definition
   *   Plugin definition.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Drupal entitytype manager service.
   */
  public function __construct(
    array $configuration,
    string $plugin_id,
    $plugin_definition,
    RendererInterface $renderer,
    protected EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $renderer);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('renderer'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildElement(TagElementInterface $tag): array {
    $object_id_data = [];
    $object_id = $tag->getOption();
    $content = $tag->getContent();
    // Nodegoat reference ID uses format 000_111111_2.
    // First part is data type ID, second part is Nodegoat ID, last part is
    // optional.
    preg_match('/(\d+)_(\d+)(_(\d+))?/', $object_id, $object_id_data);
    if (isset($object_id_data[1]) && isset($object_id_data[2])) {
      $data_type_id = (int) $object_id_data[1];
      $nodegoat_id = (int) $object_id_data[2];
      if (!empty($data_type_id) && !empty($nodegoat_id)) {
        // Find out which Drupal node bundle the Nodegoat data type ID
        // represents.
        if (isset(NodeGoatMapping::dataTypes()[$data_type_id])) {
          $bundle = NodeGoatMapping::dataTypes()[$data_type_id];
          // Find the Drupal node that is associated with the given Nodegoat
          // object record ID.
          $node = $this->getNodeByNodeGoatId($bundle, $nodegoat_id);
          if ($node instanceof EncyclopediaItemBase) {
            $build = [
              '#theme' => 'blurb_link',
              '#blurb_title' => $node->getTitle(),
              '#link_url' => $node->toUrl()->toString(),
              '#link_title' => $content,
              '#blurb_content' => $node->get('field_summary')->view([
                'type' => 'text_default',
                'label' => 'hidden',
              ]),
              '#blurb_id' => $node->id(),
            ];
            if ($blurb_img = $node->getMainImage('square_small')) {
              $build['#blurb_img'] = [
                '#type' => 'inline_template',
                '#template' => '<img src="{{ img_url }}" alt="{{ title }}" title="{{ title }}" />',
                '#context' => [
                  'img_url' => $blurb_img,
                  'title' => $node->getTitle(),
                ]
              ];
            }

            $content = $this->renderer->renderInIsolation($build);
            $content = preg_replace("/\r|\n/", "", $content);
          }
        }
      }
    }

    return [
      '#markup' => new TagProcessResult($content),
    ];
  }

  /**
   * Get Drupal node for NodeGoat object being referred to.
   *
   * @param string $bundle
   *   The node bundle to search.
   * @param int $nodegoat_id
   *   The NodeGoat ID to search for.
   *
   * @return \Drupal\node\NodeInterface|null
   *   The Drupal node.
   */
  protected function getNodeByNodeGoatId(string $bundle, int $nodegoat_id): ?NodeInterface {
    $node_storage = $this->entityTypeManager->getStorage('node');
    $nids = $node_storage->getQuery()
      ->condition('type', $bundle)
      ->condition('status', NodeInterface::PUBLISHED)
      ->condition('field_nodegoat_id', $nodegoat_id)
      ->range(0, 1)
      ->accessCheck(TRUE)
      ->execute();
    if (!empty($nids)) {
      $nid = reset($nids);
      $node = $node_storage->load($nid);
      if ($node instanceof NodeInterface) {
        return $node;
      }
    }
    return NULL;
  }

}
