<?php

declare(strict_types=1);

namespace Drupal\devb_encyclopedia_migrate\Plugin\migrate_plus\authentication;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\key\KeyRepositoryInterface;
use Drupal\migrate_plus\AuthenticationPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides NodeGoat OAuth2 authentication for the HTTP resource.
 *
 * @Authentication(
 *   id = "nodegoat_oauth2",
 *   title = @Translation("NodeGoat OAuth2")
 * )
 */
class NodeGoatOAuth2 extends AuthenticationPluginBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs a NodeGoatOAuth2 object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\key\KeyRepositoryInterface $keyRepository
   *   The key repository service.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    protected KeyRepositoryInterface $keyRepository,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('key.repository')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getAuthenticationOptions($url): array {
    return [
      'headers' => [
        'Authorization' => 'Bearer ' . $this->keyRepository->getKey('nodegoat_oauth2_access_token')?->getKeyValue(),
      ],
    ];
  }

}
