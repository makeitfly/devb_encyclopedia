<?php

declare(strict_types=1);

namespace Drupal\devb_encyclopedia\Service;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;

/**
 * Provides a service for generating texts.
 */
class ReferenceTextsGenerator implements TrustedCallbackInterface {

  use StringTranslationTrait;

  public const BASE_URL = 'https://devb.be';

  /**
   * Constructs a ReferenceTextGenerator object.
   *
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The Drupal DateTime.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager service.
   */
  public function __construct(
    protected TimeInterface $time,
    protected EntityTypeManagerInterface $entityTypeManager,
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks(): array {
    return [
      'generateTexts',
    ];
  }

  /**
   * Generates text for a given node.
   *
   * @param string $nodeId
   *   The given node ID.
   *
   * @return array
   *   The generated texts.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function generateTexts(string $nodeId): array {
    /** @var \Drupal\node\NodeInterface $node */
    $node = $this->entityTypeManager
      ->getStorage('node')
      ->load($nodeId);
    // Format the author values.
    if ($authorFieldValues = $node->get('field_editorial_info')->getValue()) {
      // Sort the authors by date, newest first.
      uasort($authorFieldValues, static function (array $a, array $b) {
        return (int) $a['date'] - (int) $b['date'];
      });
      $authorFieldValues = array_reverse($authorFieldValues);
      // If we have more than 2 items, we only want to show the first 2.
      if (count($authorFieldValues) > 2) {
        $authorFieldValues = array_slice($authorFieldValues, 0, 2);
      }
      // Check if the first one has an operation, if not, we don't want to show.
      $first = reset($authorFieldValues);
      if (empty($first['operation'])) {
        $authorFieldValues = [$authorFieldValues[0]];
      }
      $authorValues = array_map(static function ($author) {
        return $author ? $author['author'] . ' (' . $author['date'] . ')' : FALSE;
      }, $authorFieldValues);

      $authors = implode(', ', $authorValues);

      // Get the latest edit date.
      $authorDates = array_map(static function ($author) {
        return $author['date'];
      }, $authorFieldValues);
      $latestEdit = max($authorDates);
    }

    // Get the persistent identifier link.
    $url = Url::fromRoute('devb_encyclopedia.permalink', [
      'advn_id' => $node->get('field_advn_id')->getString(),
    ]);

    // Filter out language prefix.
    $link = self::BASE_URL . $url->setAbsolute(FALSE)->toString();
    $linkNoLanguage = preg_replace('/https:\/\/devb\.be\/(nl|en|fr)\//', self::BASE_URL . '/', $link);

    // Return the generated texts.
    return [
      '#theme' => 'quote_texts',
      '#authors' => $authors ?? NULL,
      '#title' => $node->getTitle(),
      '#description' => $this->t('Encyclopedie van de Vlaamse beweging'),
      '#year' => $latestEdit ?? NULL,
      '#link' => $linkNoLanguage,
    ];
  }

}
