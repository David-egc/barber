<?php

namespace Drupal\bookable_calendar\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for the bookable calendar entity edit forms.
 */
class BookableCalendarForm extends ContentEntityForm {

  /**
   * Turns a render array into a HTML string.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   *
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $form['party'] = [
      '#type' => 'details',
      '#title' => $this->t('Party Settings'),
      '#weight' => 9,
      '#open' => TRUE,
    ];
    $form['party']['max_party_size'] = $form['max_party_size'];
    $form['party']['max_open_bookings'] = $form['max_open_bookings'];
    $form['party']['slots_per_opening'] = $form['slots_per_opening'];
    $form['party']['book_in_progress'] = $form['book_in_progress'];
    $form['party']['slots_as_parties'] = $form['slots_as_parties'];
    $form['party']['book_in_progress'] = $form['book_in_progress'];
    $form['party']['one_click_booking'] = $form['one_click_booking'];

    unset($form['max_party_size']);
    unset($form['max_open_bookings']);
    unset($form['slots_per_opening']);
    unset($form['book_in_progress']);
    unset($form['slots_as_parties']);
    unset($form['book_in_progress']);
    unset($form['one_click_booking']);

    $form['notifications'] = [
      '#type' => 'details',
      '#title' => $this->t('Notification Settings'),
      '#weight' => 10,
    ];
    $form['notifications']['notification_email'] = $form['notification_email'];
    $form['notifications']['notification_email_override'] = $form['notification_email_override'];
    $form['notifications']['notification_email_subject'] = $form['notification_email_subject'];
    $form['notifications']['notification_email_body'] = $form['notification_email_body'];
    $form['notifications']['notification_email_subject_cancel'] = $form['notification_email_subject_cancel'];
    $form['notifications']['notification_email_body_cancel'] = $form['notification_email_body_cancel'];
    $form['notifications']['admin_notification_email'] = $form['admin_notification_email'];
    $form['notifications']['notify_email_recipient_role'] = $form['notify_email_recipient_role'];
    $form['notifications']['notify_email_recipients'] = $form['notify_email_recipients'];

    unset($form['notification_email']);
    unset($form['notification_email_override']);
    unset($form['notification_email_subject']);
    unset($form['notification_email_body']);
    unset($form['notification_email_subject_cancel']);
    unset($form['notification_email_body_cancel']);
    unset($form['admin_notification_email']);
    unset($form['notify_email_recipient_role']);
    unset($form['notify_email_recipients']);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {

    $entity = $this->getEntity();
    $result = $entity->save();
    $link = $entity->toLink($this->t('View'))->toRenderable();

    $message_arguments = ['%label' => $this->entity->label()];
    $logger_arguments = $message_arguments + ['link' => $this->renderer->render($link)];

    if ($result == SAVED_NEW) {
      $this->messenger()->addStatus($this->t('New bookable calendar %label has been created.', $message_arguments));
      $this->logger('bookable_calendar')->notice('Created new bookable calendar %label', $logger_arguments);
    }
    else {
      $this->messenger()->addStatus($this->t('The bookable calendar %label has been updated.', $message_arguments));
      $this->logger('bookable_calendar')->notice('Updated new bookable calendar %label.', $logger_arguments);
    }

    $form_state->setRedirect('entity.bookable_calendar.canonical', ['bookable_calendar' => $entity->id()]);
  }

}
