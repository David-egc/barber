<?php

namespace Drupal\bookable_calendar\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the bookable calendar opening instance entity edit forms.
 */
class BookableCalendarOpeningInstanceForm extends ContentEntityForm {

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
        $this->messenger()->addStatus($this->t('New bookable calendar opening instance %label has been created.', $message_arguments));
        $this->logger('bookable_calendar')->notice('Created new bookable calendar opening instance %label', $logger_arguments);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The bookable calendar opening instance %label has been updated.', $message_arguments));
        $this->logger('bookable_calendar')->notice('Updated bookable calendar opening instance %label.', $logger_arguments);
        break;
    }

    $form_state->setRedirect('entity.bookable_calendar_opening_inst.canonical', ['bookable_calendar_opening_inst' => $entity->id()]);
  }

}
