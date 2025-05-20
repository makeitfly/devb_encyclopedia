<?php

namespace Drupal\devb_encyclopedia_migrate\EventSubscriber;

use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigrateImportEvent;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\symfony_mailer\EmailFactoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Contains event subscriber for migrate events.
 *
 * It will delete all contents which is not in the source of the migration
 * after the migration has run.
 */
class MigrateEventSubscriber implements EventSubscriberInterface {

  use StringTranslationTrait;

  /**
   * Constructs a new MigrateEventSubscriber object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger.
   * @param \Drupal\symfony_mailer\EmailFactoryInterface $emailFactory
   *   The email factory service.
   */
  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
    protected LoggerInterface $logger,
    protected EmailFactoryInterface $emailFactory,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      MigrateEvents::POST_IMPORT => ['handlePostMigration'],
    ];
  }

  /**
   * Handle post migration.
   *
   * @param \Drupal\migrate\Event\MigrateImportEvent $event
   *   The migrate import event.
   */
  public function handlePostMigration(MigrateImportEvent $event): void {
    $migration = $event->getMigration();

    if (str_contains($migration->id(), 'nodegoat')) {
      // Send failed email.
      $this->sendFailedEmail($migration);

      // Remove missing content.
      $this->removeMissingContent($migration);
    }
  }

  /**
   * Sends an email with all failed messages for this migration.
   *
   * @param \Drupal\migrate\Plugin\MigrationInterface $migration
   *   The migration.
   */
  public function sendFailedEmail(MigrationInterface $migration): void {
    $mailMessages = [];
    $messages = $migration->getIdMap()->getMessages();

    foreach ($messages as $message) {
      $mailMessages[] = $message->message;
    }

    if (!empty($mailMessages)) {
      $mailMessages = implode("\n", $mailMessages);

      $this->emailFactory->sendTypedEmail('devb_encycopedia_migrate_email_builder', 'failed_messages', [
        'failed_messages' => $mailMessages,
        'migration' => $migration->label(),
      ]);
    }
  }

  /**
   * Remove missing content.
   *
   * @param \Drupal\migrate\Plugin\MigrationInterface $migration
   *   The migration.
   */
  public function removeMissingContent(MigrationInterface $migration): void {
    $id_map = $migration->getIdMap();
    $id_map->prepareUpdate();

    // Clone so that any generators aren't initialized prematurely.
    $source = clone $migration->getSourcePlugin();
    $source->rewind();
    $source_id_values = [];

    while ($source->valid()) {
      $source_id_values[] = $source->current()->getSourceIdValues();
      $source->next();
    }
    $id_map->rewind();

    // Filter the source id values. If we get id's from source, we unpublish.
    $source_id_values = array_filter($source_id_values, static function ($k) {
      return !empty($k['nodegoat_id']);
    });

    if (count($source_id_values)) {
      while ($id_map->valid()) {
        $map_source_id = $id_map->currentSource();
        $destination_ids = $id_map->currentDestination();

        if (!empty($destination_ids['nid'])) {
          $node_storage = $this->entityTypeManager->getStorage('node');
          $node = $node_storage->load($destination_ids['nid']);

          if ($node && !in_array($map_source_id, $source_id_values, FALSE)) {
            try {
              $node->delete();
              $this->logger->notice($this->t('Deleted node with id @id and source id @source_id', [
                '@id' => $node->id(),
                '@source_id' => $map_source_id['nodegoat_id'],
              ]));
            }
            catch (EntityStorageException $e) {
              $this->logger->error($this->t('Could not delete node with id @id and source id @source_id with message @message', [
                '@id' => $node->id(),
                '@source_id' => $map_source_id['nodegoat_id'],
                '@message' => $e->getMessage(),
              ]));
            }
          }
        }

        $id_map->next();
      }
    }
  }

}
