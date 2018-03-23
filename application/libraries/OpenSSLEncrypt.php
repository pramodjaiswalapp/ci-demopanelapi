<?php 
// defined('BASEPATH') OR exit('No direct script access allowed');


/**
 * @property string $data data for encryption
 * @property string $method method for encryption (use constants eg. OpenSSLEncrypt::AES_128_CBC - default value)
 * @property int $keySize set key size, default 32
 * @property int $options options for encryption default value 
 * @property string $keyPath 
 */

class OpenSSLEncrypt
{
    /**
     */
    const AES_256_CBC = 'aes-256-cbc';
    const AES_128_CBC = 'aes-128-cbc';
    const AES_192_CTR = 'aes-192-ctr';
    const AES_256_CTR = 'aes-256-ctr';

    /**
     * @var string $data 
     * @var string $method 
     * @var string $initializationVector 
     * @var string $key 
     * @var int $keySize 
     * @var int $options 
     */
    private $data;
    private $method;
    private $initializationVector;
    private $key;
    private $keySize;
    private $options;
    private $keyPath;

    public function __construct(
        $data="",
        $options = 0,
        $keySize = 32,
        $method = self::AES_256_CBC,
        $keyPath = "./"
    )
    {
        $this->options = $options;
        $this->keySize = $keySize;
//        $this->initializationVector = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
        $this->initializationVector = 'beautylivkingdom';
        $this->method = $method;
        $this->data = $data;
        $this->keyPath = $keyPath;
    }
    
    public function __get($property)
    {
        if ($property == "key") {
            return false;
        }
        if ( $property == "initializationVector" ) {
            return bin2hex($this->$property);
        }
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function __set($property, $value)
    {
//        if ($property == "key") {
//            return false;
//        }
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
    }

    public function __unset($property)
    {
        if ($property == "key" || $property == "initializationVector") {
            return false;
        }
        if (property_exists($this, $property)) {
            unset($this->$property);
        }
    }

    public function __isset($property)
    {
        if (isset($this->$property)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Generates key using openssl random pseudo bytes
     * @param bool $writeToFile if True writes key to file
     * 
     * @return string encryption key
     */
    public function generateKey($writeToFile = false)
    {
        $encryptionKey = openssl_random_pseudo_bytes($this->keySize);
        $encryptionKey = bin2hex($encryptionKey);

        if ($writeToFile) {
            $file = file_put_contents($this->keyPath . "encrypts", $encryptionKey);

            //file becomes read only
            chmod($this->keyPath . "encrypts", 0600);

            if ($file) {
                $this->key = $encryptionKey;
            }
        }
        else {
            $this->key = $encryptionKey;
        }

        return $encryptionKey;
    }

    /**
     * Encryption function 
     * 
     * @param bool $readKeyFromFile if True reads key from file
     * 
     * @return string encrypted data
     */
    public function encrypt($readKeyFromFile = false)
    {                

        if (!isset($this->key) || empty($this->key)) {
            throw new \Exception("Set Encryption Key");
        }
        
        $encryptedData = openssl_encrypt(
            $this->data,
            $this->method,
            $this->key,
            $this->options,
            $this->initializationVector
        );
        
        return $encryptedData;
    }

    /**
     * Decryption function 
     * @param string $encryptedData 
     * @param string $initializationVector 
     * @param bool $readKeyFromFile if True reads key from file
     * 
     * @return string decrypted clear text
     */
    public function decrypt($encryptedData, $initializationVector, $readKeyFromFile = false)
    {


        if (!isset($this->key) || empty($this->key)) {
            throw new \Exception("Set Encryption Key");
        }

        $initializationVector = hex2bin($initializationVector);

        $decryptedData = openssl_decrypt(
            $encryptedData,
            $this->method,
            $this->key,
            $this->options,
            $initializationVector
        );

        return $decryptedData;
    }

}