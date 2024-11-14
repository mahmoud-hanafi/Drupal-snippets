<?php

//routing info 
//code_user.reset.login.custom:
//   path: '/user/reset/{uid}/{timestamp}/{hash}'
//   defaults:
//     _controller: '\Drupal\code_user\Controller\PasswordResetController::handleReset'
//     _title: 'Password Reset'
//   requirements:
//     _access: 'TRUE'


namespace Drupal\code_user\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\Core\Url;
use Drupal\user\UserStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controll OTL link.
 */
class PasswordResetController extends ControllerBase {

  /**
   * The user storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * ChangeEmailForm constructor.
   */
  public function __construct(UserStorageInterface $user_storage) {
    $this->userStorage = $user_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')->getStorage('user')
    );
  }

  /**
   * Controll OTL Reset request.
   */
  public function handleReset($uid, $timestamp, $hash) {
    // Load the user entity.
    $account = $this->userStorage->load($uid);
    // Define a custom timeout (e.g., 1 hour).
    $timeout = 3600;
    // Verify that the user exists and is active.
    if (!$account || !$account->isActive()) {
      throw new AccessDeniedHttpException();
    }
    $expected_hash = user_pass_rehash($account, $timestamp);
    // Validate the hash and ensure the link has not expired.
    if ($hash !== $expected_hash || (REQUEST_TIME - $timestamp) > $timeout) {
      throw new AccessDeniedHttpException();
    }
    // Log the user in.
    user_login_finalize($account);
    $redirect_url = Url::fromRoute('entity.user.edit_form.change_password', ['user' => $uid])->toString();
    // Redirect to your custom page.
    return new RedirectResponse($redirect_url);
  }

}
