<?php

namespace Drupal\bookable_calendar\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Returns responses for Bookable Calendar routes.
 */
class BookableCalendarController extends ControllerBase {
  /**
   * Entity Type Manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Instance storage service.
   *
   * @var \Drupal\bookable_calendar\BookableCalendarOpeningInstanceStorageInterface
   */
  protected $instanceStorage;

  /**
   * Contact storage service.
   *
   * @var \Drupal\bookable_calendar\BookingContactStorageInterface
   */
  protected $contactStorage;

  /**
   * Constructs a new service instances.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
    $this->instanceStorage = $this->entityTypeManager->getStorage('bookable_calendar_opening_inst');
    $this->contactStorage = $this->entityTypeManager->getStorage('booking_contact');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

}
