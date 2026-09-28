<?php

namespace Drupal\bookable_calendar\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks whether a Calendar allowings bookings this far from now.
 *
 * @Constraint(
 *   id = "CalendarOpeningTooFarAway",
 *   label = @Translation("Calendar Opening Too Far Away", context = "Validation"),
 *   type = "string"
 * )
 */
class CalendarOpeningTooFarAway extends Constraint {

  public $tooFarAway = 'The requested calendar "%calendar" is does not allow bookings this far in the futere from now, must be "%future%" in the future at a maximum.';

}
