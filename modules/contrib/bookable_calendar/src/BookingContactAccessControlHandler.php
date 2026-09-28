<?php

namespace Drupal\bookable_calendar;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultAllowed;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\bookable_calendar\Entity\BookingContact;

/**
 * Defines the access control handler for the booking contact entity type.
 */
class BookingContactAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {

    switch ($operation) {
      case 'view':
        $result = AccessResult::allowedIfHasPermission($account, 'view booking contact');
        if (!$result->isAllowed() && $this->checkAccessAlt($entity)) {
          return new AccessResultAllowed();
        }
        return $result;

      case 'update':
        $result = AccessResult::allowedIfHasPermissions($account, [
          'edit booking contact',
          'administer booking contact',
        ], 'OR');
        if (!$result->isAllowed() && $this->checkAccessAlt($entity)) {
          return new AccessResultAllowed();
        }
        return $result;

      case 'delete':
        $result = AccessResult::allowedIfHasPermissions($account, [
          'delete booking contact',
          'administer booking contact',
        ], 'OR');
        if (!$result->isAllowed() && $this->checkAccessAlt($entity)) {
          return new AccessResultAllowed();
        }
        return $result;

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
      'create booking contact',
      'administer booking contact',
    ], 'OR');
  }

  /**
   * Take a Booking Contact entity and validate against query params.
   *
   * @param \Drupal\bookable_calendar\Entity\BookingContact $entity
   *   The BookingContact to check the token against.
   *
   * @return bool
   *   Whether login_token is valid for this BookingContact.
   */
  protected function checkTokenAccess(BookingContact $entity) {
    $email = \Drupal::request()->query->get('email');
    $token = \Drupal::request()->query->get('login_token');
    if ($email && $token) {
      $valid = $entity->validateLoginToken($email, $token);
      if ($valid) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Check if tempstore allows user on this entity.
   *
   * @param \Drupal\bookable_calendar\Entity\BookingContact $entity
   *   The Booking Contact to check permissions on.
   *
   * @return bool
   *   Returns the value of the tempstore (true/false) or false.
   */
  protected function checkTempStore(BookingContact $entity) {
    $tempstore = \Drupal::service('tempstore.private');
    $store = $tempstore->get('booking_contact');
    return $store->get($entity->id());
  }

  /**
   * Check different methods whether this non logged in user has access.
   *
   * @param \Drupal\bookable_calendar\Entity\BookingContact $entity
   *   The Booking Contact to check permissions on.
   *
   * @return bool
   *   Whether this Contact can access this entity.
   */
  protected function checkAccessAlt(BookingContact $entity) {
    if ($this->checkTokenAccess($entity) || $this->checkTempStore($entity)) {
      return TRUE;
    }
    return FALSE;
  }

}
