<?php

namespace Drupal\bookable_calendar\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the CalendarOpeningIsActive constraint.
 */
class CalendarOpeningIsActiveValidator extends ConstraintValidator {

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
    $accepting_bookings = $parent_opening_instance->isAcceptingBookings();

    if (!$accepting_bookings) {
      $calendar_name = $parent_opening_instance->calendarName();
      $this->context->addViolation($constraint->notActive, [
        '%calendar' => $calendar_name,
      ]);
    }
  }

}
