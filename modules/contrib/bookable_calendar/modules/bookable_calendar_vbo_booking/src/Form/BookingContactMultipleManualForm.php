<?php

namespace Drupal\bookable_calendar_vbo_booking\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the booking contact entity edit forms.
 */
class BookingContactMultipleManualForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'booking_contact_multiple_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#parents'] = [];
    $entity_type_manager = \Drupal::entityTypeManager();
    $entity = $entity_type_manager->getStorage('booking_contact')->create([
      'type' => 'booking_contact',
    ]);
    $form_state->set('entity', $entity);
    /** @var \Drupal\Core\Entity\Display\EntityFormDisplayInterface $form_display */
    $form_display = $entity_type_manager->getStorage('entity_form_display')->load('booking_contact.booking_contact.default');
    $form_state->set('form_display', $form_display);

    foreach ($form_display->getComponents() as $name => $component) {
      if ($name !== 'booking_instance') {
        $widget = $form_display->getRenderer($name);
        if (!$widget) {
          continue;
        }

        $items = $entity->get($name);
        $items->filterEmptyItems();
        $form[$name] = $widget->form($items, $form, $form_state);
        $form[$name]['#access'] = $items->access('edit');
      }
    }

    $form['multiple_opening_instances'] = [
      '#type' => 'textarea',
      '#description' => $this->t('Book multiple values by comma seperating them.'),
      '#title' => $this->t('Opening Instance IDs'),
      '#required' => TRUE,
    ];

    $opening_instances_query_params = \Drupal::request()->query->get('multiple_opening_instances');
    if (!is_null($opening_instances_query_params) && $opening_instances_query_params !== '') {
      $form['multiple_opening_instances']['#value'] = $opening_instances_query_params;
      $form['multiple_opening_instances']['#disabled'] = TRUE;
    }

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Book', [], ['context' => 'Claim a slot in this calendar']),
        '#button_type' => 'primary',
      ],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $entity_type_manager = \Drupal::entityTypeManager();
    $values = $form_state->getValues();
    $opening_instances = explode(',', $values['multiple_opening_instances']);

    // Loop over all opening instances and create the booking contact.
    foreach ($opening_instances as $opening_instance) {
      $new_contact = [
        'booking_instance' => [
          'target_id' => $opening_instance,
        ],
        'email' => $values['email'][0]['value'],
        'party_size' => $values['party_size'][0],
        'uid' => $values['uid'][0]['target_id'],
        'checked_in' => 0,
      ];
      $booking_contact = $entity_type_manager->getStorage('booking_contact')->create($new_contact);
      $booking_contact->save();
    }
  }

}
