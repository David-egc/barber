<?php

namespace Drupal\bookable_calendar\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for the booking contact entity edit forms.
 */
class BookingContactForm extends ContentEntityForm {

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
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {

    $entity = $this->getEntity();
    $result = $entity->save();
    $link = $entity->toLink($this->t('View'))->toRenderable();

    $message_arguments = ['%label' => $this->entity->label()];
    $logger_arguments = $message_arguments + ['link' => $this->renderer->render($link)];

    if ($result == SAVED_NEW) {
      $this->messenger()->addStatus($this->t('New booking contact %label has been created.', $message_arguments));
      $this->logger('bookable_calendar')->notice('Created new booking contact %label', $logger_arguments);
    }
    else {
      $this->messenger()->addStatus($this->t('The booking contact %label has been updated.', $message_arguments));
      $this->logger('bookable_calendar')->notice('Updated new booking contact %label.', $logger_arguments);
    }

    $form_state->setRedirect('entity.booking_contact.canonical', ['booking_contact' => $entity->id()]);
  }

}
