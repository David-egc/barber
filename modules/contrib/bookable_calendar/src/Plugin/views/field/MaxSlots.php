<?php

namespace Drupal\bookable_calendar\Plugin\views\field;

use Drupal\bookable_calendar\Entity\BookableCalendarOpeningInstance;
use Drupal\views\ResultRow;
use Drupal\views\Plugin\views\field\FieldPluginBase;

/**
 * A handler to provide proper displays for opening instance max slots.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("bookable_calendar_max_slots")
 */
class MaxSlots extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getValue(ResultRow $values, $field = NULL) {
    $entity = $relationship_entities['bookable_calendar_opening_inst'] ?? $values->_entity ?? NULL;

    if ($entity instanceof BookableCalendarOpeningInstance) {
      return $entity->get('max_slots')->getvalue()[0]['value'];
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Do nothing.
  }

}
