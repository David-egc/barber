<?php

namespace Drupal\bookable_calendar\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the CalendarOpeningVacancy constraint.
 */
class CalendarOpeningVacancyValidator extends ConstraintValidator {

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
    $slots_available = $parent_opening_instance->slotsAvailable();
    $slots_as_parties = $parent_opening_instance->slotsAsParties();
    $slots_requested = (int) $entity->party_size->value;

    // If not first save.
    if (!is_null($entity->id())) {
      $entity_original = \Drupal::entityTypeManager()->getStorage('booking_contact')->loadUnchanged($entity->id());
      $original_slots_requested = (int) $entity_original->party_size->value;
      if ($original_slots_requested) {
        $slots_available += $original_slots_requested;

        // If slots requested is same as last time
        // there is no need to check again.
        if ($slots_requested === $original_slots_requested) {
          return;
        }
      }
    }

    if ($slots_as_parties && $slots_available <= 0) {
      $this->context->addViolation($constraint->noVacancy, [
        '%spots-requested' => 1,
        '%spots-available' => 0,
      ]);
    }
    elseif (!$slots_as_parties && $slots_requested > $slots_available) {
      $this->context->addViolation($constraint->noVacancy, [
        '%spots-requested' => $slots_requested,
        '%spots-available' => $slots_available,
      ]);
    }
  }

}
