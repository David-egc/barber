<?php

namespace Drupal\bookable_calendar;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Utility\Token;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Database\Connection;

/**
 * Main Notification service for when a Booking is placed.
 */
class Notification {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The token service.
   *
   * @var \Drupal\Core\Utility\Token
   */
  protected $token;

  /**
   * The configuration factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Undocumented variable.
   *
   * @var \Drupal\bookable_calendar\Entity\BookingContact
   */
  protected $bookableContact;

  /**
   * Undocumented variable.
   *
   * @var \Drupal\bookable_calendar\Entity\BookableCalendar
   */
  protected $bookableCalendar;

  /**
   * Creates a new ModerationInformation instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager service.
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   *   The mail manager.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\Core\Utility\Token $token
   *   The token service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config
   *   The config service.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   */
  public function __construct(
    EntityTypeManagerInterface $entityTypeManager,
    MailManagerInterface $mail_manager,
    MessengerInterface $messenger,
    Token $token,
    ConfigFactoryInterface $config,
    AccountInterface $current_user,
    Connection $connection) {
    $this->entityTypeManager = $entityTypeManager;
    $this->mailManager = $mail_manager;
    $this->messenger = $messenger;
    $this->token = $token;
    $this->config = $config;
    $this->currentUser = $current_user;
    $this->connection = $connection;
  }

  /**
   * Notify the proper users of a new booking taking place.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity we need to send a notification on.
   */
  public function sendNotification(EntityInterface $entity, bool $cancel = FALSE) {
    $langcode = ($this->currentUser->isAnonymous()) ?
      \Drupal::languageManager()->getCurrentLanguage()->getId() :
      $this->currentUser->getPreferredLangcode();
    $this->bookableContact = $entity;
    $this->bookableCalendar = $this->bookableContact->getParentCalendar();

    if ($this->bookableCalendar && $this->bookableCalendar->hasTranslation($langcode)) {
      $this->bookableCalendar = $this->bookableCalendar->getTranslation($langcode);
    }

    $token_service = $this->token;
    $bookable_calendar_config = $this->config->get('bookable_calendar.settings')->get('email_settings');
    $suffix = $cancel ? '_cancel' : '';

    if ($this->shouldNotifyAdmins()) {
      $admin_emails = $this->getAdminEmails();

      // Should grab users langcode.
      foreach ($admin_emails as $email) {
        $to = $email;
        $params['subject'] = $token_service->replace(
          $bookable_calendar_config['admin_email']['subject' . $suffix],
          [
            'booking_contact' => $entity,
            'bookable_calendar' => $this->bookableCalendar,
          ],
          [
            'langcode' => $langcode,
            'clear' => TRUE,
          ]
        );
        $params['format'] = 'text/html';
        $params['message'] = $token_service->replace(
          $bookable_calendar_config['admin_email']['body' . $suffix],
          [
            'booking_contact' => $entity,
            'bookable_calendar' => $this->bookableCalendar,
          ],
          [
            'langcode' => $langcode,
            'clear' => TRUE,
          ]
        );
        $result = $this->mailManager->mail('bookable_calendar', 'bookable_calendar_notification', $to, $langcode, $params, NULL, TRUE);
        if ($result['result'] == FALSE) {
          $this->messenger->addError('There was a problem sending notifying an admin of your booking.');
        }
      }
    }

    if ($this->shouldNotifyUser()) {
      if ($this->bookableCalendar->overriddenUserEmails()) {
        $params['subject'] = $token_service->replace(
          $this->bookableCalendar->{'notification_email_subject' . $suffix}->value,
          [
            'booking_contact' => $entity,
            'bookable_calendar' => $this->bookableCalendar,
          ],
          [
            'langcode' => $langcode,
            'clear' => TRUE,
          ]
        );
        $params['message'] = $token_service->replace(
          $this->bookableCalendar->{'notification_email_body' . $suffix}->value,
          [
            'booking_contact' => $entity,
            'bookable_calendar' => $this->bookableCalendar,
          ],
          [
            'langcode' => $langcode,
            'clear' => TRUE,
          ]
        );
      }
      else {
        $params['subject'] = $token_service->replace(
          $bookable_calendar_config['user_email']['subject' . $suffix],
          [
            'booking_contact' => $entity,
            'bookable_calendar' => $this->bookableCalendar,
          ],
          [
            'langcode' => $langcode,
            'clear' => TRUE,
          ]
        );
        $params['message'] = $token_service->replace(
          $bookable_calendar_config['user_email']['body' . $suffix],
          [
            'booking_contact' => $entity,
            'bookable_calendar' => $this->bookableCalendar,
          ],
          [
            'langcode' => $langcode,
            'clear' => TRUE,
          ]
        );
      }
      $to = $this->bookableContact->email->value;

      $params['format'] = 'text/html';
      ;
      $result = $this->mailManager->mail('bookable_calendar', 'bookable_calendar_notification', $to, $langcode, $params, NULL, TRUE);
      if ($result['result'] == FALSE) {
        $this->messenger->addError('There was a problem emailing you your booking receipt.');
      }
    }
  }

  /**
   * Should we notify the user making the Booking.
   *
   * @return bool
   *   Whether to notify user of their booking request.
   */
  public function shouldNotifyUser() {
    if ($this->bookableCalendar) {
      return (boolean) $this->bookableCalendar->notification_email->value;
    }
    return FALSE;
  }

  /**
   * Should we notify Admins on new Bookings.
   *
   * @return bool
   *   Whether to notify admins of booking.
   */
  public function shouldNotifyAdmins() {
    if ($this->bookableCalendar) {
      return (boolean) $this->bookableCalendar->admin_notification_email->value;
    }
    return FALSE;
  }

  /**
   * Get all emails by Roles and Manually entered and return them in an array.
   *
   * @return array
   *   Array of manually entered emails.
   */
  public function getAdminEmails() {
    $role_emails = $this->emailRoles();
    $manual_emails = $this->emailManual();
    return array_merge($role_emails, $manual_emails);
  }

  /**
   * Get all emails associated with roles selected on the Bookable Calendar.
   *
   * @return array
   *   Array of emails based on selected roles for Calendar.
   */
  public function emailRoles() {
    $emails_recipients = [];
    $selected_roles = $this->bookableCalendar->get('notify_email_recipient_role')->getValue();

    foreach ($selected_roles as $role) {
      // Get all authenticated users assigned to a specified role.
      $query = $this->connection->select('user__roles', 'ur');
      $query->distinct();
      $query->join('users_field_data', 'u', 'u.uid = ur.entity_id');
      $query->fields('u', ['mail']);
      $query->condition('ur.roles_target_id', $role['target_id']);
      $query->condition('u.status', 1);
      $query->condition('u.mail', '', '<>');
      $query->orderBy('mail');
      $emails = $query->execute()->fetchCol();
      foreach ($emails as $email) {
        $emails_recipients[] = $email;
      }
    }

    return $emails_recipients;
  }

  /**
   * Get all emails entered on the Bookable Calendar.
   *
   * @return array
   *   Array of emails to send emails to.
   */
  public function emailManual() {
    $emails_recipients = [];
    $emails_recipients_manual = $this->bookableCalendar->get('notify_email_recipients')->getValue();

    foreach ($emails_recipients_manual as $email) {
      $emails_recipients[] = $email['value'];
    }

    return $emails_recipients;
  }

}
