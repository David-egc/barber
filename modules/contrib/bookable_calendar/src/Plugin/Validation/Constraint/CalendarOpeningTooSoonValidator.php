<?php

namespace Drupal\bookable_calendar\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the CalendarOpeningTooSoon constraint.
 */
class CalendarOpeningTooSoonValidator extends ConstraintValidator {

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
    $too_soon = $parent_opening_instance->isTooSoon();

    if ($too_soon) {
      $booking_lead_time = $parent_opening_instance->getBookingLeadTime();
      $calendar_name = $parent_opening_instance->calendarName();
      $this->context->addViolation($constraint->tooSoon, [
        '%calendar' => $calendar_name,
        '%future%' => $booking_lead_time,
      ]);
    }
  }

}
