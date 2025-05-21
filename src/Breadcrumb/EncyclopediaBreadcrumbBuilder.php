<?php

namespace Drupal\devb_encyclopedia\Breadcrumb;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\devb_encyclopedia\EncyclopediaBundles;
use Drupal\devb_encyclopedia\Entity\EncyclopediaItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides a custom breadcrumb builder for encyclopedia nodes.
 */
class EncyclopediaBreadcrumbBuilder implements BreadcrumbBuilderInterface {

  use StringTranslationTrait;

  /**
   * Constructs an EncyclopediaBreadcrumbBuilder object.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
   *   The request stack.
   */
  public function __construct(protected RequestStack $requestStack) {}

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match): bool {
    // Use this breadcrumb builder for encyclopedia nodes.
    $node = $route_match->getParameter('node');
    if ($node instanceof EncyclopediaItemInterface) {
      return TRUE;
    }

    $encyclopedia_views = [
      'view.encyclopedia_overview.page_search_overview',
      'view.encyclopedia_search.page_search',
    ];
    if (in_array($route_match->getRouteName(), $encyclopedia_views, TRUE)) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function build(RouteMatchInterface $route_match): Breadcrumb {
    // Render breadcrumb trail.
    $breadcrumb = new Breadcrumb();
    $breadcrumb->addLink(Link::createFromRoute($this->t('Home'), '<front>'));

    // Add breadcrumb trail for encyclopedia items.
    $node = $route_match->getParameter('node');
    if ($node instanceof EncyclopediaItemInterface) {
      $breadcrumb->addLink(Link::createFromRoute($this->t('Encyclopedie'), 'view.encyclopedia_overview.page_search_overview'));
      foreach (EncyclopediaBundles::getBundleClasses() as $bundle => $bundle_class) {
        if ($node instanceof $bundle_class) {
          $breadcrumb->addLink(Link::createFromRoute($node->getEncyclopediaTypeLabel(), 'view.encyclopedia_overview.page_search_overview', [], ['query' => ['f[0]' => 'content_type:' . $bundle]]));
        }
      }
      $breadcrumb->addLink(Link::createFromRoute($node->label(), '<nolink>'));
    }

    // Add breadcrumb trail for encyclopedia overview pages.
    if ($route_match->getRouteName() === 'view.encyclopedia_overview.page_search_overview') {
      $breadcrumb->addLink(Link::createFromRoute($this->t('Encyclopedie'), '<nolink>'));
    }
    if ($route_match->getRouteName() === 'view.encyclopedia_search.page_search') {
      $breadcrumb->addLink(Link::createFromRoute($this->t('Encyclopedie'), 'view.encyclopedia_overview.page_search_overview'));
      $current_request = $this->requestStack->getCurrentRequest();
      if ($current_request?->get('search')) {
        $search = Xss::filter($current_request?->get('search'));
        $breadcrumb->addLink(Link::createFromRoute($this->t('Search: @keyword', ['@keyword' => $search]), '<nolink>'));
      }
    }

    // Add cache context.
    $breadcrumb->addCacheContexts(['route']);

    // Return no breadcrumb if there is only 1 item.
    if (count($breadcrumb->getLinks()) === 1) {
      $breadcrumb = new Breadcrumb();
      $breadcrumb->addCacheContexts(['route']);
      return $breadcrumb->setLinks([]);
    }

    return $breadcrumb;
  }

}
