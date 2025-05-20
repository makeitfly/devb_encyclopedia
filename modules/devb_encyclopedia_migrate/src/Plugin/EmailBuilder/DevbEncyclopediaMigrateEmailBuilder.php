<?php

namespace Drupal\devb_encyclopedia_migrate\Plugin\EmailBuilder;

use Drupal\symfony_mailer\EmailInterface;
use Drupal\symfony_mailer\Processor\EmailBuilderBase;

/**
 * Defines the Email Builder plug-in for general company mails.
 *
 * @EmailBuilder(
 *   id = "devb_encycopedia_migrate_email_builder",
 *   label = "DEVB Encyclopedia Migrate mails",
 *   sub_types = {
 *     "failed_messages" = @Translation("Failed messages"),
 *   },
 * )
 */
class DevbEncyclopediaMigrateEmailBuilder extends EmailBuilderBase {

  /**
   * Saves the parameters for a newly created email.
   *
   * @param \Drupal\symfony_mailer\EmailInterface $email
   *   The email to modify.
   * @param array|null $params
   *   The parameters for this email.
   */
  public function createParams(EmailInterface $email, ?array $params = NULL): void {
    assert($params !== NULL);
    $email->setParam('failed_messages', $params['failed_messages']);
    $email->setParam('migration', $params['migration']);
  }

  /**
   * {@inheritdoc}
   */
  public function build(EmailInterface $email): void {
    $failedMessages = $email->getParam('failed_messages');
    $email->setVariable('failed_messages', $failedMessages);
    $email->setVariable('migration', $email->getParam('migration'));
  }

}
