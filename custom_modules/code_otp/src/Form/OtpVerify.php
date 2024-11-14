<?php

declare(strict_types=1);

namespace Drupal\code_otp\Form;

use Drupal;
use Drupal\user\Entity\User;
use Drupal\Core\Form\FormBase;
use Drupal\user\UserInterface;
use Drupal\code_otp\OtpService;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OtpVerify extends FormBase {

    public function getFormId() {    
        return 'otp_verify';  
    }   

    /**
     * The private temp store factory.
     *
     * @var \Drupal\code_otp\OtpService
     */
    protected OtpService $otpService;

    /**
     * Constructs an OtpRequestForm object.
     *
     * @param \Drupal\code_otp\OtpService $otpService
     *   The private temp store factory.
     */
    public function __construct(OtpService $otpService) {
        $this->otpService = $otpService;
    }

    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('code_otp.otp')
        );
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
        $input_otp = $form_state->getValue('otp');
        // Check if OTP is numeric and exactly 6 digits.
        if (!is_numeric($input_otp) || strlen($input_otp) !== 6) {
            $form_state->setErrorByName('otp', $this->t('The OTP must be a 6-digit number.'));
        }
    }

    public function buildForm(array $form, FormStateInterface $form_state) {    
        $uid = $this->otpService->getTempValue('otp_requests', 'uid');
        $user = User::load($uid);
        $email = ($user instanceof UserInterface) ? $user->getEmail() : '';

        $form['uid'] = [
            '#type' => 'hidden',
            '#value' => $uid,  
        ];
        $form['email'] = [
            '#type' => 'hidden',
            '#value' => $email,  
        ]; 
        $form['markup'] = [
            '#type' => 'markup',
            '#markup' => '<span class="markup">'. t('Otp has been sent to: ') . $email . "</span>",
        ];
        $form['otp'] = [      
            '#type' => 'hidden',
            '#attributes' => ['class' => ['full-number']],
        ];    
        // Create six text fields, each limited to one digit, and display them horizontally.
        $form['split_digits'] = [
            '#type' => 'container',
            '#attributes' => [
            'class' => ['split-digits-container'],
            ],
        ];
    
        for ($i = 0; $i < 6; $i++) {
            $form['split_digits']['digit_' . $i] = [
            '#type' => 'textfield',
            '#size' => 1,
            '#maxlength' => 1,
            '#attributes' => [
                'class' => ['split-digit'],
                'data-digit-index' => $i,
                'inputmode' => 'numeric', // Mobile numeric keyboard.
                'style' => 'text-align: center;', // Center digits in the box.
            ],
            '#required' => TRUE,
            ];
        }
        $form['actions']['#type'] = 'actions';    
        $form['actions']['submit'] = [      
            '#type' => 'submit',      
            '#value' => $this->t('Verify OTP'),    
        ];
        $form['resend_otp'] =[
            '#type' => 'markup', 
            '#markup' => "<span id='resend_otp_line'> Didn't recive an OTP code? "."<a id='resend' href='#'>". $this->t('Resend Otp') . "</a> </span>",
        ];
        $form['timer'] =[
            '#type' => 'markup',
            '#markup' => "<span id='timer'></span>",
        ];    
        $form['#attached']['library'][] = 'code_otp/code_otp';
        return $form;  
    }


    public function submitForm(array &$form, FormStateInterface $form_state) { 
        $uid = $this->otpService->getTempValue('otp_requests', 'uid');
        $input_otp = $form_state->getValue('otp');
        $user_otp = $this->otpService->getUserData('code_otp', $uid, 'otp');
        if (Time() < $this->otpService->getTempValue('otp_requests', 'expiration_time') && md5($input_otp) == $user_otp) {  
            $user = User::load($uid);    
            user_login_finalize($user);
            // OTP is valid, proceed with login.      
            \Drupal::messenger()->addMessage($this->t('OTP verified successfully!'));
            // Clear the OTP from session
            $this->otpService->clearUserData('code_otp', $uid, 'otp');
            $form_state->setRedirect('<front>');
            // Iterate over each key and delete them.
            $this->otpService->clearTempFile('otp_requests');
        } 
        else{
            \Drupal::messenger()->addError($this->t('Otp is not correct'));  
        }
    }
}