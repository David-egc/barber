<?php

namespace Drupal\bookable_calendar\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\bookable_calendar\Entity\BookableCalendar;

/**
 * Returns responses for Bookable Calendar routes.
 */
class BookableCalendarCheckInController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build(BookableCalendar $bookable_calendar) {

    return [
      '#theme' => 'admin_booking_list',
      'rows' => [],
    ];
  }

}
