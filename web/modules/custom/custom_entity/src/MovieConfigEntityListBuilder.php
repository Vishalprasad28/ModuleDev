<?php

namespace Drupal\custom_entity;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of movie entity modules.
 */
final class MovieConfigEntityListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['label'] = $this->t('Label');
    $header['id'] = $this->t('Machine name');
    $header['description'] = $this->t('Description');
    $header['year'] = $this->t('Movies Released in');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['description'] = $entity->getDescription(40);
    $row['year'] = $entity->get('release_year');
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);

    if ($this->entityType->hasLinkTemplate('preview-page')) {
      $operations['preview'] = [
        'title' => $this->t('Preview'),
        'weight' => 10,
        'url' => $this->ensureDestination($entity->toUrl('preview-page')),
      ];
    }
    
    return $operations;
  }
}
