<?php

namespace Drupal\bookable_calendar\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Stops users from Booking more spots than allowed sitewide.
 *
 * @Constraint(
 *   id = "CalendarOpeningMaxBookingsClaimedSitewideByUser",
 *   label = @Translation("Calendar Opening Max Sitewide Bookings Claimed By User", context = "Validation"),
 *   type = "string"
 * )
 */
class CalendarOpeningMaxBookingsClaimedSitewideByUser extends Constraint {

  public $overLimit = 'You have already claimed the max amount of upcoming spots sitewide of %max-bookings.';

}
