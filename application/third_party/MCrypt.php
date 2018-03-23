<?php

//defined('BASEPATH') OR exit('No direct script access allowed');

class MCrypt {

    private $iv = 'Mft10370mft#@*17';
    private $key = '%$pro012#@78mft7';

    function __construct() {
        
    }

    function encrypt($str) {

        //$key = $this->hex2bin($key);    
        $iv = $this->iv;

        $td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);

        mcrypt_generic_init($td, $this->key, $iv);
        $encrypted = mcrypt_generic($td, $str);

        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        return bin2hex($encrypted);
    }

    function decrypt($code) {
        //$key = $this->hex2bin($key);
       
        $code = $this->hex2bin($code);
        $iv = $this->iv;

        $td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);

        mcrypt_generic_init($td, $this->key, $iv);
        $decrypted = mdecrypt_generic($td, $code);

        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        
        return utf8_encode(trim($decrypted));
    }

    protected function hex2bin($hexdata) {
        $bindata = '';

        for ($i = 0; $i < strlen($hexdata); $i += 2) {
            $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
        }

        return $bindata;
    }

       function pkcs5_unpad($text) {
       
      $pad = ord($text{strlen($text) - 1}); 
        if ($pad > strlen($text)){
            return false;
        }
            
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad){
            return false;
        }
            
       // echo substr($text, 0, -1 * $pad);
       
        return substr($text, 0, -1 * $pad);
    }

    function pkcs5_pad($text) {
        $blocksize = 16;
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    
    /**
     * 1- encryption
     * 2- decryption
     * @param type $data
     * @param type $convert_type
     * @return type
     * @throws Exception
     */
    
        public function encrdecr($data = NULL, $convert_type = '1') {
        $key = '%$pro012#@78mft7%$pro012#@78mft7';
        $iv ="Mft10370mft#@*17" ;
        try {
            if (empty($key) || empty($data))
                throw new Exception("Salt or data is missing");
            if ($convert_type == 1) {
                $data = $this->addPKCS5Padding($data);
                $return_string = trim(base64_encode(mcrypt_encrypt('rijndael-128', $key, $data, 'cbc', $iv )));
            } else {
                $data = $this->addPKCS5Padding($data);
                $return_string = trim(mcrypt_decrypt('rijndael-128', $key, base64_decode($data), 'cbc', $iv ));
                $html = $return_string;
                $needle = "}";
                $lastPos = 0;
                $positions = array();
                while (($lastPos = strpos($html, $needle, $lastPos)) !== false) {
                    $positions[] = $lastPos;
                    $lastPos = $lastPos + strlen($needle);
                } $pos = 0;
                foreach ($positions as $value) {
                    $pos = $value;
                } if ($pos) {
                    $return_string = substr($return_string, 0, $pos + 1);
                }
            } return $return_string;
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }
        
        }  
         public function addPKCS5Padding($data) {
        $padding = '';
        $padlength = 16 - (strlen($data) % 16);
        for ($i = 1; $i <= $padlength; $i++) {
            $padding .= chr($padlength);
        } return $data . $padding;
    }

    }


