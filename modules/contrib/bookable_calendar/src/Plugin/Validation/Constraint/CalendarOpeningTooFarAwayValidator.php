<?php

namespace Drupal\bookable_calendar\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the CalendarOpeningFarAway constraint.
 */
class CalendarOpeningTooFarAwayValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    $account = \Drupal::currentUser();
    if ($account->hasPermission('bypass booking contact checks')) {
      return TRUE;
    }

    /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
    $entity = $this->context->getRoot()->getValue();

    $parent_opening_instance = $entity->booking_instance->entity;
    $too_far_away = $parent_opening_instance->isTooFarAway();

    if ($too_far_away) {
      $booking_future_time = $parent_opening_instance->getBookingFutureTime();
      $calendar_name = $parent_opening_instance->calendarName();
      $this->context->addViolation($constraint->tooFarAway, [
        '%calendar' => $calendar_name,
        '%future%' => $booking_future_time,
      ]);
    }
  }

}
