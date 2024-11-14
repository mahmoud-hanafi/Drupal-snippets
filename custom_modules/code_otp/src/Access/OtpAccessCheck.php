<?php

namespace Drupal\code_otp\Access;

use Drupal\code_otp\OtpService;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a custom access check for OTP requests.
 */
class OtpAccessCheck implements AccessInterface {

  /**
   * The private temp store factory.
   *
   * @var \Drupal\code_otp\OtpService
   */
  protected OtpService $otpService;

  public function __construct(OtpService $otpService) {
    $this->otpService = $otpService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('code_otp.otp')
    );
  }

  /**
   * Custom access check to ensure the user has requested an OTP.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   The access result.
   */
  public function access() {
    // Get the TempStore otp request from custom otp service.
    $status = $this->otpService->getTempValue('otp_requests', 'otp_requested');

    // Check if the tempstore variable is set indicating an OTP request.
    if ($status) {
      return AccessResult::allowed();
    }

    return AccessResult::forbidden();
  }

}
