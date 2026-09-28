<?php

namespace Drupal\bookable_calendar;

use Drupal\views\EntityViewsData;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Provides the views data for the entity.
 */
class BookableCalendarOpeningInstanceViewsData extends EntityViewsData {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();
    $this->attachDateTimeViewsData($data);
    return $data;
  }

  /**
   * Fix views data integration for the smartdate field.
   */
  protected function attachDateTimeViewsData(&$data) {
    // Automatic integration blocked behind https://www.drupal.org/node/2489476.
    $columns = [
      'value' => 'date',
      'end_value' => 'date',
      'duration' => 'numeric',
      'timezone' => 'standard',
      'rrule' => 'standard',
    ];
    // Provide human-readable property names.
    $labels = [
      'value' => $this->t('Start'),
      'end_value' => $this->t('End'),
      'duration' => $this->t('Duration'),
      'timezone' => $this->t('Timezone'),
      'rrule' => $this->t('Recurring'),
    ];
    // Provide human-readable property help text.
    $desc = [
      'value' => $this->t('The start of the specified date/time range.'),
      'end_value' => $this->t('The end of the specified date/time range.'),
      'duration' => $this->t('The duration of the specified date/time range.'),
      'timezone' => $this->t('The timezone of the specified date/time range.'),
      'rrule' => $this->t('The recurrence rule for the specified date/time range.'),
    ];
    // The set of views handlers we want to manipulate.
    $types = [
      'field',
      'filter',
      'sort',
      'argument',
    ];
    $table_name = 'bookable_calendar_opening_inst';
    $field_name_base = 'date';
    foreach ($columns as $column => $plugin_id) {
      foreach ($types as $type) {
        if (isset($data[$table_name][$field_name_base . '__' . $column][$type]) || $type == 'field') {
          $plugin_id_adjusted = $plugin_id;
          // For certain types, the plugin id needs to change.
          if ($plugin_id == 'standard' && in_array($type, ['filter', 'argument'])) {
            $plugin_id_adjusted = 'string';
          }
          // Override the default data with our custom values.
          $data[$table_name][$field_name_base . '__' . $column][$type]['title'] = $this->t('Date') . ' - ' . $labels[$column];
          $data[$table_name][$field_name_base . '__' . $column][$type]['id'] = $plugin_id_adjusted;
          $data[$table_name][$field_name_base . '__' . $column][$type]['help'] = $desc[$column];
          $data[$table_name][$field_name_base . '__' . $column][$type]['field_name'] = $field_name_base;
          $data[$table_name][$field_name_base . '__' . $column][$type]['property'] = $column;
        }
      }
    }

    $type = 'field';
    $entity_type = \Drupal::entityTypeManager()->getDefinition('bookable_calendar_opening_inst');
    $data[$table_name][$field_name_base][$type]['title'] = $this->t('Date');
    $data[$table_name][$field_name_base][$type]['id'] = 'field';
    $data[$table_name][$field_name_base][$type]['help'] = $this->t('Appears in: @bundles.', [
      '@bundles' => $entity_type->getLabel(),
    ]);
    $data[$table_name][$field_name_base][$type]['field_name'] = $field_name_base;
    $data[$table_name][$field_name_base][$type]['property'] = $column;

    // Provide a relationship for the entity type with the entity reference
    // revisions field.
    $args = [
      '@label' => $this->t('Smart date recurring rule'),
      '@field_name' => 'Date',
    ];

    $data[$table_name][$field_name_base . '_rrule']['relationship'] = [
      'title' => $this->t('@label referenced from @field_name', $args),
      'label' => $this->t('@field_name: @label', $args),
      'group' => $this->t('Booking'),
      'help' => $this->t('Appears in: @bundles.', ['@bundles' => implode(', ', ['booking' => 'booking'])]),
      'id' => 'standard',
      'base' => 'smart_date_rule',
      'entity type' => 'smart_date_rule',
      'base field' => 'rid',
      'relationship field' => $field_name_base . '_rrule',
    ];

  }

}
