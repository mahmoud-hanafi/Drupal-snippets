<?php

// routing info
// code_user.change_email:
//   path: '/edit/change-email'
//   defaults:
//     _title: 'Enter your new email'
//     _form: 'Drupal\code_user\Form\ChangeEmailForm'
//   requirements:
//     _role: 'authenticated'

namespace Drupal\code_user\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\UserStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Component\Utility\EmailValidator;
use Drupal\Core\Flood\FloodInterface;
use Drupal\Core\Config\ConfigFactory;

/**
 * Provides a Code MCIT User form.
 */
class ChangeEmailForm extends FormBase {

  /**
   * The flood service.
   *
   * @var \Drupal\Core\Flood\FloodInterface
   */
  protected $flood;

  /**
   * The current user service.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The user storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * The email validator service.
   *
   * @var Drupal\Component\Utility\EmailValidator
   */
  protected $emailValidator;

  /**
   * ChangeEmailForm constructor.
   */
  public function __construct(ConfigFactory $config_factory, FloodInterface $flood, AccountProxyInterface $current_user, UserStorageInterface $user_storage, EmailValidator $email_validator) {
    $this->configFactory = $config_factory;
    $this->flood = $flood;
    $this->currentUser = $current_user;
    $this->userStorage = $user_storage;
    $this->emailValidator = $email_validator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('flood'),
      $container->get('current_user'),
      $container->get('entity_type.manager')->getStorage('user'),
      $container->get('email.validator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'code_user_change_email';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $user = $this->userStorage->load($this->currentUser->id());
    $form['email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email'),
      '#placeholder' => $user->getEmail(),
      '#required' => TRUE,
      '#attributes' => [
        'style' => 'width: 30%;',
      ],
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send'),
    ];

    return $form;
  }

  /**
   * Check if email already exists.
   */
  public function validateEmail($email) {
    $uid = $this->userStorage->getQuery()->accessCheck(TRUE)->condition('mail', $email)->execute();
    $is_valid = ($uid) ? TRUE : FALSE;
    return $is_valid;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $ip_address = $this->currentUser->getAccountName();
    $limit = 5;
    $interval = 3600;

    // Check flood limit for this form action by IP.
    if (!$this->flood->isAllowed('code_user_change_email', $limit, $interval, $ip_address)) {
      $this->messenger()->addError($this->t('You have exceeded the maximum number of attempts. Please try again later.'));
      return;
    }

    // If allowed, proceed and register this attempt.
    $this->flood->register('code_user_change_email', $interval, $ip_address);
    // $this->flood->register('user.password_request_ip', $flood_config->get('ip_window'));
    $user = $this->userStorage->load($this->currentUser->id());
    if (self::validateEmail($form_state->getValue('email'))
        || !($this->emailValidator->isValid($form_state->getValue('email')))
        || $form_state->getValue('email') === $user->getEmail()
      ) {
      $form_state->setErrorByName('email', $this->t('please enter a valid email not already used in the site and different from the current email.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $user = $this->userStorage->load($this->currentUser->id());
    $user->setEmail($form_state->getValue('email'));
    $user->save();
    $this->messenger()->addStatus($this->t($form_state->getValue('email') . 'has been added successfully.'));
  }

}
