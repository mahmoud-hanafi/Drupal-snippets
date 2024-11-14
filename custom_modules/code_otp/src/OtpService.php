<?php

namespace Drupal\code_otp;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\user\UserDataInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * OtpService service.
 */
class OtpService {

  /**
   * The code_otp.otp service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerChannelFactory;

  /**
   * The code_otp.otp service.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * The code_otp.otp service.
   *
   * @var \Drupal\Core\TempStore\PrivateTempStoreFactory
   */
  protected $tempStoreFactory;

  /**
   * The code_otp.otp service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The code_otp.otp service.
   *
   * @var \Drupal\user\UserDataInterface
   */
  protected $userData;

  /**
   * {@inheritdoc}
   */
  public function __construct(LoggerChannelFactoryInterface $loggerChannelFactory, MailManagerInterface $mailManager, PrivateTempStoreFactory $tempStoreFactory, UserDataInterface $userData) {
    $this->loggerChannelFactory = $loggerChannelFactory;
    $this->mailManager = $mailManager;
    $this->tempStoreFactory = $tempStoreFactory;
    $this->userData = $userData;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('logger.factory'),
      $container->get('plugin.manager.mail'),
      $container->get('tempstore.private'),
      $container->get('user.data')
    );
  }

  /**
   * Generate random integer otp.
   */
  public function generateOpt() {
    return random_int(100000, 999999);
  }

  /**
   * Send OTP email.
   */
  public function sendOpt($module, $key, $to, &$params) {
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $result = $this->mailManager->mail($module, $key, $to, $langcode, $params, NULL, TRUE);
    if ($result['result'] !== TRUE) {
      $this->loggerChannelFactory->get('otp_login')->error('There was a problem sending your OTP email to %email.', ['%email' => $to]);
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Return temp key value.
   */
  public function setTempValue($name, $key, $value) {
    $this->tempStoreFactory->get($name)->set($key, $value);
  }

  /**
   * Return temp key value.
   */
  public function getTempValue($name, $key) {
    $tempstore = $this->tempStoreFactory->get($name);
    if ($tempstore->get($key)) {
      return $tempstore->get($key);
    }
    return '';
  }

  /**
   * Delete temp store file.
   */
  public function clearTempFile($name) {
    foreach ($this->tempStoreFactory->get($name) as $key) {
      $this->tempStoreFactory->get($name)->delete($key);
    }
  }

  /**
   * Set user data.
   */
  public function setUserData($module, $uid, $name, $value) {
    $this->userData->set($module, $uid, $name, $value);
  }

  /**
   * Get User Data.
   */
  public function getUserData($module, $uid, $name) {
    return $this->userData->get($module, $uid, $name);
  }

  /**
   * Clear user data.
   */
  public function clearUserData($module, $uid, $name) {
    $this->userData->delete($module, $uid, $name);
  }

}
