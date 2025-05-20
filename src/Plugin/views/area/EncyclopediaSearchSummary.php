<?php

namespace Drupal\devb_encyclopedia\Plugin\views\area;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Link;
use Drupal\views\Attribute\ViewsArea;
use Drupal\views\Plugin\views\area\AreaPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Views area handler to display Encyclopedia search result summary.
 */
#[ViewsArea("devb_encyclopedia_search_summary")]
class EncyclopediaSearchSummary extends AreaPluginBase {

  /**
   * The current request object.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected Request $currentRequest;

  /**
   * Constructs a new EncyclopediaSearchSummary object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RequestStack $request_stack) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentRequest = $request_stack->getCurrentRequest();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('request_stack')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $this->view->get_total_rows = TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function render($empty = FALSE): array {
    $total = $this->view->total_rows ?? count($this->view->result);
    $search = $this->currentRequest->get('search') ? Xss::filter($this->currentRequest->get('search')) : '';
    return [
      '#type' => 'inline_template',
      '#template' => '<p class="search-summary">{{ result_summary }}{{ reset_link }}</p>',
      '#context' => [
        'result_summary' => $this->t('@count results found for "@query"', [
          '@count' => $total,
          '@query' => $search,
        ]),
        'reset_link' => Link::createFromRoute($this->t('wis zoekterm'), 'view.encyclopedia_overview.page_search_overview')->toString(),
      ],
    ];
  }

}
