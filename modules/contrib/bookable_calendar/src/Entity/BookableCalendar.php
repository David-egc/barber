<?php

namespace Drupal\bookable_calendar\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\bookable_calendar\BookableCalendarInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Defines the bookable calendar entity class.
 *
 * @ContentEntityType(
 *   id = "bookable_calendar",
 *   label = @Translation("Bookable Calendar"),
 *   label_collection = @Translation("Bookable Calendars"),
 *   handlers = {
 *     "view_builder" = "Drupal\bookable_calendar\BookableCalendarViewBuilder",
 *     "list_builder" = "Drupal\bookable_calendar\BookableCalendarListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\bookable_calendar\BookableCalendarAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\bookable_calendar\Form\BookableCalendarForm",
 *       "edit" = "Drupal\bookable_calendar\Form\BookableCalendarForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "bookable_calendar",
 *   data_table = "bookable_calendar_field_data",
 *   revision_table = "bookable_calendar_revision",
 *   revision_data_table = "bookable_calendar_field_revision",
 *   show_revision_ui = TRUE,
 *   translatable = TRUE,
 *   admin_permission = "administer bookable calendar",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "revision_id",
 *     "langcode" = "langcode",
 *     "label" = "title",
 *     "uuid" = "uuid"
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_uid",
 *     "revision_created" = "revision_timestamp",
 *     "revision_log_message" = "revision_log"
 *   },
 *   links = {
 *     "add-form" = "/admin/content/bookable-calendar/bookable-calendar/add",
 *     "canonical" = "/bookable-calendar/{bookable_calendar}",
 *     "edit-form" = "/admin/content/bookable-calendar/bookable-calendar/{bookable_calendar}/edit",
 *     "delete-form" = "/admin/content/bookable-calendar/bookable-calendar/{bookable_calendar}/delete",
 *     "collection" = "/admin/content/bookable-calendar"
 *   },
 *   field_ui_base_route = "entity.bookable_calendar.settings"
 * )
 */
class BookableCalendar extends RevisionableContentEntityBase implements BookableCalendarInterface {

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return $this->get('title')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTitle($title) {
    $this->set('title', $title);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->get('description')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function isEnabled() {
    return (bool) $this->get('status')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setStatus($status) {
    $this->set('status', $status);
    return $this;
  }

  /**
   * Whether this calendar overrides System Wide email settings.
   */
  public function overriddenUserEmails() {
    return (bool) $this->get('notification_email_override')->value;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(t('Title'))
      ->setDescription(t('The title of the bookable calendar entity.'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -10,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(t('Description'))
      ->setDescription(t('A description of the bookable calendar.'))
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => -9,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'hidden',
        'weight' => -9,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['success_message'] = BaseFieldDefinition::create('text_long')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(t('Success message'))
      ->setDescription(t('Message displayed after a sucessful registration.'))
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => -8,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', FALSE);

    $fields['notification_email'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('User Notification Email'))
      ->setDescription(t('If enabled users will get a notification email when they book an opening.'))
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => TRUE,
        ],
        'weight' => -7,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', FALSE);

    $fields['notification_email_override'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Override Default User Email'))
      ->setDescription(t('Text in the email is editable on the main settings page, if you want different text per calendar you will need to select this'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => TRUE,
        ],
        'weight' => -6,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', FALSE);

    $fields['notification_email_subject'] = BaseFieldDefinition::create('string')
      ->setLabel(t('User Email Subject'))
      ->setTranslatable(TRUE)
      ->setDescription(t('The subject of the email sent to the user if Override Default User Email is set'))
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['notification_email_body'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('User Email Body'))
      ->setTranslatable(TRUE)
      ->setDescription(t('The body of the email sent to the user if Override Default User Email is set'))
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['notification_email_subject_cancel'] = BaseFieldDefinition::create('string')
      ->setLabel(t('User Email Subject  for cancellations'))
      ->setTranslatable(TRUE)
      ->setDescription(t('The subject of the email for cancellations sent to the user if Override Default User Email is set'))
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['notification_email_body_cancel'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('User Email Body for cancellations'))
      ->setTranslatable(TRUE)
      ->setDescription(t('The body of the email for cancellations sent to the user if Override Default User Email is set'))
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['admin_notification_email'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Admin Notification Emails'))
      ->setDescription(t('If enabled admins will get a notification email on each booking.'))
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => TRUE,
        ],
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', FALSE);

    $fields['notify_email_recipient_role'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Notification Email Recipient Role'))
      ->setDescription(t('Send email notifications on bookings to the following roles.'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSettings([
        'target_type' => 'user_role',
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_buttons',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'placeholder' => '',
        ],
        'weight' => -3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', FALSE);

    $fields['notify_email_recipients'] = BaseFieldDefinition::create('email')
      ->setLabel(t('Notification Email Recipients'))
      ->setDescription(t('The email address, or addresses one per line to receive the emails on bookings.'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setDisplayOptions('form', [
        'type' => 'email_default',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'placeholder' => '',
        ],
        'weight' => -2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', FALSE);

    $fields['max_party_size'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Max Party Size'))
      ->setDescription(t('What is the largest amount of bookings a single user can claim per opening. This limits a single user from claiming all 10 to themselves.'))
      ->setDisplayOptions('form', [
        'type' => 'number',
        'settings' => [
          'display_label' => FALSE,
        ],
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', FALSE);

    $fields['max_open_bookings'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Max Open Bookings Per User'))
      ->setDescription(t('Limit the amount of future bookings a single user can do, will limit off of email address.'))
      ->setDisplayOptions('form', [
        'type' => 'number',
        'settings' => [
          'display_label' => FALSE,
        ],
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', FALSE);

    $fields['slots_per_opening'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Slots Per Opening'))
      ->setDescription(t("The amount of Bookings that can claim a single opening. For example if your event has hour long openings you let people register for and you can handle 10 people per hour set this field to '10.'"))
      ->setRequired(TRUE)
      ->setDefaultValue(1)
      ->setDisplayOptions('form', [
        'type' => 'number',
        'settings' => [
          'display_label' => FALSE,
        ],
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', FALSE);

    $fields['slots_as_parties'] = BaseFieldDefinition::create('boolean')
      ->setRevisionable(TRUE)
      ->setLabel(t('Treat Slots as Parties'))
      ->setDescription(t('This will change the math of "Max Slots" to "Max Parties". This will allow you to only have 3 different groups claim a slot but each group can have as big of a party as your Max Party Size limit.'))
      ->setDefaultValue(FALSE)
      ->setSetting('on_label', 'Treat Slots as Parties')
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => FALSE,
        ],
        'weight' => 2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', FALSE);

    $fields['booking_future_time'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Booking Future Time'))
      ->setDescription(t('How far in the future from now are people allowed to register,
        this would stop people from registering for events over a month away. An offset from the current
        time such as "+ 1 day" "+ 1 month" or "+ 1 year" using
        <a href="https://www.php.net/manual/en/function.strtotime.php">php strtotime</a>.'))
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', FALSE);

    $fields['booking_lead_time'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Booking Lead Time'))
      ->setDescription(t('How close to now are people allowed to register,
        this would stop people from registering for same day events.
        An offset from the current time such as "now" "+ 15 minutes" or "+ 1 day" using
        <a href="https://www.php.net/manual/en/function.strtotime.php">php strtotime</a>.'))
      ->setRequired(TRUE)
      ->setDefaultValue('now')
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', FALSE);

    $fields['book_in_progress'] = BaseFieldDefinition::create('boolean')
      ->setRevisionable(TRUE)
      ->setLabel(t('Allow In Progress Bookings'))
      ->setDescription(t('Allows for bookings to happen as long as opening has not ended.'))
      ->setDefaultValue(FALSE)
      ->setSetting('on_label', 'Allow In Progress Bookings')
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => FALSE,
        ],
        'weight' => 5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', FALSE);

    $fields['one_click_booking'] = BaseFieldDefinition::create('boolean')
      ->setRevisionable(TRUE)
      ->setLabel(t('Enable "One-Click" Booking of Openings'))
      ->setDescription(t('If user is logged in, clicking the "Book" button will book the opening with the logged in users information but will only book a single slot.'))
      ->setDefaultValue(FALSE)
      ->setSetting('on_label', 'Enable "One-Click" Bookings')
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => FALSE,
        ],
        'weight' => 5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', FALSE);

    $fields['calendar_openings'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Calendar Openings'))
      ->setDescription(t('The Openings for this Calendar.'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSettings([
        'target_type' => 'bookable_calendar_opening',
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', FALSE);

    $fields['active'] = BaseFieldDefinition::create('boolean')
      ->setRevisionable(TRUE)
      ->setLabel(t('Active'))
      ->setDescription(t('Allows for calendars to be visible but no longer accepting Bookings whether temporary or permanently.'))
      ->setDefaultValue(TRUE)
      ->setSetting('on_label', 'Active')
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => FALSE,
        ],
        'weight' => 11,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', FALSE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setRevisionable(TRUE)
      ->setLabel(t('Status'))
      ->setDescription(t('A boolean indicating whether the bookable calendar is enabled.
        It is different than active as this will hide it from end users instead of just disabling Bookings'))
      ->setDefaultValue(TRUE)
      ->setSetting('on_label', 'Enabled')
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => FALSE,
        ],
        'weight' => 12,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', FALSE);

    return $fields;
  }

  /**
   * If One-Click Bookings are enabled for the Cal.
   *
   * @return bool
   */
  public function oneClickBookingsEnabled() {
    if ($this->one_click_booking->value) {
      return TRUE;
    }
    $bookable_calendar_config = \Drupal::config('bookable_calendar.settings')->get('sitewide_settings');
    $sitewide_one_click_booking = (boolean) $bookable_calendar_config['one_click_booking'];
    if ($sitewide_one_click_booking) {
      return TRUE;
    }
    return FALSE;
  }

}
