<?php 

class Encryption 
{

    const AES_256_CBC = 'aes-256-cbc';
    const AES_128_CBC = 'aes-128-cbc';
    const AES_192_CTR = 'aes-192-ctr';
    const AES_256_CTR = 'aes-256-ctr';

    private $data;
    private $method;
    private $initializationVector;
    private $key;
    private $keySize;
    private $options;

    public function __construct($data) 
    {
        $this->options = 0;
        $this->keySize = 16;
        $this->initializationVector = "";
        $this->method = self::AES_128_CBC;
        $this->data = $data;
    }

    public function __get($property) 
    {
        if ( property_exists($this, $property) ) {
            return $this->$propery;
        }
    }

    public function __set($property, $value) 
    {
        if ( property_exists($this, $property) ) {
            $this->$propery = $value;
        }
    }

    public function generateKey() 
    {
        $encryptionKey = openssl_random_pseudo_bytes($this->keySize);
        $encryptionKey = bin2hex($encryptionKey);

        $file = file_put_contents("./encrypts", $encryptionKey);

        //file becomes read only
        chmod("./encrypts", 0444);

        if ( $file ) {
            $this->key = $encryptionKey;
        }
    }

    public function encrypt($readFromFile = false)
    {
        if ( $readFromFile && file_exists("./encrypts") ) {
            $this->key = file_get_contents("./encrypts");
        }

        if ( !isset($this->key) || empty($this->key)  ) {
            throw new Exception("Set Encryption Key");
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
    
}


$enc = new Encryption();

$enc->generateKey(16);
