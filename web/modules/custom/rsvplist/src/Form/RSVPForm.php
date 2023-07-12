<?php

namespace Drupal\rsvplist\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Component\Utility\EmailValidatorInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * RSVP Form Class.
 */
class RSVPForm extends FormBase {

  /**
   * @var RouteMatchInterface $route
   */
  protected RouteMatchInterface $route;

  /**
   * @var MessengerInterface $this->messenger
   */
  protected MessengerInterface $messenger;

  /**
   * @var AccountInterface $user
   */
  protected AccountInterface $user;

  /**
   * @var Connection $databaseConnection
   */
  protected Connection $databaseConnection;

  /**
   * @var TimeInterface $time
   */
  protected TimeInterface $time;

  /**
   * @var EmailValidatorInterface $emailvalidator
   */
  protected EmailValidatorInterface $emailValidator;

  /**
   * @param Drupal\Core\Routing\RouteMatchInterface $route
   *   Takes the current route object.
   * @param Drupal\Core\Messenger\MessengerInterface $messenger
   *   Takes the Messenger service object.
   * @param Drupal\Core\Session\AccountInterface $user
   *   Takes the current user object.
   * @param Drupal\Core\database\Connection $connection
   *   Takes the database connection object.
   * @param Drupal\Component\Datetime\TimeInterface $time
   *   Takes the Current timeinterface object.
   * @param Drupal\Component\Utility\EmailValidatorInterface $email_validator
   *   Takes the Email Validator object for validation purpose.
   */
  public function __construct(RouteMatchInterface $route, MessengerInterface $messenger, AccountInterface $user, Connection $connection, TimeInterface $time, EmailValidatorInterface $email_validator) {
    $this->route = $route;
    $this->messenger = $messenger;
    $this->user = $user;
    $this->databaseConnection = $connection;
    $this->time = $time;
    $this->emailValidator = $email_validator;
  }

    /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_route_match'),
      $container->get('messenger'),
      $container->get('current_user'),
      $container->get('database'),
      $container->get('datetime.time'),
      $container->get('email.validator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rsvplist_email_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    
    // Gives the fiully Loaded Node Object of the current node.
    $node = $this->route->getParameter('node');

    // Some Pages may not be node in that cae the node object will contain
    // the NULL.
    if (!(is_null($node))) {
      $nid = $node->id();
    }

    // If the Node is NULL.
    else {
      $nid = 0;
    }

    // Establishing the Form Render Array.
    // It Implements the Email Id Field
    // A Submit Buttonn and a hidden text field
    // containing the node id.
    $form['email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email Address'),
      '#size' => 30,
      '#description' => $this->t('Enter the Email Id of the User in a Correct Formate'),
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];
    $form['nid'] = [
      '#type' => 'hidden',
      '#value' => $nid,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Checking if the entered email id is valid.
    $submitted_email = $form_state->getValue('email');
    if (!$this->emailValidator->isvalid($submitted_email)) {
      $form_state->setErrorByName('email', $this->t('Provided Email Id is not Valid'));
    }

    // Checking if the given email id already exists.
    try {
      $database = $this->databaseConnection;
      $query = $database->select('rsvplist', 'r');
      $query->condition('r.mail', $form_state->getValue('email'), '=');
      $query->fields('r', ['id']);
      $result = $query->execute()->fetchAll();

      if ($result) {
        $form_state->setErrorByName('email', $this->t('Provided Email Id is already taken try another one'));
      }
    }
    catch (Exception $e) {
      $this->messenger->addMessage($this->t('Failed to varify, please try again later'));
    }

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    try {
      // Getting the Current Logged In user
      // $user = \Drupal\user\Entity::load(\Drupal::currentUser()->id());
      $uid = $this->user->id();

      // Getting the Values entered in the form.
      $nid = $form_state->getValue('nid');
      $email = $form_state->getValue('email');

      // Getting the Current time the form was submitted on.
      $current = $this->time->getRequestTime();

      // Building a Querybuilder object.
      $query = $this->databaseConnection->insert('rsvplist');

      // Listing the fields to be populated.
      $query->fields([
        'uid',
        'nid',
        'mail',
        'timestamp',
      ]);

      // Listing the values to be inserted in the above mentioned fields.
      $query->values([
        $uid,
        $nid,
        $email,
        $current,
      ]);
      $query->execute();

      // Displaying a success message after the successful submission.
      $this->messenger->addMessage($this->t('Thank You For your submission'));
    }

    catch (Exception $e) {
      $this->messenger->addMessage($this->t('Submission Failed'));
    }
  }

}
