<?php

namespace Drupal\bookable_calendar\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\bookable_calendar\BookableCalendarOpeningInstanceInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\bookable_calendar\DateFormatter;

/**
 * Defines the bookable calendar opening instance entity class.
 *
 * @ContentEntityType(
 *   id = "bookable_calendar_opening_inst",
 *   label = @Translation("Bookable Calendar Opening Instance"),
 *   label_collection = @Translation("Bookable Calendar Opening Instances"),
 *   label_singular = @Translation("bookable calendar opening instance"),
 *   label_plural = @Translation("bookable calendar opening instances"),
 *   label_count = @PluralTranslation(
 *     singular = "@count bookable calendar opening instances",
 *     plural = "@count bookable calendar opening instances",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\bookable_calendar\BookableCalendarOpeningInstanceListBuilder",
 *     "view_builder" = "Drupal\bookable_calendar\BookableCalendarOpeningInstanceViewBuilder",
 *     "views_data" = "Drupal\bookable_calendar\BookableCalendarOpeningInstanceViewsData",
 *     "access" = "Drupal\bookable_calendar\BookableCalendarOpeningInstanceAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\bookable_calendar\Form\BookableCalendarOpeningInstanceForm",
 *       "edit" = "Drupal\bookable_calendar\Form\BookableCalendarOpeningInstanceForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "bookable_calendar_opening_inst",
 *   admin_permission = "administer bookable calendar opening instance",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "title",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "collection" = "/admin/content/bookable-calendar/bookable-calendar-opening-instance",
 *     "add-form" = "/admin/content/bookable-calendar/booking-calendar-opening-instance/add",
 *     "canonical" = "/bookable-calendar/booking-calendar-opening-instance/{bookable_calendar_opening_inst}",
 *     "edit-form" = "/admin/content/bookable-calendar/booking-calendar-opening-instance/{bookable_calendar_opening_inst}/edit",
 *     "delete-form" = "/admin/content/bookable-calendar/booking-calendar-opening-instance/{bookable_calendar_opening_inst}/delete",
 *   },
 *   field_ui_base_route = "entity.bookable_calendar_opening_inst.settings",
 * )
 */
class BookableCalendarOpeningInstance extends ContentEntityBase implements BookableCalendarOpeningInstanceInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('Title of the Opening Instance'));
    $fields['booking'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Booking'))
      ->setDescription(t('Each booking that references this instance.'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSettings([
        'target_type' => 'booking',
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'placeholder' => '',
        ],
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['booking_opening'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Booking Opening'))
      ->setDescription(t('The Opening this Instance points to.'))
      ->setCardinality(1)
      ->setRequired(TRUE)
      ->setSettings([
        'target_type' => 'bookable_calendar_opening',
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'placeholder' => '',
        ],
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['date'] = BaseFieldDefinition::create('smartdate')
      ->setLabel(t('Date'))
      ->setDescription(t('The dates for this opening instance.'))
      ->setCardinality(1)
      ->setRequired(TRUE)
      ->setDefaultValue([
        'default_date_type' => 'next_hour',
        'default_date' => '',
        'default_duration_increments' => "30\r\n60|1 hour\r\n90\r\n120|2 hours\r\ncustom",
        'default_duration' => '60',
      ])
      ->setDisplayOptions('form', [
        'type' => 'smartdate_default',
        'weight' => 0,
        'settings' => [
          'modal' => TRUE,
          'default_tz' => 'user',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'smartdate_default',
        'label' => 'hidden',
        'weight' => 0,
        'settings' => [
          'format' => 'time_only',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['available_slots'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Available slots'))
      ->setDescription(t('The available slots for this opening instance.'))
      ->setCardinality(1)
      ->setComputed(TRUE)
      ->setClass('\Drupal\bookable_calendar\AvailableSlotsItemList')
      ->setDisplayConfigurable('view', TRUE);

    $fields['max_slots'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Maximum slots'))
      ->setDescription(t('The maximum slots for this opening instance.'))
      ->setCardinality(1)
      ->setComputed(TRUE)
      ->setClass('\Drupal\bookable_calendar\MaxSlotsItemList')
      ->setDisplayConfigurable('view', TRUE);

    $fields['slots'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Slots'))
      ->setDescription(t("The number of available slots for this instance. If empty (NB not zero), will use calendar's defaults."))
      ->setCardinality(1)
      ->setDisplayOptions('form', [
        'widget_type' => 'integer',
        'weight' => 10,
      ])
      ->setSetting('min', 0)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

  /**
   * Instance is created set the title based on the date.
   *
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::preSave($storage, $update);

    $date = $this->date;
    $formatted_date = DateFormatter::formatDateToBooking($date->value, $date->end_value);
    $this->set('title', $this->getParentCalendar()->label() . ': ' . $formatted_date);

  }

  /**
   * Instance is created update the parent opening to have this one saved.
   *
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);
  }

  /**
   * When an Instance is deleted remove it from the repeat rule on the parent opening.
   *
   * {@inheritdoc}
   */
  public static function preDelete(EntityStorageInterface $storage, array $entities) {
    foreach ($entities as $entity) {
      $start_date = $entity->date->value;
      $end_date = $entity->date->end_value;
      $opening = $entity->booking_opening->entity;
      $database = \Drupal::database();

      // Find rrule instance we need to remove.
      $instance_to_delete = $database->select('bookable_calendar_opening__date', 'date')
        ->condition('date.entity_id', $opening->id())
        ->condition('date.date_value', $start_date)
        ->condition('date.date_end_value', $end_date)
        ->fields('date', [
          'entity_id',
          'date_value',
          'date_end_value',
          'date_rrule',
          'date_rrule_index',
        ])
        ->execute()->fetch();

      if (!is_null($instance_to_delete)) {
        // If we found an instance to delete, delete it.
        $database->delete('bookable_calendar_opening__date')
          ->condition('entity_id', $opening->id())
          ->condition('date_value', $start_date)
          ->condition('date_end_value', $end_date)
          ->execute();

        // Also add override so it doesn't come back.
        $smart_date_override_storage = \Drupal::entityTypeManager()->getStorage('smart_date_override');
        $new_smart_date = $smart_date_override_storage->create([
          'rrule' => $instance_to_delete->date_rrule,
          'rrule_index' => $instance_to_delete->date_rrule_index,
          'value' => NULL,
          'end_value' => NULL,
          'duration' => NULL,
        ]);
        $new_smart_date->save();
      }
    }
  }

  /**
   * How many remaining slots are there.
   *
   * @return int
   *   Amount of slots available.
   */
  public function slotsAvailable() {
    $max_slots = $this->maxSlotsAvailable();
    if ($this->slotsAsParties()) {
      $currenty_party_count = $this->partyCount();
      $slots_availble = $max_slots - $currenty_party_count;
    }
    else {
      $existing_bookings = $this->get('booking')->getValue();
      $existing_bookings_count = count($existing_bookings);
      $slots_availble = $max_slots - $existing_bookings_count;
    }
    return $slots_availble;
  }

  /**
   * Whether the current user has a booking on this instance.
   *
   * @return bool
   */
  public function isBookedByCurrentUser() {
    $ids = $this->entityTypeManager()->getStorage('booking_contact')->getQuery()
      ->condition('uid', \Drupal::currentUser()->id())
      ->condition('uid', 0, '<>')
      ->condition('booking_instance', $this->id())
      ->accessCheck(FALSE)
      ->execute();
    return !empty($ids);
  }

  /**
   * All booking contacts of the current user for this instance.
   *
   * @return \Drupal\bookable_calendar\Entity\BookingContact[]
   */
  public function getBookingContactsByCurrentUser() {
    $ids = $this->entityTypeManager()->getStorage('booking_contact')->getQuery()
      ->condition('uid', \Drupal::currentUser()->id())
      ->condition('uid', 0, '<>')
      ->condition('booking_instance', $this->id())
      ->accessCheck(FALSE)
      ->execute();
    return empty($ids) ? [] : BookingContact::loadMultiple($ids);
  }

  /**
   * The Calendar this Instance is part of.
   */
  public function getParentCalendar() {
    return $this->booking_opening->entity->bookable_calendar->entity;
  }

  /**
   * The Opening this Instance is part of.
   */
  public function getParentOpening() {
    return $this->booking_opening->entity;
  }

  /**
   * Max amount of slots available.
   */
  public function maxSlotsAvailable() {
    if (!$this->slots->isEmpty()) {
      return $this->slots->value;
    }
    if (!$this->getParentOpening()->slots->isEmpty()) {
      return $this->getParentOpening()->slots->value;
    }
    return $this->getParentCalendar()->slots_per_opening->value;
  }

  /**
   * Max party size allowed for this calendar.
   */
  public function maxPartySize() {
    return (int) $this->getParentCalendar()->max_party_size->value;
  }

  /**
   * Are we currently accepting bookings on this calendar.
   */
  public function isAcceptingBookings() {
    return (boolean) $this->getParentCalendar()->active->value;
  }

  /**
   * The name of the parent Calendar.
   */
  public function calendarName() {
    return (string) $this->getParentCalendar()->title->value;
  }

  /**
   * Are we treating Slots as Parties.
   */
  public function slotsAsParties() {
    return (bool) $this->getParentCalendar()->slots_as_parties->value;
  }

  /**
   * Get all bookings to know how many parties have been added.
   *
   * @return int
   *   number of parties booked.
   */
  public function partyCount() {
    $bookings = $this->get('booking')->getValue();
    $booking_parties = [];
    $booking_storage = \Drupal::entityTypeManager()->getStorage('booking');
    foreach ($bookings as $booking) {
      if ($loaded_booking = $booking_storage->load($booking['target_id'])) {
        $booking_contact_id = $loaded_booking->contact->target_id;
        if (!in_array($booking_contact_id, $booking_parties, TRUE)) {
          array_push($booking_parties, $booking_contact_id);
        }
      }
    }

    return (int) count($booking_parties);
  }

  /**
   * Confirm this Instance is not in the past.
   *
   * @return bool
   *   Whether is past or not.
   */
  public function isInPast() {
    $allow_in_progress_bookings = $this->getParentCalendar()->book_in_progress->value;
    if ($allow_in_progress_bookings) {
      $opening_time = (int) $this->date->end_value;
    }
    else {
      $opening_time = (int) $this->date->value;
    }
    $now = strtotime('now');
    if ($opening_time <= $now) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Confirm that right now isn't too close to that.
   *
   * @return bool
   *   Is it too close to now.
   */
  public function isTooSoon() {
    if ($this->getParentCalendar()->book_in_progress->value) {
      // If you can book things in progress it will never be too soon.
      return FALSE;
    }
    $lead_time_raw = $this->getBookingLeadTime();
    $lead_time = strtotime('now ' . $lead_time_raw);
    $instance_start_date = (int) $this->date->value;

    if ($lead_time >= $instance_start_date) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * How far is the future it has to be to book this.
   */
  public function getBookingLeadTime() {
    return $this->getParentCalendar()->booking_lead_time->value;
  }

  /**
   * Confirm that right now isn't too far away from this instance.
   *
   * @return bool
   *   Whether it's too far in the future to book this.
   */
  public function isTooFarAway() {
    $booking_future_time = $this->getBookingFutureTime();

    if (!is_null($booking_future_time)) {
      $future_time = strtotime('now ' . $booking_future_time);
      $instance_start_date = (int) $this->date->value;

      if ($future_time <= $instance_start_date) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Get how far in the future you can book.
   */
  public function getBookingFutureTime() {
    return $this->getParentCalendar()->booking_future_time->value;
  }

  /**
   * Get success message on parent calendar.
   */
  public function getSuccessMessage() {
    return $this->getParentCalendar()->success_message->value;
  }

  /**
   * Get whether One-Click Bookings are enabled.
   */
  public function oneClickBookingsEnabled() {
    return $this->getParentCalendar()->oneClickBookingsEnabled();
  }

}
