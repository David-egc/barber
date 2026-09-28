<?php

namespace Drupal\bookable_calendar;

use Drupal\bookable_calendar\Entity\BookableCalendarOpeningInstance;
use Drupal\Core\Field\FieldItemList;
use Drupal\Core\TypedData\ComputedItemListTrait;

/**
 * Item list for a computed field that displays the available slots.
 */
class AvailableSlotsItemList extends FieldItemList {

  use ComputedItemListTrait;

  /**
   * {@inheritdoc}
   */
  protected function computeValue() {
    $entity = $this->getEntity();
    if ($entity instanceof BookableCalendarOpeningInstance) {
      $this->setValue($entity->slotsAvailable());
    }
  }

}
