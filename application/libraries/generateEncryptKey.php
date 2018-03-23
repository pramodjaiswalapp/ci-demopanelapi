<?php 

require "./OpenSSLEncrypt.php";

$enc = new Encryption\OpenSSLEncrypt("data");
$enc->generateKey(true);
