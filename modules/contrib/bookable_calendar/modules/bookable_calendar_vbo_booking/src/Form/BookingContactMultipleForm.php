<?php

namespace Drupal\bookable_calendar_vbo_booking\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Common methods for Views Bulk Edit forms.
 */
trait BookingContactMultipleForm {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity repository service.
   *
   * @var \Drupal\Core\Entity\EntityRepositoryInterface
   */
  protected $entityRepository;

  /**
   * The entity field manager service.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * The time service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Builds the bundle forms.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   * @param array $bundle_data
   *   An array with all entity types and their bundles.
   *
   * @return array
   *   The bundle forms.
   */
  public function buildBundleForms(array $form, FormStateInterface $form_state, array $bundle_data) {

    // Store entity data.
    $form_state->set('bcb_entity_bundles_data', $bundle_data);

    $form['#attributes']['class'] = ['views-bulk-edit-form'];

    $bundle_count = 0;
    foreach ($bundle_data as $entity_type_id => $bundles) {
      foreach ($bundles as $bundle => $label) {
        $bundle_count++;
      }
    }

    foreach ($bundle_data as $entity_type_id => $bundles) {

      foreach ($bundles as $bundle => $label) {
        $form = $this->getBundleForm($entity_type_id, $bundle, $label, $form, $form_state, $bundle_count);
      }
    }

    return $form;
  }

  /**
   * Gets the form for this entity display.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string $bundle
   *   The bundle ID.
   * @param mixed $bundle_label
   *   Bundle label.
   * @param array $form
   *   Form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form_state object.
   * @param int $bundle_count
   *   Number of bundles that may be affected.
   *
   * @return array
   *   Edit form for the current entity bundle.
   */
  protected function getBundleForm($entity_type_id, $bundle, $bundle_label, array $form, FormStateInterface $form_state, $bundle_count) {
    $form['#parents'] = [];

    if (!isset($form[$entity_type_id])) {
      $form[$entity_type_id] = [
        '#type' => 'container',
        '#tree' => TRUE,
      ];
    }

    $entity = $this->entityTypeManager->getStorage('booking_contact')->create([
      'type' => 'booking_contact',
    ]);
    $form_state->set('entity', $entity);
    /** @var \Drupal\Core\Entity\Display\EntityFormDisplayInterface $form_display */
    $form_display = $this->entityTypeManager->getStorage('entity_form_display')->load('booking_contact.booking_contact.default');
    $form_state->set('form_display', $form_display);

    foreach ($form_display->getComponents() as $name => $component) {
      if ($name !== 'booking_instance') {
        $widget = $form_display->getRenderer($name);
        if (!$widget) {
          continue;
        }

        $items = $entity->get($name);
        $items->filterEmptyItems();
        $form[$entity_type_id][$bundle][$name] = $widget->form($items, $form, $form_state);
        $form[$entity_type_id][$bundle][$name]['#access'] = $items->access('edit');
      }
    }

    $form_display->buildForm($entity, $form[$entity_type_id][$bundle], $form_state);
    unset($form['bookable_calendar_opening_inst']['bookable_calendar_opening_inst']['booking_instance']);

    return $form;
  }

  /**
   * Save modified entity field values to action configuration.
   *
   * @param array $form
   *   Form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form_state object.
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    $storage = $form_state->getStorage();
    $bundle_data = $storage['bcb_entity_bundles_data'];

    foreach ($bundle_data as $entity_type_id => $bundles) {
      foreach ($bundles as $bundle => $label) {
        $field_data = $form_state->getValues();

        foreach ($field_data as $key => $field) {
          if ($key === 'submit' || $key === 'cancel' || $key === 'form_build_id' || $key === 'form_token' || $key === 'form_id') {
            continue;
          }
          $this->configuration[$entity_type_id][$bundle]['values'][$key] = $field;
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
    $type_id = $entity->getEntityTypeId();
    $bundle = $entity->bundle();
    $result = $this->t('No values changed');

    $entity = $this->entityRepository->getActive($type_id, $entity->id());

    if (isset($this->configuration[$type_id][$bundle])) {
      $values = $this->configuration[$type_id][$bundle]['values'];
      $new_contact = $values;
      $new_contact['booking_instance'] = $entity->id();
      $contact_storage = \Drupal::entityTypeManager()->getStorage('booking_contact');
      $contact = $contact_storage->create($new_contact);
      $contact->save();
      $result = $this->t('Created Booking Contact');
    }
    return $result;
  }

}
