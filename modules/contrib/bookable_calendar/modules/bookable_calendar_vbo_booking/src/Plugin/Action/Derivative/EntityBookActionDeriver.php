<?php

namespace Drupal\bookable_calendar_vbo_booking\Plugin\Action\Derivative;

use Drupal\Core\Action\Plugin\Action\Derivative\EntityActionDeriverBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\FieldableEntityInterface;

/**
 * A action deriver.
 */
class EntityBookActionDeriver extends EntityActionDeriverBase {

  /**
   * {@inheritdoc}
   */
  protected function isApplicable(EntityTypeInterface $entity_type) {
    return $entity_type->entityClassImplements(FieldableEntityInterface::class);
  }

}
