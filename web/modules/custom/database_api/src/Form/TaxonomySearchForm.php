<?php

namespace Drupal\database_api\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Render\Element\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Database APi Taxonomy term search form.
 */
class TaxonomySearchForm extends FormBase {
  /**
   * Contains the database connection object for database handling.
   * 
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Constructs the database connection object.
   * 
   * @param \Drupal\Core\Database\Connection $connection
   *   Contains the Connection class object.
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'database_api_taxonomy_search';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['term_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Write the Term Name'),
      '#default_value' => $form_state->getValue('term_name'),
      '#description' => $this->t('Its Case Sensitive'),
      '#required' => TRUE,
    ];

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Search'),
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    try {
      $query = $this->connection->select('taxonomy_term_data', 'td');
      $query->join('taxonomy_term_field_data', 'tfd', 'td.tid = tfd.tid');
      $query->fields('td', ['tid', 'uuid']);
      $query->where('BINARY name = BINARY :term', 
        [':term' => $form_state->getValue('term_name')]);
      $result = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
    }
    catch (\Exception $e) {
      $this->messenger()->addMessage('Something wrong happened');
    }
    
    if ($result) {
      $this->messenger()->addMessage($this->t('Term @term has ID: @id', 
        ['@term' => $form_state->getValue('term_name'), '@id' => $result[0]['tid']]));
      $this->messenger()->addMessage($this->t('Term @term has UUID: @uuid', 
        ['@term' => $form_state->getValue('term_name'), '@uuid' => $result[0]['uuid']]));
      $url = '/taxonomy/term/' . $result[0]['tid'];
      $message_url = $this->t('<a href="@url">Click Here</a> to view nodes using this term', ['@url' => $url]);
      $this->messenger()->addMessage($message_url, 'status');
    }
    else {
      $form_state->setErrorByName('term_name', $this->t("Term @term doesn't exit", 
        ['@term' => $form_state->getValue('term_name')]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    
  }

}
