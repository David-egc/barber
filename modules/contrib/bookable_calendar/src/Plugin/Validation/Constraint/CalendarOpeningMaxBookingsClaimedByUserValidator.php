<?php

namespace Drupal\bookable_calendar\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the CalendarOpeningMaxBookingsClaimedByUser constraint.
 */
class CalendarOpeningMaxBookingsClaimedByUserValidator extends ConstraintValidator {

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
    $parent_calendar = $entity->getParentCalendar();
    $max_open_bookings = (int) $parent_calendar->max_open_bookings->value;

    if (!is_null($max_open_bookings) & $max_open_bookings !== 0) {
      // Get all opening bookings by this user for this calendar.
      $query = \Drupal::database()->select('booking_contact', 'contact');
      $query->fields('contact', [
        'email',
        'booking_instance',
      ]);
      $query->condition('contact.email', $entity->email->value);
      $query->leftJoin('bookable_calendar_opening_inst', 'inst', 'inst.id = contact.booking_instance');
      $query->condition('inst.date__end_value', strtotime('now'), '>');
      $query->fields('inst', [
        'date__end_value',
        'booking_opening',
      ]);
      $query->leftJoin('bookable_calendar_opening_field_data', 'opening', 'opening.id = inst.booking_opening');
      $query->fields('opening', [
        'bookable_calendar',
      ]);
      $query->condition('opening.bookable_calendar', $parent_calendar->id());
      $user_open_bookings = $query->execute()->fetchAll();
      if (count($user_open_bookings) >= $max_open_bookings) {
        $this->context->addViolation($constraint->overLimit, [
          '%max-bookings' => $max_open_bookings,
        ]);
      }
    }
  }

}
