<?php

namespace Drupal\bookable_calendar_vbo_booking\Plugin\Action;

use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Modify entity field values.
 *
 * @Action(
 *   id = "remove_bookings_on_opening",
 *   label = @Translation("Remove Bookings On Opening Instance"),
 *   type = "bookable_calendar_opening_inst",
 *   confirm = TRUE
 * )
 */
class RemoveBookingsOnOpening extends ViewsBulkOperationsActionBase {

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    $bookings = $entity->get('booking')->getValue();
    foreach ($bookings as $booking) {
      $loaded_booking = \Drupal::entityTypeManager()->getStorage('booking')->load($booking['target_id']);
      if ($loaded_booking) {
        $contact = $loaded_booking->contact->entity;

        $contact->delete();
      }

    }
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    $access = $object->access('update', $account, TRUE);
    return $return_as_object ? $access : $access->isAllowed();
  }

}
