<?php

namespace Drupal\devb_encyclopedia_xbbcode\Plugin\XBBCode;

use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Renderer;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\devb_encyclopedia\Plugin\Field\FieldType\EncyclopediaImage;
use Drupal\node\NodeInterface;
use Drupal\xbbcode\Parser\Tree\TagElementInterface;
use Drupal\xbbcode\Plugin\TagPluginBase;
use Drupal\xbbcode\TagProcessResult;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Renders NodeGoat inline image BBCode tags.
 *
 * @XBBCodeTag(
 *   id = "inline_image",
 *   label = @Translation("Inline image"),
 *   description = @Translation("Formats inline images."),
 *   sample = @Translation("[{{ name }}=1][/{{ name }}]"),
 *   name = "pic",
 * )
 */
class InlineImage extends TagPluginBase implements ContainerFactoryPluginInterface {

  /**
   * Creates a InlineImage instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The route match service.
   * @param \Drupal\Core\Render\Renderer $renderer
   *   The renderer service.
   * @param \Drupal\Core\File\FileUrlGeneratorInterface $fileUrlGenerator
   *   The file URL generator service.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    protected RouteMatchInterface $routeMatch,
    protected Renderer $renderer,
    protected FileUrlGeneratorInterface $fileUrlGenerator,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match'),
      $container->get('renderer'),
      $container->get('file_url_generator'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function doProcess(TagElementInterface $tag): TagProcessResult {
    $output = '';
    $pic_num = (int) $tag->getOption();
    $node = $this->routeMatch->getParameter('node');
    if ($node instanceof NodeInterface) {
      $image_delta = NULL;
      $node_images = $node->get('field_images');
      foreach ($node_images as $delta => $node_image) {
        if ($node_image instanceof EncyclopediaImage) {
          if ($node_image->getOrderInText() == $pic_num) {
            $image_delta = $delta;
            break;
          }
        }
      }
      if (isset($image_delta)) {
        $imageValue = $node->get('field_images')->get($delta)->getValue();
        $imageFile = $node->get('field_images')->get($delta)->get('entity')->getValue();
        $imageUrl = $this->fileUrlGenerator->generateAbsoluteString($imageFile->getFileUri());

        $output = [
          '#theme' => 'devb_inline_image',
          '#image' => [
            '#theme' => 'image_style',
            '#style_name' => 'lemma_image',
            '#uri' => $imageFile->getFileUri(),
            '#alt' => $imageValue['alt'],
            '#title' => $imageValue['title'],
          ],
          '#url' => $imageUrl,
          '#alt' => $imageValue['alt'],
          '#title' => $imageValue['title'],
        ];

        $output = $this->renderer->render($output);
      }
    }

    return new TagProcessResult($output);
  }

}
