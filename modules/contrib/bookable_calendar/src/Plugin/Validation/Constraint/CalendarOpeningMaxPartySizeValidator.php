<?php

namespace Drupal\bookable_calendar\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the NotEmptyWhenPublished constraint.
 */
class CalendarOpeningMaxPartySizeValidator extends ConstraintValidator {

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
    $max_party_size = (int) $parent_opening_instance->maxPartySize();
    $slots_requested = (int) $entity->party_size->value;

    if ($slots_requested > $max_party_size && $max_party_size !== 0) {
      $this->context->addViolation($constraint->noVacancy, [
        '%spots-requested' => $slots_requested,
        '%max-party-size' => $max_party_size,
      ]);
    }
  }

}
