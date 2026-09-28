<?php

namespace Drupal\bookable_calendar\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Stops users from Booking more spots than allowed on Calendar Opennings.
 *
 * @Constraint(
 *   id = "CalendarOpeningMaxBookingsClaimedByUser",
 *   label = @Translation("Calendar Opening Max Bookings Claimed By User", context = "Validation"),
 *   type = "string"
 * )
 */
class CalendarOpeningMaxBookingsClaimedByUser extends Constraint {

  public $overLimit = 'You have already claimed the max amount of upcoming spots for this calendar of %max-bookings.';

}
