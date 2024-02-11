<?php

namespace Drupal\custom_ksu\Controller;

use Drupal\Core\Controller\ControllerBase;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\HttpFoundation\Response;
use Drupal\user\Entity\User;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\profile\Entity\Profile;
use Drupal\Core\Routing\RouteMatchInterface;


/**
 * Class HsAlgorthimController.
 */
class HsAlgorthimController extends ControllerBase
{

    /**
     * create token.
     *
     */
    public function createToken()
    { 
        // detect browser language
        $languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $browserLanguage = strtolower(substr($languages[0], 0, 5)); // Get the first two characters
        // get current user id
        $user_id = \Drupal::currentUser()->id();
        // $user = User::load($user_id);
        // $profile = Profile::load($user_id);
        // $fieldDefinitions = $user->getFieldDefinitions();
        // dd($user);
        // $username = $user->getDisplayName();
        // get expiration date 
        $expiration_time = \Drupal::time()->getCurrentTime() + (30*60);
        $secret_key = "pDzCAJ69KSacWY2kLaqf0UWb89i_gy_6IGvndSWe4e";
        $payload = array(
            'userIdentifier' => $user_id, // Issuer
            'languagecode' => $browserLanguage, // Subject
            'expiration_time' =>  \Drupal::time()->getCurrentTime() + (30*60), // Time the token was issued
        );
        $token = JWT::encode($payload, $secret_key, 'HS256');
        // dd($token);
        $client = \Drupal::httpClient();
        $url = 'https://viviendoconevw.cslbehring.cl/Login?token='.$token;
        $response = $client->request('get', ($url));
        // $dumps_url = 'http://ptsv2.com/t/g8493-1580421270';
        $response = new TrustedRedirectResponse($url);  
        $response->send();  
        // dd($response->getStatusCode());
        // if ($response->getStatusCode() === 200) {
        //     // Decode the JSON response body
        //     $data = json_decode($response->getBody(), TRUE);
        //     // dd($data);
        //     // Use the data for your application
        //     foreach ($data['items'] as $item) {
        //       // Do something with the item data
        //       dd($item);
        //     }
        //   }
        // else {
        //     // Handle failed submission
        //     watchdog_error('Failed to submit data to API: @message', ['@message' => $response->getBody()]);
        //   }
        
        // $payload = JWT::decode($token, new Key($secret_key, 'HS256'));
        
        $build = [
            '#type' => 'theme',
            '#theme' => 'von_willebrand_disease',
            '#items' => $token,
        ];
        
        return $build;
    }

    /**
     * validate token.
     *
     */
    public function validateToken()
    {
        $route_match = \Drupal::routeMatch();
        $token = $route_match->getParameter('token');
        
        $secret_key = "pDzCAJ69KSacWY2kLaqf0UWb89i_gy_6IGvndSWe4e";
        // self::is_valid_jwt_token($token, $secret_key);
        // dd(strlen($secret_key));
        // dd(base64_decode($token));
        
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
          return new Response('Invalid JWT token format');
        }
        $header = json_decode(base64_decode($parts[0]), true);
        // dd($payload);
        // Check algorithm is HS256
        if ($header['alg'] === 'HS256') {
          $payload = json_decode(base64_decode($parts[1]), true);
          $user_id = \Drupal::currentUser()->id();
          $languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
          $browserLanguage = strtolower(substr($languages[0], 0, 5));
          $response = new Response();
          $userIdentifier = $payload['userIdentifier'] ? $payload['userIdentifier'] : '';
          $languagecode = $payload['languagecode'] ? $payload['languagecode'] : '';
          if (!empty($userIdentifier) && !empty($languagecode)) {
              if ($user_id == $userIdentifier && $browserLanguage == $languagecode) {
                return new Response('Success! Valid token', Response::HTTP_OK);
              } else {
                return new Response('Invalid token', Response::HTTP_OK);
              }
          } else{
              return new Response('Invalid token', Response::HTTP_OK);
          } 
        } else{
          return new Response("given token isn't supported by HS256 algorithm");
        }
    }

}