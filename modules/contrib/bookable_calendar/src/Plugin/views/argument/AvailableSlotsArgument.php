<?php

namespace Drupal\bookable_calendar\Plugin\views\argument;

use Drupal\views\Plugin\views\argument\NumericArgument;

/**
 * Argument handler to accept number of available slots for an instance.
 *
 * @ViewsArgument("bookable_calendar_available_slots_argument")
 */
class AvailableSlotsArgument extends NumericArgument {

  /**
   * {@inheritdoc}
   */
  public function query($group_by = FALSE) {
    if ($this->value) {
      $this->query->view->storage->set('bookable_calendar_available_slots_argument', [
        'value' => $this->value,
        'operator' => $this->operator,
      ]);
    }
  }

}
