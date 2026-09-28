<?php

namespace Drupal\bookable_calendar\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the booking entity edit forms.
 */
class BookingForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {

    $entity = $this->getEntity();
    $result = $entity->save();

    $message_arguments = ['%label' => $this->entity->toLink()->toString()];
    $logger_arguments = [
      '%label' => $this->entity->label(),
      'link' => $entity->toLink($this->t('View'))->toString(),
    ];

    switch ($result) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('New booking %label has been created.', $message_arguments));
        $this->logger('bookable_calendar')->notice('Created new booking %label', $logger_arguments);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The booking %label has been updated.', $message_arguments));
        $this->logger('bookable_calendar')->notice('Updated booking %label.', $logger_arguments);
        break;
    }

    $form_state->setRedirect('entity.booking.canonical', ['booking' => $entity->id()]);
  }

}
