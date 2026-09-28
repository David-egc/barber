<?php

namespace Drupal\bookable_calendar;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Provides a list controller for the bookable calendar opening instance.
 */
class BookableCalendarOpeningInstanceListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build['table'] = parent::render();

    $total = $this->getStorage()
      ->getQuery()
      ->count()
      ->execute();

    $build['summary']['#markup'] = $this->t('Total bookable calendar opening instances: @total', ['@total' => $total]);
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('ID');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var BookableCalendarOpeningInstanceInterface $entity */
    $row['id'] = $entity->toLink();
    return $row + parent::buildRow($entity);
  }

}
