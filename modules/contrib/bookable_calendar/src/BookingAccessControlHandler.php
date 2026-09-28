<?php

namespace Drupal\bookable_calendar;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the booking entity type.
 */
class BookingAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {

    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view booking');

      case 'update':
        return AccessResult::allowedIfHasPermissions($account, [
          'edit booking',
          'administer booking',
        ], 'OR');

      case 'delete':
        return AccessResult::allowedIfHasPermissions($account, [
          'delete booking',
          'administer booking',
        ], 'OR');

      default:
        // No opinion.
        return AccessResult::neutral();
    }

  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermissions($account, [
      'create booking',
      'administer booking',
    ], 'OR');
  }

}
