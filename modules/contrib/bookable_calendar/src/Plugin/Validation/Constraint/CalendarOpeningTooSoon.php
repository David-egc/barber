<?php

namespace Drupal\bookable_calendar\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks whether a Calendar allowings bookings this soon to now.
 *
 * @Constraint(
 *   id = "CalendarOpeningTooSoon",
 *   label = @Translation("Calendar Opening Too Soon", context = "Validation"),
 *   type = "string"
 * )
 */
class CalendarOpeningTooSoon extends Constraint {

  public $tooSoon = 'The requested calendar "%calendar" is does not allow bookings this close to now, must be "%future%" in the future at a minimum.';

}
