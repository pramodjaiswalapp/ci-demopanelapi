<?php
/**
 * Custom Authentication and error handler Controller.
 * 
 * @package         Libraries
 * @category        Libraries
 * @author          AppInventiv
 */
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

class Authentication extends REST_Controller {
    
    protected $loginUser;
    /**
     * Constructor for the AutheticationLib
     *
     * @access public
     * @param string $config (do not change)
     * @return void , EXIT_USER_INPUT on error
     */
    public function __construct($config = 'rest') {
        parent::__construct($config);
       
        $config_username = AUTH_USER;
        $config_password = AUTH_PASS;
        $this->lang->load('common_lang', "english");
        $this->lang->load('api','api');
        $header['PHP_AUTH_USER'] = $this->input->server('PHP_AUTH_USER');
        $header['PHP_AUTH_PW']   = $this->input->server('PHP_AUTH_PW');
        if(!empty($header['PHP_AUTH_USER']) && !empty($header['PHP_AUTH_PW'])){
            if ($config_username != $header['PHP_AUTH_USER'] || $config_password != $header['PHP_AUTH_PW']) {
                
                $responseArr = array(
                    'code' => INVALID_HEADER,
                    'msg' => $this->lang->line('Invalid_Header_key'),
                    'result'=>[]
                );
                $this->response($responseArr);
            }
        } else {
            $response = array(
                'code' => MISSING_HEADER,
                'msg' => $this->lang->line('MISSING_HEADER_PARAM'),
                'result' => []
                
            );
            $this->response($response);
            
        }    
        
    }
    
    /**
     * @name checkLogin
     * @description checkes for login with accesstoken from header.
     * @param string $accessToken
     * @return int or error array
     */
    public function checkLogin($accessToken) {
        if (!empty($accessToken)) {
            $this->load->model('User_model');
            $params = array();
            $params['access_token'] = $accessToken;
            $this->loginUser = $this->User_model->getLoginDetail($params);
            if (!empty($this->loginUser)) {
                if ($this->loginUser->userStatus != ACTIVE) {
                    $this->response(['code' => USER_STATUS_NOT_ACTIVE,'message'=>$this->lang->line('USER_STATUS_NOT_ACTIVE')]);
                }
                return $this->loginUser->userId;
            }
        }
        $this->response(['code' => INVALID_ACCESS_TOKEN,'message'=>$this->lang->line('INVALID_ACCESS_TOKEN')]);
    }
    
    /**
     * @name getAccessToken
     * @description get token value from header.
     * @return string
     */
    public function getAccessToken(){
        if (!empty($this->input->request_headers()['Uaccesstoken'])) {
            return $this->input->request_headers()['Uaccesstoken'];
        } else{
            $this->response(['code' => HEADER_MISSING,'message'=>$this->lang->line('HEADER_MISSING')]);
        }
    }
    
    public function checkRequiredParams($param = array()) {
        if (isset($param) && is_array($param) && count($param)) {
            foreach ($param as $par) {
                if (empty($par)) {
                    return 0;
                }
            }
        }
        return 1;
    }
    
    /**
     * @name validator
     * @description validates input array that value is set. Optionally checks
     *              for empty case. 
     * @param array $param
     * @param array $data
     * @param boolean $checkEmpty
     * @return boolean
     */
    public function validator($param , $data , $checkEmpty = false){
        if ($checkEmpty) {
            for ($i = 0; $i < count($param); $i++) {
                if (empty($data[$param[$i]])) {
                    return false;
                }
            }
        }else{
            for ($i = 0; $i < count($param); $i++) {
                if (!isset($data[$param[$i]])) {
                    return false;
                }
            }
        }
        return true;
    }

   
     private function parameterMissing($panel = false) {
        $this->response_data([], PARAM_REQ, $panel,'REQUIRED_PARAMETER_MISSING');
    }

    public function checkEmptyParameter($array = [], $required = [], $panel = false) {
        foreach ($required as $req) {
            if (!isset($array[$req]) || empty($array[$req])) {
                $this->parameterMissing($req);
            }
        }
    }

   
    public function response_data($data, $status = 200, $lang_key, $msg = '') {

        
            
            if(!empty($msg))
            $err_msg = $msg;
            else
            $err_msg = $this->lang->line($lang_key);
                
                $response = array(
                                "code" => $status, 
                                "msg" => $err_msg, 
                                "result" => $data, 
                            );

                $this->response($response);
            // when empty data
           
        
    }

     public function validateData($data = []) {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'email': $this->validateEmail($value);
                    break;
                default :$this->response_data('', 406);
                    break;
            }
        }
    }

    private function validateEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $this->response_data('', 207);
        }
        return false;
    }

    public function defineDefaultValue($keyArr, $data) {
        foreach ($keyArr as $key) {
            if (!isset($data[$key])) {
                if ($key == "ipaddress") {
                    $data[$key] = $_SERVER["REMOTE_ADDR"];
                } else
                    $data[$key] = "";
            }
        }
        return $data;
    }

    public function encrypt($text, $salt = 'A3p@pI#%!nVeNiT@#&vNaZiM', $isBaseEncode = true) {
        if ($isBaseEncode) {
            return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
        } else {
            return trim(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
        }
    }

    public function decrypt($text, $salt = 'A3p@pI#%!nVeNiT@#&vNaZiM') {
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $salt, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
    }


     public function create_access_token($user_id = '1', $email = 'engineer.nazim@gmail.com') {
        $session_private_key = chr(mt_rand(ord('a'), ord('z'))) . substr(md5(time()), 1);
        $session_public_key = $this->encrypt($user_id . $email, $session_private_key, true);
        $access_token['private_key'] = $session_private_key;
        $access_token['public_key']  = $session_public_key;
        return $access_token;
    }

   
    public function checkvalidationcustom($arr) {
        // append your custom check for other validations 

        foreach ($arr as $key => $val) {

            switch ($key) {
 
                case 'dob' : if (!$this->isValidDateTimeString($val, 'm/d/Y', 'UTC'))
                        $this->response_data([], INVALID_DATE_FORMAT,'INVALID_DATE_FORMAT');
                    break;


                case 'password' :

                    if (!preg_match(PASSWORD_REGEX, $val))
                        $this->response_data([], INVALID_PASSWORD_FORMAT, 'INVALID_PASSWORD_FORMAT');
                    break;

                default : break;
            }
        }
    }

    function isValidDateTimeString($str_dt, $str_dateformat, $str_timezone) {
        $date = DateTime::createFromFormat($str_dateformat, $str_dt, new DateTimeZone($str_timezone));
        return $date && $date->format($str_dateformat) == $str_dt;
    }
    


}



