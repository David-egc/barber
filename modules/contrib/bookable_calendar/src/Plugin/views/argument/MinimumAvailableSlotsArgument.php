<?php

namespace Drupal\bookable_calendar\Plugin\views\argument;

use Drupal\views\Plugin\views\argument\NumericArgument;

/**
 * Argument handler to accept minimum number of available slots for an instance.
 *
 * I.e. pass instances for which the number of available slots is greater than
 * or equal to the argument value.
 *
 * @ViewsArgument("bookable_calendar_minimum_available_slots_argument")
 */
class MinimumAvailableSlotsArgument extends NumericArgument {

  /**
   * {@inheritdoc}
   */
  public function query($group_by = FALSE) {
    if ($this->value) {
      $this->query->view->storage->set('bookable_calendar_minimum_available_slots_argument', [
        'value' => $this->value,
        'operator' => $this->operator,
      ]);
    }
  }

}
