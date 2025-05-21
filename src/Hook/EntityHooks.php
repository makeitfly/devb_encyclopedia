<?php

declare(strict_types=1);

namespace Drupal\devb_encyclopedia\Hook;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Block\BlockManagerInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\Display\EntityFormDisplayInterface;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\Markup;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\devb_encyclopedia\EncyclopediaBundles;
use Drupal\devb_encyclopedia\Entity\EncyclopediaItemInterface;
use Drupal\node\NodeInterface;

/**
 * Implements entity hooks.
 */
class EntityHooks {

  use StringTranslationTrait;

  /**
   * Constructs an EntityHooks instance.
   *
   * @param \Drupal\Core\Block\BlockManagerInterface $blockManager
   *   The block plugin manager.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   * @param \Drupal\Core\Session\AccountProxyInterface $currentUser
   *   The current user.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(
    protected BlockManagerInterface $blockManager,
    protected RendererInterface $renderer,
    protected AccountProxyInterface $currentUser,
    protected EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Implements hook_entity_extra_field_info().
   */
  #[Hook('entity_extra_field_info')]
  public function entityExtraFieldInfo(): array {
    $extra = [];
    foreach (EncyclopediaBundles::getBundles() as $bundle) {
      $extra['node'][$bundle]['display']['label'] = [
        'label' => $this->t('Label'),
        'weight' => 0,
        'visible' => FALSE,
      ];

      $extra['node'][$bundle]['display']['bundle_label'] = [
        'label' => $this->t('Encyclopedia item type'),
        'weight' => 0,
        'visible' => FALSE,
      ];

      $extra['node'][$bundle]['display']['encyclopedia_node_media'] = [
        'label' => $this->t('Encyclopedia node media embed'),
        'weight' => 0,
        'visible' => FALSE,
      ];

      $extra['node'][$bundle]['display']['encyclopedia_node_update_info'] = [
        'label' => $this->t('Record update information'),
        'weight' => 0,
        'visible' => FALSE,
      ];

      $extra['node'][$bundle]['display']['encyclopedia_node_suggestion_webform'] = [
        'label' => $this->t('Suggestion webform'),
        'weight' => 0,
        'visible' => FALSE,
      ];

      $extra['node'][$bundle]['display']['encyclopedia_node_editorial_history'] = [
        'label' => $this->t('Editorial history'),
        'weight' => 0,
        'visible' => FALSE,
      ];

      $extra['node'][$bundle]['display']['encyclopedia_node_tocbot'] = [
        'label' => $this->t('Table of contents'),
        'weight' => 0,
        'visible' => FALSE,
      ];

      $extra['node'][$bundle]['display']['encyclopedia_node_quote'] = [
        'label' => $this->t('Quote'),
        'weight' => 0,
        'visible' => FALSE,
      ];
    }
    return $extra;
  }

  /**
   * Implements hook_entity_view().
   */
  #[Hook('entity_view')]
  public function entityView(array &$build, EntityInterface $entity, EntityViewDisplayInterface $display, $view_mode): void {
    if (!$entity instanceof EncyclopediaItemInterface) {
      return;
    }

    if ($display->getComponent('label')) {
      $build['label'] = [
        '#type' => 'inline_template',
        '#template' => '<h2>{{ label }}</h2>',
        '#context' => [
          'label' => $entity->label(),
        ],
      ];
    }

    if ($display->getComponent('bundle_label')) {
      $build['bundle_label'] = [
        '#type' => 'inline_template',
        '#template' => '<div class="bundle">{{ bundle }}</div>',
        '#context' => [
          'bundle' => $entity->getEncyclopediaTypeLabel(),
        ],
      ];
    }

    if ($display->getComponent('encyclopedia_node_media')) {
      $build['encyclopedia_node_media'] = [
        '#type' => 'view',
        '#name' => 'encyclopedia_node_media',
        '#display_id' => 'embed_node_media',
        '#arguments' => [$entity->id()],
      ];
    }

    if ($display->getComponent('encyclopedia_node_update_info')) {
      if ($entity instanceof ContentEntityInterface && $entity->hasField('field_editorial_info') && !$entity->get('field_editorial_info')->isEmpty()) {
        $editorial_info = $entity->get('field_editorial_info')->getValue();
        if (count($editorial_info) === 1) {
          $latest_editorial_info = reset($editorial_info);
          if (!empty($latest_editorial_info['date']) && $latest_editorial_info['date'] !== "0") {
            $build['encyclopedia_node_update_info'] = [
              '#type' => 'inline_template',
              '#template' => '<p class="authoring-info">{{ label }} {{ last_edit_date }}</p>',
              '#context' => [
                'label' => $this->t('Geplaatst op'),
                'last_edit_date' => $latest_editorial_info['date'],
              ],
            ];
          }
        }
        else {
          $latest_editorial_info = reset($editorial_info);
          $oldest_editorial_info = end($editorial_info);
          if (!empty($latest_editorial_info['date']) && $latest_editorial_info['date'] !== "0") {
            $build['encyclopedia_node_update_info'] = [
              '#type' => 'inline_template',
              '#template' => '<p class="authoring-info">{{ label_first }} {{ first_edit_date }}, {{ label_last }} {{ last_edit_date }}</p>',
              '#context' => [
                'label_first' => $this->t('Geplaatst op'),
                'label_last' => $this->t('aangepast op'),
                'first_edit_date' => $oldest_editorial_info['date'],
                'last_edit_date' => $latest_editorial_info['date'],
              ],
            ];
          }
        }
      }
    }

    if ($display->getComponent('encyclopedia_node_suggestion_webform')) {
      $build['encyclopedia_node_suggestion_webform'] = [
        '#type' => 'link',
        '#title' => Markup::create('<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M17.7071 2.29289C17.3166 1.90237 16.6834 1.90237 16.2929 2.29289L14 4.58579L12.7071 3.29289C12.3166 2.90237 11.6834 2.90237 11.2929 3.29289L5.29289 9.29289C4.90237 9.68342 4.90237 10.3166 5.29289 10.7071C5.68342 11.0976 6.31658 11.0976 6.70711 10.7071L12 5.41421L12.5858 6L10.2929 8.29289L3.29289 15.2929C3.10536 15.4804 3 15.7348 3 16V20C3 20.5523 3.44772 21 4 21H8C8.26522 21 8.51957 20.8946 8.70711 20.7071L15.7071 13.7071L21.7071 7.70711C22.0976 7.31658 22.0976 6.68342 21.7071 6.29289L17.7071 2.29289ZM14.7071 6.70711L17 4.41421L19.5858 7L15 11.5858L12.4142 9L14.7071 6.70711ZM5 16.4142L11 10.4142L13.5858 13L7.58579 19H5V16.4142Z" fill="#28111B"/>
</svg>' . '<span>' . $this->t('Suggestie') . '</span>'),
        '#url' => Url::fromRoute('entity.webform.canonical', ['webform' => 'encyclopedie_suggestie'], [
          'query' => [
            'source_entity_type' => 'node',
            'source_entity_id' => $entity->id(),
          ],
        ]),
        '#attributes' => ['class' => ['suggestion btn btn--invert']],
      ];
    }

    if ($display->getComponent('encyclopedia_node_editorial_history')) {
      if ($entity instanceof ContentEntityInterface && $entity->hasField('field_editorial_info') && !$entity->get('field_editorial_info')->isEmpty()) {
        $build['encyclopedia_node_editorial_history'] = $entity->get('field_editorial_info')->view([
          'type' => 'editorial_info_basic_formatter',
          'label' => 'hidden',
        ]);
      }
    }

    if ($display->getComponent('encyclopedia_node_tocbot')) {
      $build['encyclopedia_node_tocbot'] = $this->renderTocbot();
    }

    if ($display->getComponent('encyclopedia_node_quote')) {
      $build['encyclopedia_node_quote'] = [
        '#lazy_builder' => [
          'devb_encyclopedia.reference_texts_generator::generateTexts',
          [$entity->id()],
        ],
        '#create_placeholder' => TRUE,
      ];
    }
  }

  /**
   * Implements hook_entity_bundle_info_alter().
   */
  #[Hook('entity_bundle_info_alter')]
  public function entityBundleInfoAlter(&$bundles): void {
    foreach (EncyclopediaBundles::getBundleClasses() as $bundle_name => $class) {
      if (isset($bundles['node'][$bundle_name])) {
        $bundles['node'][$bundle_name]['class'] = $class;
      }
    }
  }

  /**
   * Implements hook_entity_view_mode_alter().
   */
  #[Hook('entity_view_mode_alter')]
  public function entityViewModeAlter(&$view_mode, EntityInterface $entity): void {
    $all_results_portrait = TRUE;
    if (
      $entity instanceof NodeInterface &&
      $view_mode === 'search_result' &&
      $entity->hasField('field_is_overview') &&
      (!$entity->get('field_is_overview')->value || $all_results_portrait)
    ) {
      $view_mode = 'search_result_vertical';
    }
  }

  /**
   * Implements hook_entity_form_display_alter().
   */
  #[Hook('entity_form_display_alter')]
  public function entityFormDisplayAlter(EntityFormDisplayInterface &$form_display, array $context): void {
    $roles = $this->currentUser->getRoles();
    $isEditor = in_array('editor', $roles, TRUE)
      || in_array('super_editor', $roles, TRUE);

    if ($isEditor
      && $context['entity_type'] === 'node'
      && str_contains($context['bundle'], 'encyclopedia_')) {
      $storage = $this->entityTypeManager->getStorage('entity_form_display');
      /** @var \Drupal\Core\Entity\Display\EntityFormDisplayInterface $differentDisplay */
      $differentDisplay = $storage->load("{$context['entity_type']}.{$context['bundle']}.cropping");

      if ($differentDisplay) {
        $form_display = $differentDisplay;
      }
    }
  }

  /**
   * Render tocbot block.
   *
   * @return array
   *   The tocbot render array.
   */
  protected function renderTocBot(): array {
    $build = [
      '#type' => 'container',
      '#attributes' => ['class' => ['tocbot-block']],
    ];
    try {
      $plugin_block = $this->blockManager->createInstance('tocbot_block', []);
      $build['toc'] = $plugin_block->build();
      $this->renderer->addCacheableDependency($build, $plugin_block);
      $build['title'] = [
        '#type' => 'inline_template',
        '#template' => '<div class="summary-inner"><h4>{{ title }}</h4><svg class="icon icon--chevron-down">
          <use id="icon--chevron-down" xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon--chevron-down"></use>
        </svg></div>',
        '#context' => [
          'title' => $this->t('Table of contents'),
        ],
        '#weight' => -10,
      ];
    }
    catch (PluginException $ex) {
    }
    return $build;
  }

}
