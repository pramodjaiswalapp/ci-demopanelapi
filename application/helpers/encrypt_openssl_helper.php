<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//function encrypt_with_openssl($encryptObj, $urlencode = false)
//{
//    $encryptObj->keyPath = ENCRYPT_KEY_PATH;
//    $iv = $encryptObj->initializationVector;
//    $encryptedData = $encryptObj->encrypt(true) . ":" . $iv;
//    if ( $urlencode ) {
//        return rawurlencode($encryptedData);
//    } else {
//        return $encryptedData;
//    }
//}
//
//function decrypt_with_openssl($decryptObj, $data, $urldecode = false)
//{
//    $decryptObj->keyPath = ENCRYPT_KEY_PATH;
//    if ( $urldecode ) {
//        $data = rawurldecode($data);
//    }
//    $dcrypt = explode(":", $data);
//    if ( count($dcrypt) != 2 ) {
//        return false;
//    }
//    $decryptedData = $decryptObj->decrypt($dcrypt[0], $dcrypt[1], true);
//
//    return $decryptedData;
//}
