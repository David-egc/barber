<?php

namespace Drupal\bookable_calendar;

use Drupal\Core\Database\Connection;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;

/**
 *
 */
class Renderer {

  use StringTranslationTrait;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected AccountInterface $currentUser;

  /**
   * @var \Drupal\Core\Database\Connection
   */
  protected Connection $connection;

  /**
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected LanguageManagerInterface $languageManager;

  /**
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected DateFormatterInterface $dateFormatter;

  /**
   * Creates a new ModerationInformation instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager service.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   *   The language manager.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $dateFormatter
   *   The date formatter service.
   */
  public function __construct(
    EntityTypeManagerInterface $entityTypeManager,
    AccountInterface $current_user,
    Connection $connection,
    LanguageManagerInterface $languageManager,
    DateFormatterInterface $dateFormatter
  ) {
    $this->entityTypeManager = $entityTypeManager;
    $this->currentUser = $current_user;
    $this->connection = $connection;
    $this->languageManager = $languageManager;
    $this->dateFormatter = $dateFormatter;
  }

  /**
   *
   */
  public function instances(BookableCalendarInterface $calendar): array {
    $booking_lead_time = $calendar->get('booking_lead_time')->value;
    $booking_future_time = $calendar->get('booking_future_time')->value;
    $book_in_progress = $calendar->get('book_in_progress')->value;
    if (is_null($booking_future_time)) {
      $booking_future_time = '+ 1 year';
    }
    $query = $this->connection->select('bookable_calendar__calendar_openings', 'cal');
    $query->fields('cal', [
      'entity_id',
      'calendar_openings_target_id',
    ]);
    $query->condition('cal.entity_id', $calendar->id());
    $query->leftJoin('bookable_calendar_opening_inst', 'inst', 'inst.booking_opening = cal.calendar_openings_target_id');
    if ($book_in_progress) {
      $query->condition('inst.date__end_value', strtotime($booking_lead_time), '>');
    }
    else {
      $query->condition('inst.date__value', strtotime($booking_lead_time), '>');
    }
    $query->condition('inst.date__end_value', strtotime($booking_future_time), '<=');
    $query->fields('inst', [
      'booking_opening',
      'id',
      'date__value',
    ]);
    $query->orderBy('date__value');
    $query->range(0, 100);
    $opening_instances = $query->execute()->fetchAllAssoc('id');
    $result = [];
    if ($opening_instances) {
      $instances = array_keys($opening_instances);
      $loaded_instances = $this->entityTypeManager->getStorage('bookable_calendar_opening_inst')->loadMultiple($instances);
      $date_formatter = DateFormatter::getDateFormatSettings();
      $language = $this->languageManager->getCurrentLanguage()->getId();

      foreach ($loaded_instances as $key => $instance) {
        $instance_start_date = $this->dateFormatter->format($instance->get('date')->value, 'custom', $date_formatter['date_format'], NULL, $language);
        $result[$instance_start_date][$key] = $this->entityTypeManager->getViewBuilder('bookable_calendar_opening_inst')->view($instance);
      }
      foreach ($result as $date => $items) {
        $result[$date]['#markup'] = '<h2>' . $date . '</h2><div class="bookable-calendar-opening-instance-key"><div class="field__label">' . $this->t('Time') . '</div><div class="field__label">' . $this->t('Slots Available') . '</div><div></div></div>';
      }
    }
    return $result;
  }

  /**
   *
   */
  public function instanceAvailability(BookableCalendarOpeningInstanceInterface $instance): array {
    $result = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'availability',
        ],
      ],
    ];
    $slots_available = $instance->slotsAvailable();
    $max_slots = $instance->maxSlotsAvailable();
    $remaining_percent = $slots_available / $max_slots;
    if ($remaining_percent <= 0) {
      $result['#attributes']['class'][] = 'at-capacity';
    }
    elseif ($remaining_percent < 0.25) {
      $result['#attributes']['class'][] = 'near-capacity';
    }
    elseif ($remaining_percent <= 0.5) {
      $result['#attributes']['class'][] = 'limited';
    }
    $result['seats_available'] = [
      '#title' => $this->t('Seats Available'),
      '#type' => 'text',
      '#markup' => '<div class="availability__seats-available">' . $slots_available . '</div>',
    ];
    $result['divider'] = [
      '#type' => 'text',
      '#markup' => '<div class="availability__divider">/</div>',
    ];
    $result['max_seats_available'] = [
      '#title' => $this->t('Max Seats Available'),
      '#type' => 'text',
      '#markup' => '<div class="availability__max-seats-available">' . $max_slots . '</div>',
    ];
    return $result;
  }

  /**
   *
   */
  public function instanceBookLink(BookableCalendarOpeningInstanceInterface $instance): array {
    $slots_available = $instance->slotsAvailable();
    $booked_by_current_user = $instance->isBookedByCurrentUser();
    $url = Url::fromRoute('bookable_calendar.booking_contact.create', [
      'opening_instance' => $instance->id(),
    ]);
    $result = [
      '#title' => $this->t('Book', [], ['context' => 'Claim a slot in this calendar']),
      '#type' => 'link',
      '#url' => $url,
      '#attributes' => [
        'class' => [
          'availability__link',
          'button',
          'button--small',
        ],
        'data-max-party-size' => $instance->maxPartySize(),
        'data-opening-instance' => $instance->id(),
        'data-slots-as-parties' => $instance->slotsAsParties(),
        'data-slots-available' => $slots_available,
        'data-existing-booking' => $booked_by_current_user ? 1 : 0,
      ],
    ];
    if ($slots_available === 0) {
      $result['#title'] = $this->t('No Slots Available');
      $result['#attributes']['class'][] = 'fully-booked';
      if (!$booked_by_current_user) {
        $result['#attributes']['class'][] = 'is-disabled';
      }
    }
    if ($booked_by_current_user) {
      $result['#attributes']['class'][] = 'booked-by-me';
    }
    return $result;
  }

  /**
   *
   */
  public function addOneClickBooking(array &$build, BookableCalendarInterface $instance): void {
    if ($instance->oneClickBookingsEnabled() && $this->currentUser->isAuthenticated()) {
      $build['#attached']['library'][] = 'bookable_calendar/one_click_booking';
      $build['#attached']['drupalSettings']['one_click_booking'] = [
        'uid' => $this->currentUser->id(),
        'email' => $this->currentUser->getEmail(),
        'booked_text' => $this->t('Booked', [], ['context' => 'This slot has already been claimed']),
        'available_text' => $this->t('Available'),
      ];
      $build['#cache']['contexts'][] = 'user';
    }
  }

}
