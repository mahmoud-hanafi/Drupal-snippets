<?php

/**
 * @file
 * Contains \Drupal\json_export\Controller\EncryptUsers.
 */

namespace Drupal\json_export\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
* Class EncryptUsers.
*/
class EncryptUsers extends ControllerBase {
    
    
    public function build() {
        $uids = \Drupal::entityQuery('user')
                ->accessCheck(TRUE)
                ->condition('roles', 'administrator', '!=')
                ->execute();
        $users = User::loadMultiple($uids);
        $secret_key = "pDzCAJ69KSacWY2kLaqf0UWb89i_gy_6IGvndSWe4e";
        $cipher = 'aes-256-cbc';
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
        // self::decrypte($users); 
        $counter = 1;   
        foreach($users as $user) {
            // Loop through each user field
            foreach ($user->getFields() as $field_name => $field) {
                $field_value = $user->get($field_name)->getValue()[0]['value'];
                // dd($field_name);
                $fields = ['field_full_name', 'field_id'];
                if(in_array($field_name, $fields) ){
                    if (!empty($field_value)) {
                        // $field_value = JWT::encode(["$field_name"=>$field_value], $secret_key, 'HS256');
                        // $field_value = base64_encode(openssl_encrypt($field_value , $cipher, $secret_key, OPENSSL_RAW_DATA, $iv));
                        $field_value = $field_value . $counter++; 
                        $user->$field_name->value = $field_value;
                        // dd($user->$field_name->value);
                        $user->save();
                        //$field_value = JWT::decode($field_value, new Key($secret_key, 'HS256'));
                    }
                }
                else continue;
            }
        }
        dd("working");
        dd($fields);
        dd($uids);
    }  


    public function decrypte($users) {
        $secret_key = "pDzCAJ69KSacWY2kLaqf0UWb89i_gy_6IGvndSWe4e";
        $cipher = 'aes-256-cbc';
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
        foreach($users as $user) {
            // Loop through each user field
            foreach ($user->getFields() as $field_name => $field) {
                $field_value = $user->get($field_name)->getValue()[0]['value'];
                // dd($field_name);
                $fields = ['field_full_name', 'field_id'];
                if(in_array($field_name, $fields) ){
                    if (!empty($field_value)) {
                        // $field_value = JWT::encode(["$field_name"=>$field_value], $secret_key, 'HS256');
                        $field_value = base64_decode(openssl_decrypt($field_value , $cipher, $secret_key, OPENSSL_RAW_DATA, $iv));
                        $user->$field_name->value = $field_value;
                        // dd($user->$field_name->value);
                        $user->save();
                        //$field_value = JWT::decode($field_value, new Key($secret_key, 'HS256'));
                    }
                }
                else continue;
            }
            dd("decrypted");
        }
    }
}
