<?php

namespace Drupal\bookable_calendar\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\bookable_calendar\BookableCalendarOpeningInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Defines the bookable calendar opening entity class.
 *
 * @ContentEntityType(
 *   id = "bookable_calendar_opening",
 *   label = @Translation("Bookable Calendar Opening"),
 *   label_collection = @Translation("Bookable Calendar Openings"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\bookable_calendar\BookableCalendarOpeningListBuilder",
 *     "views_data" = "Drupal\bookable_calendar\BookableCalendarOpeningViewsData",
 *     "access" = "Drupal\bookable_calendar\BookableCalendarOpeningAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\bookable_calendar\Form\BookableCalendarOpeningForm",
 *       "edit" = "Drupal\bookable_calendar\Form\BookableCalendarOpeningForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "bookable_calendar_opening",
 *   data_table = "bookable_calendar_opening_field_data",
 *   revision_table = "bookable_calendar_opening_revision",
 *   revision_data_table = "bookable_calendar_opening_field_revision",
 *   show_revision_ui = TRUE,
 *   translatable = TRUE,
 *   admin_permission = "administer bookable calendar opening",
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
 *     "add-form" = "/admin/content/bookable-calendar/booking-calendar-opening/add",
 *     "canonical" = "/bookable-calendar/bookable-calendar-opening/{bookable_calendar_opening}",
 *     "edit-form" = "/admin/content/bookable-calendar/booking-calendar-opening/{bookable_calendar_opening}/edit",
 *     "delete-form" = "/admin/content/bookable-calendar/booking-calendar-opening/{bookable_calendar_opening}/delete",
 *     "collection" = "/admin/content/bookable-calendar/bookable-calendar-opening"
 *   },
 *   field_ui_base_route = "entity.bookable_calendar_opening.settings"
 * )
 */
class BookableCalendarOpening extends RevisionableContentEntityBase implements BookableCalendarOpeningInterface {

  use StringTranslationTrait;

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
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(t('Title'))
      ->setDescription(t('The title of the bookable calendar opening entity.'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setRevisionable(TRUE)
      ->setLabel(t('Status'))
      ->setDescription(t('A boolean indicating whether the bookable calendar opening is enabled.'))
      ->setDefaultValue(TRUE)
      ->setSetting('on_label', 'Enabled')
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => FALSE,
        ],
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'boolean',
        'label' => 'above',
        'weight' => 0,
        'settings' => [
          'format' => 'enabled-disabled',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['bookable_calendar'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Bookable Calendar'))
      ->setRequired(TRUE)
      ->setDescription(t('The calendar this Opening Belongs to'))
      ->setCardinality(1)
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
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'label' => 'above',
        'weight' => -4,
        'settings' => [
          'format' => 'enabled-disabled',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['booking_instance'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Booking Instance'))
      ->setDescription(t('The Opening Instances for this Opening.'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSettings([
        'target_type' => 'bookable_calendar_opening_inst',
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'label' => 'above',
        'weight' => 0,
        'settings' => [
          'format' => 'enabled-disabled',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['date'] = BaseFieldDefinition::create('smartdate')
      ->setLabel(t('Date'))
      ->setDescription(t('The dates for this opening.'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setRequired(TRUE)
      ->setDefaultValue([
        'default_date_type' => 'next_hour',
        'default_date' => '',
        'default_duration_increments' => "15\r\n30\r\n60|1 hour\r\n90\r\n120|2 hours\r\ncustom",
        'default_duration' => '60',
      ])
      ->setSetting('allow_recurring', TRUE)
      ->setSetting('month_limit', '13')
      ->setDisplayOptions('form', [
        'type' => 'smartdate_default',
        'weight' => -3,
        'settings' => [
          'modal' => TRUE,
          'default_tz' => 'user',
        ],
        'third_party_settings' => [
          'smart_date_recur' => [
            'allowed_recur_freq_values' => [
              'MINUTELY' => 'MINUTELY',
              'HOURLY' => 'HOURLY',
              'DAILY' => 'DAILY',
              'WEEKLY' => 'WEEKLY',
              'MONTHLY' => 'MONTHLY',
              'YEARLY' => 'YEARLY',
            ],
          ],
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['slots'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Slots'))
      ->setDescription(t("The default number of available slots for instances in this opening. If empty (NB not zero), will use calendar's defaults."))
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
   * When a Opening is deleted delete all it's individual Instances.
   *
   * {@inheritdoc}
   */
  public static function preDelete(EntityStorageInterface $storage, array $entities) {
    $instanceStorage = \Drupal::entityTypeManager()->getStorage('bookable_calendar_opening_inst');
    // For each booking contact deleted delete all it's bookings.
    foreach ($entities as $entity) {
      $instances = $entity->get('booking_instance')->getValue();
      foreach ($instances as $instance) {
        $loaded_instance = $instanceStorage->load($instance['target_id']);
        if ($loaded_instance) {
          $loaded_instance->delete();
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    if (!$this->original) {
      return;
    }
    // If updating Opening and the Parent Cal has changed remove it from the Parent Cal.
    $original_bookable_cal_id = $this->original->bookable_calendar->target_id;
    if (!is_null($original_bookable_cal_id)) {
      $new_bookable_cal_id = $this->bookable_calendar->target_id;
      if ($original_bookable_cal_id !== $new_bookable_cal_id) {
        $parent_cal = $this->original->bookable_calendar->entity;
        $existing_openings = $parent_cal->get('calendar_openings')->getValue();
        foreach ($existing_openings as $key => $existing_opening) {
          if ($existing_opening['target_id'] === $this->id()) {
            unset($existing_openings[$key]);
            break;
          }
        }
        $parent_cal->set('calendar_openings', $existing_openings);
        $parent_cal->save();
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);

    // Check if parent cal already holds this opening
    // if not add it to the parent cal.
    $parent_cal = $this->bookable_calendar->entity;
    $existing_openings = $parent_cal->get('calendar_openings')->getValue();
    $already_exists = FALSE;
    foreach ($existing_openings as $existing_opening) {
      if ($existing_opening['target_id'] === $this->id()) {
        $already_exists = TRUE;
        break;
      }
    }
    if (!$already_exists) {
      $existing_openings[] = [
        'target_id' => $this->id(),
      ];

      $parent_cal->set('calendar_openings', $existing_openings);
      $parent_cal->save();
    }
  }

}
