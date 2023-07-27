<?php

namespace Drupal\custom_entity\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\custom_entity\Entity\MovieCinfigEntity;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Movie Entity module form.
 */
final class MovieConfigEntityForm extends EntityForm {

  /**
   * Contains the Entity Manager object to manage the entities being created.
   * 
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityManager;

  /**
   * Constructs the entity manager dependency.
   * 
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_manager
   *   Contains the EntityTypeManager Object to handle the different entities
   *   accross the site.
   */
  public function __construct(EntityTypeManagerInterface $entity_manager) {
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }
  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $this->entity->label(),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $this->entity->id(),
      '#machine_name' => [
        'exists' => [$this, 'exist'],
      ],
      '#disabled' => !$this->entity->isNew(),
    ];

    $form['status'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enabled'),
      '#default_value' => $this->entity->status(),
    ];

    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $this->entity->get('description'),
    ];

    $form['release_year'] = [
      '#type' => 'number',
      '#title' => $this->t('Release Year of the Movie'),
      '#description' => $this->t('Enter the release year of the movie'),
      '#default_value' => $this->entity->get('release_year'),
      '#ajax' => [
        'callback' => '::fetchMovieByYear',
        'focus' => TRUE,
        'event' => 'change',
        'wrapper' => 'movie-list',
      ],
    ];

    $form['movies'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Movie Ids of Movies released on given year'),
      '#attributes' => [
        'id' => 'movie-list',
        'readonly' => 'readonly',
      ],
      '#default_value' => $this->entity->get('movies'),
    ];

    $form['update_button'] = [
      '#type' => 'button',
      '#value' => $this->t('Update Movie Ids'),
      '#ajax' => [
        'callback' => '::fetchMovieByYear',
        'focus' => TRUE,
        'event' => 'click',
        'wrapper' => 'movie-list',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('release_year') < 1880 || $form_state->getValue('release_year') > 3000) {
      $form_state->setErrorByName('release_year', $this->t('Year value is outside the range'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    $result = parent::save($form, $form_state);
    $message_args = ['%label' => $this->entity->label()];
    $this->messenger()->addStatus(
      match($result) {
        \SAVED_NEW => $this->t('Created new example %label.', $message_args),
        \SAVED_UPDATED => $this->t('Updated example %label.', $message_args),
      }
    );
    $form_state->setRedirectUrl($this->entity->toUrl('collection'));
    return $result;
  }

  /**
   * Helper function to check whether an configuration entity exists.
   * 
   * @param string $id
   *   Contains the entity id to be checked.
   * 
   * @return bool
   *   Returns Bool based on whethere the entity already exists or not.
   */
  public function exist(string $id) {
    $entity = $this->entityTypeManager->getStorage('movie_config_entity')->getQuery()
      ->condition('id', $id)
      ->execute();
    return (bool) $entity;
  }

  /**
   * Ajax Function to fetch all the movies that has release year as mentioned.
   * 
   * @param array $form
   *   Takes the form render array of the entity.
   * @param FormStateInterface $form_state
   *   Takes the FoemState Object of the form to get the stored values in
   *   fields.
   * 
   * @return AjaxResponse
   *   Returns the Ajax Response after procession the form data.
   */
  public function fetchMovieByYear(array $form, FormStateInterface $form_state) {

    // Getting the entity storage of node.
    $query= $this->entityManager->getStorage('node')->getQuery();

    // Targetting a specific bundle {movie}.
    $query->condition('type', 'movie')->accessCheck(FALSE);
    $ids = $query->execute();
    $entities = $this->entityManager->getStorage('node')->loadMultiple($ids);
    $array_of_entity_urls = [];

    // Looping through all the entities.
    foreach ($entities as $id => $entity) {
      if ((int) date('Y', $entity->getCreatedTime()) == $form_state->getValue('release_year')) {
        array_push($array_of_entity_urls, $id);
      }
    }
    $string_formate = implode(',', $array_of_entity_urls);
    $response = new AjaxResponse();
    $response->addCommand(new InvokeCommand('#movie-list', 'val', ['']));
    $response->addCommand(new InvokeCommand('#movie-list', 'val', [$string_formate]));
    return $response;
  }

}
