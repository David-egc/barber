<?php

namespace Drupal\bookable_calendar\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks whether a Calendar is available to book at all.
 *
 * @Constraint(
 *   id = "CalendarOpeningIsActive",
 *   label = @Translation("Calendar Opening Is Active", context = "Validation"),
 *   type = "string"
 * )
 */
class CalendarOpeningIsActive extends Constraint {

  public $notActive = 'The requested calendar "%calendar" is not currently accepting bookings.';

}
