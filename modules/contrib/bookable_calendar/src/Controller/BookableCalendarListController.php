<?php

namespace Drupal\bookable_calendar\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

/**
 * Returns responses for Bookable Calendar routes.
 */
class BookableCalendarListController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $header = [
      [
        'data' => $this->t('Name'),
        'class' => [RESPONSIVE_PRIORITY_MEDIUM],
        'field' => 'name',
      ],
      [
        'data' => $this->t('Description'),
        'class' => [RESPONSIVE_PRIORITY_MEDIUM],
        'field' => 'description',
      ],
      [
        'data' => $this->t('Operations'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
      ],
    ];

    $rows = [
      [
        'Bookable Calendar',
        'The main Bookable Calendar Entity',
        [
          'data' => [
            '#type' => 'operations',
            '#links' => [
              'manage_fields' => [
                'title' => $this->t('Manage Fields'),
                'url' => Url::fromUri('internal:/admin/structure/bookable-calendar/bookable-calendar/fields'),
              ],
              'manage_form_display' => [
                'title' => $this->t('Manage Form Display'),
                'url' => Url::fromUri('internal:/admin/structure/bookable-calendar/bookable-calendar/form-display'),
              ],
              'manage_display' => [
                'title' => $this->t('Manage Display'),
                'url' => Url::fromUri('internal:/admin/structure/bookable-calendar/bookable-calendar/display'),
              ],
            ],
          ],
        ],
      ],
      [
        'Bookable Calendar Opening',
        'Each Calendar Opening that attaches to a Bookable Calendar',
        [
          'data' => [
            '#type' => 'operations',
            '#links' => [
              'manage_fields' => [
                'title' => $this->t('Manage Fields'),
                'url' => Url::fromUri('internal:/admin/structure/bookable-calendar/bookable-calendar-opening/fields'),
              ],
              'manage_form_display' => [
                'title' => $this->t('Manage Form Display'),
                'url' => Url::fromUri('internal:/admin/structure/bookable-calendar/bookable-calendar-opening/form-display'),
              ],
              'manage_display' => [
                'title' => $this->t('Manage Display'),
                'url' => Url::fromUri('internal:/admin/structure/bookable-calendar/bookable-calendar-opening/display'),
              ],
            ],
          ],
        ],
      ],
      [
        'Booking Contact',
        'The contact of the user who registered a booking',
        [
          'data' => [
            '#type' => 'operations',
            '#links' => [
              'manage_fields' => [
                'title' => $this->t('Manage Fields'),
                'url' => Url::fromUri('internal:/admin/structure/bookable-calendar/booking-contact/fields'),
              ],
              'manage_form_display' => [
                'title' => $this->t('Manage Form Display'),
                'url' => Url::fromUri('internal:/admin/structure/bookable-calendar/booking-contact/form-display'),
              ],
              'manage_display' => [
                'title' => $this->t('Manage Display'),
                'url' => Url::fromUri('internal:/admin/structure/bookable-calendar/booking-contact/display'),
              ],
            ],
          ],
        ],
      ],
      [
        'Booking',
        'The individual booking linking a Contact to a Opening Instance',
        [
          'data' => [
            '#type' => 'operations',
            '#links' => [
              'manage_fields' => [
                'title' => $this->t('Manage Fields'),
                'url' => Url::fromUri('internal:/admin/structure/bookable-calendar/booking/fields'),
              ],
              'manage_form_display' => [
                'title' => $this->t('Manage Form Display'),
                'url' => Url::fromUri('internal:/admin/structure/bookable-calendar/booking/form-display'),
              ],
              'manage_display' => [
                'title' => $this->t('Manage Display'),
                'url' => Url::fromUri('internal:/admin/structure/bookable-calendar/booking/display'),
              ],
            ],
          ],
        ],
      ],
      [
        'Bookable Calendar Opening Instance',
        'The individual opening instance of each Bookable Calendar Opening',
        [
          'data' => [
            '#type' => 'operations',
            '#links' => [
              'manage_fields' => [
                'title' => $this->t('Manage Fields'),
                'url' => Url::fromUri('internal:/admin/structure/bookable-calendar/bookable-calendar-opening-instance/fields'),
              ],
              'manage_form_display' => [
                'title' => $this->t('Manage Form Display'),
                'url' => Url::fromUri('internal:/aadmin/structure/bookable-calendar/bookable-calendar-opening-instance/form-display'),
              ],
              'manage_display' => [
                'title' => $this->t('Manage Display'),
                'url' => Url::fromUri('internal:/admin/structure/bookable-calendar/bookable-calendar-opening-instance/display'),
              ],
            ],
          ],
        ],
      ],
    ];

    $build['content'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    ];

    return $build;
  }

}
