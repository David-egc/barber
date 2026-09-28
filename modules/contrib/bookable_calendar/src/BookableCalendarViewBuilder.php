<?php

namespace Drupal\bookable_calendar;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityViewBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * View builder handler for bookable calender.
 */
class BookableCalendarViewBuilder extends EntityViewBuilder {

  /**
   * @var \Drupal\bookable_calendar\Renderer
   */
  protected Renderer $renderer;

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type): BookableCalendarViewBuilder {
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

      if ($display->getComponent('instances')) {
        $build[$id]['instances'] = $this->renderer->instances($entity);
      }
    }
  }

}
