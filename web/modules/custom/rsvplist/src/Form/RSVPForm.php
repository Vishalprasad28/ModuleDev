<?php

namespace Drupal\rsvplist\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @package \Drupal\rsvplist\Form\RSVPForm
 *   RSVP Form Class.
 */
class RSVPForm extends FormBase {

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
    $node = \Drupal::routeMatch()->getParameter('node');

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
    if (!(\Drupal::service('email.validator')->isvalid($submitted_email))) {
      $form_state->setErrorByName('email', $this->t('Provided Email Id is not Valid'));
    }

    // Checking if the given email id already exists.
    try {
      $database = \Drupal::database();
      $query = $database->select('rsvplist', 'r');
      $query->condition('r.mail', $form_state->getValue('email'), '=');
      $query->fields('r', ['id']);
      $result = $query->execute()->fetchAll();

      if ($result) {
        $form_state->setErrorByName('email', $this->t('Provided Email Id is already taken try another one'));
      }
    }
    catch (\Exception $e) {
      \Drupal::messenger()->addMessage($this->t('Failed to varify, please try again later'));
    }

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // $submitted_email = $form_state->getValue('email');
    // $this->messenger()->addMessage($this->$this->t('The Email Id that
    // you entered is @email',
    // ['@email' => $submitted_email]));
    try {
      // Getting the Current Logged In user
      // $user = \Drupal\user\Entity::load(\Drupal::currentUser()->id());
      $uid = \Drupal::currentUser()->id();

      // Getting the Values entered in the form.
      $nid = $form_state->getValue('nid');
      $email = $form_state->getValue('email');

      // Getting the Current time the form was submitted on.
      $current = \Drupal::time()->getRequestTime();

      // Building a Querybuilder object.
      $query = \Drupal::database()->insert('rsvplist');

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
      \Drupal::messenger()->addMessage($this->t('Thank You For your submission'));
    }

    catch (\Exception $e) {
      \Drupal::messenger()->addMessage($this->t('Submission Failed'));
    }
  }

}
