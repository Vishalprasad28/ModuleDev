<?php 

/**
 * @file
 * It contains the Config form implememntation for the helloworld module
 */

namespace Drupal\helloworld\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class BasicConfigForm extends ConfigFormBase {

  /**
   * @var array $accepted_domains
   */
  private array $accepted_domains;

  /**
   * @var array $accepted_domain_extentions
   */
  private array $accepted_domain_extentions;

  public function __construct() {
    $this->accepted_domains = [
      'yahoo',
      'gmail',
      'outlook',
      'proton',
    ];
    $this->accepted_domain_extentions = [
      'com',
    ];
  }
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'helloworld_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return [
      'helloworld.config.form'
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    //Declaring the available options for the gender field
    $options = array(
      1 => $this->t('male'),
      2 => $this->t('female'),
      3 => $this->t('others'),
    );

    $form['full_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter Your Full Name'),
      '#description' => $this->t('First Name Should Contain only Characters'),
      '#required' => TRUE,
      '#attributes'=> [
        'id' => 'dynamic-field',
      ],
    ];
    $form['phone'] = [
      '#type' => 'tel',
      '#title' => $this->t('Enter Your Phone Number'),
      '#description' => $this->t('Enter an Indian Telephone Number'),
      '#maxlength' => 13,
      '#required' => TRUE,
      '#pattern' => '[\+][9][1][0-9]{10}$',
    ];
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Enter Your Email'),
      '#description' => $this->t('Only some Restricted Domains are allowed'),
      '#pattern' => '[a-z0-9._%+-]+@[a-z.-]+\.[a-z]{2,}$',
      '#required' => TRUE,
    ];
    $form['gender'] = [
      '#type' => 'radios',
      '#title' => $this->t('Enter Your Gender'),
      '#default_value' => 1,
      '#options' => $options,
      '#ajax' => [
        'callback' => '::dynamicNameModify',
        'disable-refocus' => FALSE,
        'event' => 'change',
        'wrapper' => 'dynamic-field',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Verifying entry..'),
        ],
      ],
      '#description' => $this->t('Select Your Gender'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    #Validation Logic Goes here
    $values = $form_state->getValues();

    if (!$this->validNameFormate($values['full_name'])) {
      $form_state->setErrorByName('full_name', $this->t('Name is in Invalid Format'));
    }
    if (!$this->validateEmailFormate($values['email'])) {
      $form_state->setErrorByName('email', $this->t('Email domain is not accepted'));
    }
    if (!$this->validatePhoneNumber($values['phone'])) {
      $form_state->setErrorByName('phone', $this->t('Phone is in invalid Formate'));
    }


  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    #Post Form Submit action goes here

    #Starting a session
    $session = \Drupal::request()->getSession();
    $user_details = $session->get('custom_user.details', array());
    
    #Storing the user details in a session
    $user_details = $form_state->getValues();
    $user_details['gender'] = $form['gender']['#options'][$form_state->getValue('gender')];
    $session->set('custom_user.details', $user_details);

    #Redirecting to the route
    $form_state->setRedirect('helloworld.simple_page');
    parent::submitForm($form, $form_state);
  }

  /**
   * @method validNameFormate()
   * 
   * @param string $val
   *   Accepts a string to validate
   * 
   * @return bool
   *   Returns True or False based on the Validation
   */
  private function validNameFormate(string $val) {
    if ($val == "" || !preg_match("/^[a-zA-Z ]+$/", $val)) {
      return FALSE;
    }
      return TRUE;
  }

  /**
   * @method validateEmailFormate()
   * 
   * @param string $email
   *   Accepts the email to be varified
   * 
   * @return bool
   *   Returns True or False based on validation
   */
  private function validateEmailFormate(string $email) {
    $domain_with_extention = explode('@', $email);
    $domain = explode('.', $domain_with_extention[1]);

    if ($email == '' || !in_array($domain[0], $this->accepted_domains, TRUE) || !in_array($domain[1], $this->accepted_domain_extentions, TRUE)) {
      return FALSE;
    }
    return TRUE;
  }
  
  /**
   * @method validatePhoneNumber()
   * 
   * @param string $phone_number
   *   Accepts the phone number to be varified
   * 
   * @return bool
   *   Returns bool based on the validation
   */
  private function validatePhoneNumber(string $phone_number) {

    if (!preg_match('/^[\+][9][1][0-9]{10}$/', $phone_number)) {
      return FALSE;
    }
    else {
      return TRUE;
    }
  }

  /**
   * @method dynamicNameModify()
   * 
   * @param array &$form
   *   Takes in the Form array
   * @param FormStateInterface $form_state
   *   Takes in the FormStateInterface Object
   * 
   * @return array
   */
  public function dynamicNameModify(array &$form, FormStateInterface $form_state) {
    $full_name = $form['full_name']['#value'];
    $gender = $form['gender']['#value'];

    if ($gender === 1) {
      $full_name = 'Mr' . $full_name;
    }
    elseif ($gender === 2) {
      $full_name = 'Miss' . $full_name;
    }
    $form['full_name']['#value'] = $full_name;

    $ajax = new AjaxResponse();
    $ajax->addCommand(new ReplaceCommand('#dynamic-field', $form['full_name']));
    return $ajax;

  }
}
