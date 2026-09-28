<?php

namespace Drupal\bookable_calendar;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Provides an interface defining a bookable calendar opening entity type.
 */
interface BookableCalendarOpeningInterface extends ContentEntityInterface {

  /**
   * Gets the bookable calendar opening title.
   *
   * @return string
   *   Title of the bookable calendar opening.
   */
  public function getTitle();

  /**
   * Sets the bookable calendar opening title.
   *
   * @param string $title
   *   The bookable calendar opening title.
   *
   * @return \Drupal\bookable_calendar\BookableCalendarOpeningInterface
   *   The called bookable calendar opening entity.
   */
  public function setTitle($title);

  /**
   * Returns the bookable calendar opening status.
   *
   * @return bool
   *   TRUE if the bookable calendar opening is enabled, FALSE otherwise.
   */
  public function isEnabled();

  /**
   * Sets the bookable calendar opening status.
   *
   * @param bool $status
   *   TRUE to enable this bookable calendar opening, FALSE to disable.
   *
   * @return \Drupal\bookable_calendar\BookableCalendarOpeningInterface
   *   The called bookable calendar opening entity.
   */
  public function setStatus($status);

}
