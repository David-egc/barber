<?php

namespace Drupal\bookable_calendar\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the CalendarOpeningMaxBookingsClaimedSitewideByUser constraint.
 */
class CalendarOpeningMaxBookingsClaimedSitewideByUserValidator extends ConstraintValidator {

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

    $bookable_calendar_config = \Drupal::config('bookable_calendar.settings')->get('sitewide_settings');
    $sitewide_max_open_bookings = (int) $bookable_calendar_config['max_open_bookings'];

    if (!is_null($sitewide_max_open_bookings) & $sitewide_max_open_bookings !== 0) {
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
      ]);

      $user_open_bookings = $query->execute()->fetchAll();
      if (count($user_open_bookings) >= $sitewide_max_open_bookings) {
        $this->context->addViolation($constraint->overLimit, [
          '%max-bookings' => $sitewide_max_open_bookings,
        ]);
      }
    }
  }

}
