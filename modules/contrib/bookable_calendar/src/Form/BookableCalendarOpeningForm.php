<?php

namespace Drupal\bookable_calendar\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for the bookable calendar opening entity edit forms.
 */
class BookableCalendarOpeningForm extends ContentEntityForm {

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

    // Manage all the instances for this opening in a batch.
    $dates_open = $entity->get('date')->getValue();
    $existing_instances = $entity->get('booking_instance')->getValue();

    // Grab each open date add an Instance for it.
    $operations = [
      [
        'bookable_calendar_process_opening_instances',
        [[
          'dates_open' => $dates_open,
          'bookable_cal_opening' => $entity->id(),
          'existing_instances' => $existing_instances,
          'original_instances' => array_flip(array_column($existing_instances, 'target_id')),

        ],
        ],
      ],
    ];
    $batch = [
      'title' => $this->t('Processing @num Opening Instances', ['@num' => count($dates_open)]),
      'operations' => $operations,
      'finished' => 'bookable_calendar_process_opening_instances_finished',
      'init_message' => $this->t('Processing Opening Instances.'),
      'progress_message' => $this->t('Processed @current out of @total.'),
      'error_message' => $this->t('Processing Opening Instances has encountered an error.'),
    ];
    batch_set($batch);

    $link = $entity->toLink($this->t('View'))->toRenderable();

    $message_arguments = ['%label' => $this->entity->label()];
    $logger_arguments = $message_arguments + ['link' => $this->renderer->render($link)];

    if ($result == SAVED_NEW) {
      $this->messenger()->addStatus($this->t('New bookable calendar opening %label has been created.', $message_arguments));
      $this->logger('bookable_calendar')->notice('Created new bookable calendar opening %label', $logger_arguments);
    }
    else {
      $this->messenger()->addStatus($this->t('The bookable calendar opening %label has been updated.', $message_arguments));
      $this->logger('bookable_calendar')->notice('Updated new bookable calendar opening %label.', $logger_arguments);
    }

    $form_state->setRedirect('entity.bookable_calendar_opening.canonical', ['bookable_calendar_opening' => $entity->id()]);
  }

}
