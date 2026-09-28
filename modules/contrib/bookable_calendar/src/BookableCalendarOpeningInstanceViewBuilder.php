<?php

namespace Drupal\bookable_calendar;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityViewBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * View builder handler for bookable calender opening instances.
 */
class BookableCalendarOpeningInstanceViewBuilder extends EntityViewBuilder {

  /**
   * @var \Drupal\bookable_calendar\Renderer
   */
  protected Renderer $renderer;

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type): BookableCalendarOpeningInstanceViewBuilder {
    $instance = parent::createInstance($container, $entity_type);
    $instance->renderer = $container->get('bookable_calendar.renderer');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildComponents(array &$build, array $entities, array $displays, $view_mode): void {
    parent::buildComponents($build, $entities, $displays, $view_mode);

    foreach ($entities as $id => $entity) {
      $bundle = $entity->bundle();
      $display = $displays[$bundle];

      if ($display->getComponent('availability')) {
        $build[$id]['availability'] = $this->renderer->instanceAvailability($entity);
      }
      if ($display->getComponent('book_link')) {
        $build[$id]['book_link'] = $this->renderer->instanceBookLink($entity);
      }
    }
  }

}
