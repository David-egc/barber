<?php

namespace Drupal\bookable_calendar\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Stops users from Booking more spots than allowed on Calendar Opennings.
 *
 * @Constraint(
 *   id = "CalendarOpeningMaxPartySize",
 *   label = @Translation("Calendar Openning Max Party Size", context = "Validation"),
 *   type = "string"
 * )
 */
class CalendarOpeningMaxPartySize extends Constraint {

  public $noVacancy = 'You requested %spots-requested for this opening but the max party size is %max-party-size.';

}
