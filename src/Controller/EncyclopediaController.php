<?php

namespace Drupal\devb_encyclopedia\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller for DEVB Encyclopedia routes.
 */
class EncyclopediaController extends ControllerBase {

  /**
   * Redirect a permalink URL with ADVN ID to current node path.
   *
   * @param string $advn_id
   *   The permanent ADVN ID used in the permalink URL.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   The redirect response to the current object URL.
   */
  public function permalink(string $advn_id): RedirectResponse {
    $node = $this->getEncyclopediaNodeByAdvnId($advn_id);
    if ($node instanceof NodeInterface) {
      return $this->redirect('entity.node.canonical', ['node' => $node->id()]);
    }
    throw new NotFoundHttpException();
  }

  protected function getEncyclopediaNodeByAdvnId(string $advn_id): ?NodeInterface {
    $node_storage = $this->entityTypeManager()->getStorage('node');
    $nids = $node_storage->getQuery()
      ->condition('status', 1)
      ->condition('field_advn_id', $advn_id)
      ->range(0, 1)
      ->accessCheck()
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
