<?php

namespace Drupal\bookable_calendar\Plugin\views\filter;

use Drupal\views\Plugin\views\filter\NumericFilter;

/**
 * Filter handler to show the number of availabile slots for an instance.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("bookable_calendar_available_slots_filter")
 */
class AvailableSlotsFilter extends NumericFilter {

  /**
   * {@inheritdoc}
   */
  public function query() {
    if ($this->value['value']) {
      $this->query->view->storage->set('bookable_calendar_available_slots_filter', [
        'value' => $this->value,
        'operator' => $this->operator,
      ]);
    }
  }

}
