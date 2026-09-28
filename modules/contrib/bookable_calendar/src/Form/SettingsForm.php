<?php

namespace Drupal\bookable_calendar\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Bookable Calendar settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bookable_calendar_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['bookable_calendar.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Add the "Browse available tokens" link to the form.
    if (\Drupal::service('module_handler')->moduleExists('token')) {
      $form['tokens'] = [
        '#theme' => 'token_tree_link',
        '#token_types' => [
          'booking_contact',
          'bookable_calendar',
        ],
        '#global_types' => TRUE,
        '#click_insert' => TRUE,
        '#show_restricted' => FALSE,
        '#recursion_limit' => 3,
        '#text' => $this->t('Browse available tokens'),
      ];
    }

    $config = $this->config('bookable_calendar.settings');
    $email_settings = $config->get('email_settings') ?? [];
    $sitewide_settings = $config->get('sitewide_settings') ?? [];

    $form['email_settings'] = [
      '#type' => 'container',
      '#tree' => TRUE,
    ];
    $form['email_settings']['admin_email'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Admin Email Notification Settings'),
      '#description' => $this->t('The emails sent to admins when bookings are created'),
      '#weight' => 1,
    ];
    $form['email_settings']['admin_email']['subject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Admin Email Subject'),
      '#default_value' => $email_settings['admin_email']['subject'] ?? 'A new booking was created for [booking_contact:calendar_title]',
    ];
    $form['email_settings']['admin_email']['body'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Admin Email body'),
      '#default_value' => $email_settings['admin_email']['body'] ?? "A new booking was created: \n [booking_contact:values]",
    ];
    $form['email_settings']['admin_email']['subject_cancel'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Admin Email Subject for cancellations'),
      '#default_value' => $email_settings['admin_email']['subject_cancel'] ?? 'A booking was cancelled for [booking_contact:calendar_title]',
    ];
    $form['email_settings']['admin_email']['body_cancel'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Admin Email body for cancellations'),
      '#default_value' => $email_settings['admin_email']['body_cancel'] ?? "The booking was cancelled: \n [booking_contact:values]",
    ];

    $form['email_settings']['user_email'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('User Email Notification Settings'),
      '#description' => $this->t('The emails sent to the user who books a calendar'),
      '#weight' => 2,
    ];
    $form['email_settings']['user_email']['subject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('User Email Subject'),
      '#default_value' => $email_settings['user_email']['subject'] ?? 'Your booking is confirmed',
    ];
    $form['email_settings']['user_email']['body'] = [
      '#type' => 'textarea',
      '#title' => $this->t('User Email body'),
      '#default_value' => $email_settings['user_email']['body'] ?? "You can view your booking here: [booking_contact:hashed_login_url] \n [booking_contact:values]",
    ];
    $form['email_settings']['user_email']['subject_cancel'] = [
      '#type' => 'textfield',
      '#title' => $this->t('User Email Subject for cancellations'),
      '#default_value' => $email_settings['user_email']['subject_cancel'] ?? 'Your booking was cancelled',
    ];
    $form['email_settings']['user_email']['body_cancel'] = [
      '#type' => 'textarea',
      '#title' => $this->t('User Email body for cancellations'),
      '#default_value' => $email_settings['user_email']['body_cancel'] ?? "Your booking was cancelled: \n [booking_contact:values]",
    ];

    $form['sitewide_settings'] = [
      '#type' => 'container',
      '#tree' => TRUE,
    ];
    $form['sitewide_settings']['max_open_bookings'] = [
      '#type' => 'number',
      '#title' => $this->t('Max Open Sitewide Bookings Per User'),
      '#description' => $this->t('Limit the amount of future bookings a single user can do sitewide, will limit regardless of individual Calendar settings. Set 0 means no limit.'),
      '#default_value' => $sitewide_settings['max_open_bookings'] ?? 0,
    ];
    $form['sitewide_settings']['one_click_booking'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable "One-Click" Booking of Openings'),
      '#description' => $this->t('If user is logged in, clicking the "Book" button will book the opening with the logged in users information but will only book a single slot site-wide. Can also be enabled on a per-calendar basis on the Calendar settings.'),
      '#default_value' => $sitewide_settings['one_click_booking'] ?? 0,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('bookable_calendar.settings')
      ->set('email_settings', $form_state->getValue('email_settings'))
      ->set('sitewide_settings', $form_state->getValue('sitewide_settings'))
      ->save();

  }

}
