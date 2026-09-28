<?php

namespace Drupal\bookable_calendar\Plugin\views\field;

use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * A handler to provide proper displays for opening instance book link.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("bookable_calendar_book_link")
 */
class BookLink extends FieldPluginBase {

  /**
   *
   */
  public function render(ResultRow $values) {
    $link = \Drupal::service('bookable_calendar.renderer')->instanceBookLink($values->_entity);
    $result = $this->renderer->render($link);
    return $result;
  }

  /**
   *
   */
  public function query() {}

}
