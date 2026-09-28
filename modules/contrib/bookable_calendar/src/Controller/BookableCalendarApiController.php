<?php

namespace Drupal\bookable_calendar\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\MessageCommand;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Url;
use Drupal\Core\Database\Connection;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\bookable_calendar\Entity\BookingContact;
use Drupal\bookable_calendar\Entity\BookableCalendar;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\bookable_calendar\Entity\BookableCalendarOpeningInstance;

/**
 * Returns responses for Bookable Calendar routes.
 */
class BookableCalendarApiController extends ControllerBase {
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
   * Database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs a new service instances.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Entity type manager.
   * @param \Drupal\Core\Database\Connection $database
   *   Database connection.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, Connection $database) {
    $this->entityTypeManager = $entityTypeManager;
    $this->instanceStorage = $this->entityTypeManager->getStorage('bookable_calendar_opening_inst');
    $this->contactStorage = $this->entityTypeManager->getStorage('booking_contact');
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('database')
    );
  }

  /**
   * Get passed an OpeniningInstance and user info and "book" that spot.
   */
  public function bookAjax(BookableCalendarOpeningInstance $opening_instance, Request $req): AjaxResponse {
    $response = new AjaxResponse();
    $data = $this->doBook($opening_instance, $req);
    $success = $data['status'] === 'success';
    if ($data['message'] !== NULL) {
      $type = $success ? 'status' : 'error';
      $response->addCommand(new MessageCommand($data['message'], NULL, ['type' => $type]));
    }
    $this->messenger()->deleteAll();
    if (!$success) {
      $response->setStatusCode(201);
    }
    return $response;
  }

  /**
   *
   */
  public function cancelAjax(BookableCalendarOpeningInstance $opening_instance, Request $req): AjaxResponse {
    $response = new AjaxResponse();
    $errors = 0;
    foreach ($opening_instance->getBookingContactsByCurrentUser() as $bookingContact) {
      try {
        $bookingContact->delete();
      }
      catch (EntityStorageException $e) {
        $response->addCommand(new MessageCommand('Booking contact ' . $bookingContact->id() . ' can not be deleted.', NULL, ['type' => 'error']));
        $errors++;
      }
    }
    if ($errors) {
      $response->setStatusCode(201);
    }
    else {
      $response->addCommand(new MessageCommand('Booking successfully cancelled.', NULL, ['type' => 'status']));
    }
    return $response;
  }

  /**
   * Get passed an OpeniningInstance and user info and "book" that spot.
   */
  public function book(BookableCalendarOpeningInstance $opening_instance, Request $req): JsonResponse {
    return new JsonResponse($this->doBook($opening_instance, $req));
  }

  /**
   * Helper function for booking through the API or with Ajax.
   *
   * @return array
   */
  protected function doBook(BookableCalendarOpeningInstance $opening_instance, Request $req): array {
    // Allow for both POST body and form-data.
    $post_body = $req->getContent();
    $post_form_data = $req->request->all();
    if ($post_body || $post_form_data) {
      if ($post_body) {
        $body = (array) json_decode($post_body);
        $contact = (array) $body['contact_info'];
      }
      else {
        $contact = $post_form_data;
      }

      $contact['booking_instance'] = [
        'target_id' => $opening_instance->id(),
      ];

      if (isset($contact['email']) && isset($contact['party_size'])) {
        $new_contact = $this->contactStorage->create($contact);
        $violations = $new_contact->validate();

        if ($violations->count() > 0) {
          $validation_errors = [];
          foreach ($violations as $violation) {
            $validation_errors[] = $violation->getMessage();
          }
          return [
            'status' => 'failed',
            'message' => 'There was some validation errors:<br/>' . implode('<br/>', $validation_errors),
          ];
        }
        else {
          $new_contact->save();
          $sucess_message = $opening_instance->getSuccessMessage();
          return [
            'status' => 'success',
            'message' => $sucess_message,
          ];
        }
      }
      else {
        return [
          'status' => 'failed',
          'message' => 'not all required parameters were provided',
        ];
      }
    }
    else {
      return [
        'status' => 'failed',
        'message' => 'No request body sent',
      ];
    }
  }

  /**
   * Get passed an array of OpeniningInstances and user info and "book" that spot.
   *
   * @param \Symfony\Component\HttpFoundation\Request $req
   *
   * @return void
   */
  public function bookMultiple(Request $req) {
    $post_body = $req->getContent();
    if ($post_body) {
      $body = (array) json_decode($post_body);
      $contact = (array) $body['contact_info'];

      if (!isset($contact['email']) && !isset($contact['party_size'])) {
        return new JsonResponse([
          'status' => 'failed',
          'message' => 'not all required parameters were provided',
        ]);
      }

      $booking_data = [
        'errors' => 0,
        'data' => [],
      ];
      foreach ($body['opening_instances'] as $opening_instance) {
        $contact['booking_instance'] = [
          'target_id' => $opening_instance,
        ];

        $new_contact = $this->contactStorage->create($contact);
        $violations = $new_contact->validate();

        if ($violations->count() > 0) {
          $booking_data['errors'] += $violations->count();
          $booking_data['status'] = 'failed';
          $validation_errors = [];
          foreach ($violations as $violation) {
            $validation_errors[] = $violation->getMessage();
          }
          $booking_data['data'][$opening_instance] = [
            'status' => 'failed',
            'message' => 'There was some validation errors',
            'data' => $validation_errors,
          ];
        }
        else {
          $new_contact->save();
          $booking_data['data'][$opening_instance] = [
            'status' => 'success',
          ];
        }
      }
      if ($booking_data['errors'] === 0) {
        $booking_data['status'] = 'success';
      }
      return new JsonResponse($booking_data);
    }
    else {
      return new JsonResponse([
        'status' => 'failed',
        'message' => 'No request body sent',
      ]);
    }
  }

  /**
   * Get all bookines for a given Calendar and time range.
   *
   * To deine a custom date range pass in
   * as query params
   * 'start' and 'end' both anything that strtotime will understand.
   *
   * @param \Drupal\bookable_calendar\Entity\BookableCalendar $bookable_calendar
   *   The Bookable Calendar we are getting Bookings for.
   * @param \Symfony\Component\HttpFoundation\Request $req
   *   The Request Object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Json of every booking.
   */
  public function getBookings(BookableCalendar $bookable_calendar, Request $req) {
    $date_start = strtotime('today');
    $date_end = strtotime('tomorrow');
    $params = $req->query->all();

    if ($params) {
      if (isset($params['start'])) {
        $date_start = strtotime($params['start']);
      }
      if (isset($params['end'])) {
        $date_end = strtotime($params['end']);
      }
    }
    $opening_storage = $this->entityTypeManager->getStorage('bookable_calendar_opening');
    $opening_array = [];
    $openings = $opening_storage->loadByProperties([
      'bookable_calendar' => [
        'target_id' => $bookable_calendar->id(),
      ],
    ]);
    foreach ($openings as $opening) {
      $opening_array[] = $opening->id();
    }

    $database = $this->database;
    $query = $database->select('bookable_calendar_opening_inst', 'instance');
    $query->condition('instance.booking_opening', $opening_array, 'IN');
    $query->condition('instance.date__value', $date_start, '>=');
    $query->condition('instance.date__end_value', $date_end, '<=');
    $query->fields('instance', [
      'booking_opening',
      'date__value',
      'date__end_value',
      'id',
    ]);
    $todays_instances = $query->execute()->fetchAll();

    $contactStorage = $this->entityTypeManager->getStorage('booking_contact');
    $booking_storage = $this->entityTypeManager->getStorage('booking');
    $rows = [];
    foreach ($todays_instances as $instance) {
      $contacts = $contactStorage->loadByProperties([
        'booking_instance' => [
          'target_id' => $instance->id,
        ],
      ]);
      foreach ($contacts as $contact) {
        $row_contact = [
          'id' => $contact->id(),
          'checked_in' => (bool) $contact->checked_in->value,
          'email' => $contact->email->value,
          'date' => date('D, m/d/Y - g:ia', $instance->date__value),
          'party_size' => $contact->party_size->value,
        ];
        if ($contact->booking->target_id) {
          $booking = $booking_storage->load($contact->booking->target_id);
          $row_contact['created'] = date('D, m/d/Y - g:ia', $booking->created->value);
        }
        $rows[] = $row_contact;
      }
    }
    // Sort results by dates.
    $dates = array_column($rows, 'date');
    array_multisort($dates, SORT_ASC, $rows);
    return new JsonResponse([
      'data' => $rows,
    ]);
  }

  /**
   * Take a Contact and "Check Them In".
   *
   * @param \Drupal\bookable_calendar\Entity\BookingContact $booking_contact
   *   The Booking Contact.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A Success Message.
   */
  public function checkIn(BookingContact $booking_contact) {
    $booking_contact->set('checked_in', TRUE);
    $booking_contact->save();
    return new JsonResponse([
      'status' => 'success',
      'message' => $booking_contact->id() . ' successfully checked in',
    ]);
  }

  /**
   * Take a Contact and "Check Them Out".
   *
   * @param \Drupal\bookable_calendar\Entity\BookingContact $booking_contact
   *   The Booking Contact You want to "check Out".
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A Success Message
   */
  public function checkOut(BookingContact $booking_contact) {
    $booking_contact->set('checked_in', FALSE);
    $booking_contact->save();
    return new JsonResponse([
      'status' => 'success',
      'message' => $booking_contact->id() . ' successfully checked out',
    ]);
  }

  /**
   * Get Passed in a Calendar and show the opening times.
   *
   * In format that works with fullcalendar_block.
   *
   * @param \Drupal\bookable_calendar\Entity\BookableCalendar $bookable_calendar
   *   The Bookable Calendar you want openings for.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Json of all openings
   */
  public function getOpenings(BookableCalendar $bookable_calendar) {
    $opening_storage = $this->entityTypeManager->getStorage('bookable_calendar_opening');
    $instanceStorage = $this->entityTypeManager->getStorage('bookable_calendar_opening_inst');
    $openings = $opening_storage->loadByProperties([
      'bookable_calendar' => [
        'target_id' => $bookable_calendar->id(),
      ],
    ]);
    $opening_array = [];
    foreach ($openings as $opening) {
      $opening_instance = $instanceStorage->loadByProperties([
        'booking_opening' => [
          'target_id' => $opening->id(),
        ],
      ]);
      foreach ($opening_instance as $instance) {
        $path = Url::fromRoute('entity.bookable_calendar_opening_inst.canonical', [
          'bookable_calendar_opening_inst' => $instance->id(),
        ])->toString();
        $opening_array[] = [
          'title' => $bookable_calendar->title->value,
          'start' => date('Y-m-d H:i:s', $instance->date->value),
          'end' => date('Y-m-d H:i:s', $instance->date->end_value),
          'url' => $path,
        ];
      }
    }
    return new JsonResponse(
      $opening_array
    );
  }

}
