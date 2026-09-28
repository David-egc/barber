<?php

namespace Drupal\bookable_calendar\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\bookable_calendar\BookingInterface;

/**
 * Defines the booking entity class.
 *
 * @ContentEntityType(
 *   id = "booking",
 *   label = @Translation("Booking"),
 *   label_collection = @Translation("Bookings"),
 *   label_singular = @Translation("booking"),
 *   label_plural = @Translation("bookings"),
 *   label_count = @PluralTranslation(
 *     singular = "@count bookings",
 *     plural = "@count bookings",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\bookable_calendar\BookingListBuilder",
 *     "views_data" = "Drupal\bookable_calendar\BookingViewsData",
 *     "access" = "Drupal\bookable_calendar\BookingAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\bookable_calendar\Form\BookingForm",
 *       "edit" = "Drupal\bookable_calendar\Form\BookingForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "booking",
 *   data_table = "booking",
 *   admin_permission = "administer booking",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "id",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "collection" = "/admin/content/bookable-calendar/booking",
 *     "add-form" = "/admin/content/bookable-calendar/booking/add",
 *     "canonical" = "/bookable-calendar/booking/{booking}",
 *     "edit-form" = "/admin/content/bookable-calendar/booking/{booking}/edit",
 *     "delete-form" = "/admin/content/bookable-calendar/booking/{booking}/delete",
 *   },
 *   field_ui_base_route = "entity.booking.settings",
 * )
 */
class Booking extends ContentEntityBase implements BookingInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setDescription(t('The time that the booking was created.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['booking_instance'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Booking Instance'))
      ->setDescription(t('The Opening Instance this Booking is for.'))
      ->setRequired(TRUE)
      ->setSettings([
        'target_type' => 'bookable_calendar_opening_inst',
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
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'label' => 'above',
        'weight' => 0,
        'settings' => [
          'format' => 'enabled-disabled',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['booking_calendar'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Booking Calendar'))
      ->setDescription(t('The parent Calendar this Booking is for.'))
      ->setRequired(TRUE)
      ->setSettings([
        'target_type' => 'bookable_calendar',
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
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'label' => 'above',
        'weight' => 0,
        'settings' => [
          'format' => 'enabled-disabled',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['contact'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Contact'))
      ->setDescription(t('The Booking Contact this Booking belongs to.'))
      ->setRequired(TRUE)
      ->setSettings([
        'target_type' => 'booking_contact',
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
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'label' => 'above',
        'weight' => 0,
        'settings' => [
          'format' => 'enabled-disabled',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['booking_date'] = BaseFieldDefinition::create('smartdate')
      ->setLabel(t('Booking Date'))
      ->setDescription(t('The date this booking is for.'))
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
        'label' => 'above',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

  /**
   * When a Booking is deleted remove it on the Opening Instance.
   *
   * {@inheritdoc}
   */
  public static function preDelete(EntityStorageInterface $storage, array $entities) {
    // For each booking contact deleted delete all it's bookings.
    foreach ($entities as $entity) {
      $bookable_instance = $entity->booking_instance->entity;

      if ($bookable_instance) {
        $bookings = $bookable_instance->get('booking')->getValue();
        foreach ($bookings as $key => $booking) {
          if ($booking['target_id'] === $entity->id()) {
            // Drop from array.
            unset($bookings[$key]);
            break;
          }
        }
        $bookable_instance->set('booking', $bookings);
        $bookable_instance->save();
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);

    if (!$update) {
      $instanceStorage = \Drupal::entityTypeManager()->getStorage('bookable_calendar_opening_inst');
      $booking_instance = $this->booking_instance->target_id;

      $loaded_booking_instance = $instanceStorage->load($booking_instance);

      if ($loaded_booking_instance) {
        $existing_bookings = $loaded_booking_instance->get('booking')->getValue();

        if ($existing_bookings) {
          array_push($existing_bookings, [
            'target_id' => $this->id(),
          ]);
          $loaded_booking_instance->set('booking', $existing_bookings);
        }
        else {
          $loaded_booking_instance->set('booking', [
            'target_id' => $this->id(),
          ]);
        }
        $loaded_booking_instance->save();

        // Save parent cal to this.
        $parent_cal_id = $loaded_booking_instance->getParentCalendar()->id();
        $this->set('booking_calendar', [
          'target_id' => $parent_cal_id,
        ]);
        $this->save();
      }
    }
  }

  /**
   * Return raw Booking Info.
   */
  public function getRawValues() {
    $raw_value = 'Booking Created: ' . $this->created->value . "\n" .
      'Booking Date: ' . $this->booking_date->value . "\n" .
      'Email: ' . $this->contact->entity->email->value . "\n" .
      'Party Size:' . $this->contact->entity->party_size->value;

    return $raw_value;
  }

}
