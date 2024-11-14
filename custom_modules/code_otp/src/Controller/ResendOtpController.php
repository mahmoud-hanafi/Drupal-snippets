<?php

declare(strict_types=1);

namespace Drupal\code_otp\Controller;

use Drupal\code_otp\OtpService;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns responses for Code OTP routes.
 */
class ResendOtpController extends ControllerBase {

  /**
   * The code_otp.otp service.
   *
   * @var \Drupal\code_otp\OtpService
   */
  protected OtpService $otpService;

  /**
   * The controller constructor.
   *
   * @param \Drupal\code_otp\OtpService $otpService
   *   The code_otp.otp service.
   */
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
   * Builds the response.
   */
  public function resend(Request $request) {

    try {
      $data = $request->getContent();
      $data = json_decode($data, TRUE);
      $uid = $data['uid'];
      $email = $data['email'];
      // dd($uid);
      if ($uid  && $email) {
        $this->otpService->clearUserData('code_otp', $uid, 'otp');
        $otp = $this->otpService->generateOpt();
        $params['otp'] = $otp;
        $this->otpService->setUserData('code_otp', $uid, 'otp', md5(strval($otp)));
        $result = $this->otpService->sendOpt('code_otp', 'otp', $email, $params);
        if ($result) {
          \Drupal::messenger()->addMessage($this->t('OTP has been sent successfully!'));
        }
        $this->otpService->setTempValue('otp_requests', 'expiration_time', time() + 120);
      }
      return new JsonResponse([
        'result' => $result,
        'error' => FALSE,
        'message' => 'OTP has been sent successfully!',
      ]);
    }
    catch (\Throwable $th) {
      \Drupal::service('logger.factory')->get("otp")->error($th->getMessage());
      return new JsonResponse([
        'error' => TRUE,
        'message' => 'Server error 500',
      ]);
    }
  }

}
