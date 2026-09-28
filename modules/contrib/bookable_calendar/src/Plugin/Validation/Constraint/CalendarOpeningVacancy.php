<?php

namespace Drupal\bookable_calendar\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Stops users from Booking more spots than allowed on Calendar Opennings.
 *
 * @Constraint(
 *   id = "CalendarOpeningVacancy",
 *   label = @Translation("Calendar Openning Vacancy", context = "Validation"),
 *   type = "string"
 * )
 */
class CalendarOpeningVacancy extends Constraint {

  public $noVacancy = 'You requested %spots-requested for this opening but only %spots-available are available.';

}
