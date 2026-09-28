<?php

namespace Drupal\bookable_calendar\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks whether a Calendar Opening is in the past.
 *
 * @Constraint(
 *   id = "CalendarOpeningNotInPast",
 *   label = @Translation("Calendar Opening Not In Past", context = "Validation"),
 *   type = "string"
 * )
 */
class CalendarOpeningNotInPast extends Constraint {

  public $inPast = 'You are not allowed to book things in the past';

}
