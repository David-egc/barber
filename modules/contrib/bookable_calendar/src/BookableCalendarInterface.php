<?php

namespace Drupal\bookable_calendar;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Provides an interface defining a bookable calendar entity type.
 */
interface BookableCalendarInterface extends ContentEntityInterface {

  /**
   * Gets the bookable calendar title.
   *
   * @return string
   *   Title of the bookable calendar.
   */
  public function getTitle();

  /**
   * Sets the bookable calendar title.
   *
   * @param string $title
   *   The bookable calendar title.
   *
   * @return \Drupal\bookable_calendar\BookableCalendarInterface
   *   The called bookable calendar entity.
   */
  public function setTitle($title);

  /**
   * Returns the bookable calendar status.
   *
   * @return bool
   *   TRUE if the bookable calendar is enabled, FALSE otherwise.
   */
  public function isEnabled();

  /**
   * Sets the bookable calendar status.
   *
   * @param bool $status
   *   TRUE to enable this bookable calendar, FALSE to disable.
   *
   * @return \Drupal\bookable_calendar\BookableCalendarInterface
   *   The called bookable calendar entity.
   */
  public function setStatus($status);

}
