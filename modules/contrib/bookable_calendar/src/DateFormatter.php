<?php

namespace Drupal\bookable_calendar;

use Drupal\smart_date\SmartDateTrait;

/**
 * Wrapper around the Smart Date Date Formatter as needed.
 */
class DateFormatter {

  use SmartDateTrait;

  /**
   * Take a start/end date and format it matching the Smart Date Formatter.
   *
   * @param string $start_ts
   *   Start date timestamp.
   * @param string $end_ts
   *   End date timestamp.
   *
   * @return string
   *   Formatted date string.
   */
  public static function formatDateToBooking($start_ts, $end_ts) {
    $options = self::getDateFormatSettings();
    $timezone = date_default_timezone_get();
    $date = self::formatSmartDate($start_ts, $end_ts, $options, $timezone);
    $output = '';
    foreach ($date as $value) {
      foreach ($value as $value1) {
        if (is_array($value1) && array_key_exists('#markup', $value1)) {
          $output .= $value1['#markup'];
        }
        elseif (is_array($value1) && array_key_exists('value', $value1) && array_key_exists('#markup', $value1['value'])) {
          $output .= $value1['value']['#markup'];
        }
        elseif (is_string($value1)) {
          $output .= $value1;
        }
      }
    }
    return $output;
  }

  /**
   * Get date format options set on Booking Entity display.
   *
   * @return array
   *   date format options.
   */
  public static function getDateFormatSettings() {
    $service = \Drupal::service('entity_display.repository');
    $view_display = $service->getViewDisplay('booking', 'booking', 'default');
    $view_options = $view_display->get('content')['booking_date'];
    if (!is_null($view_options)) {
      $smart_date_format = \Drupal::entityTypeManager()->getStorage('smart_date_format')
        ->load($view_options['settings']['format']);
      $options = $smart_date_format->getOptions();
    }
    else {
      $options = [
        'date_format' => 'l, F j, Y - H:i',
      ];
    }
    return $options;
  }

}
