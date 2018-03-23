<?php
 if ( !defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

 if ( !function_exists( 'pr' ) ) {

     function pr( $d ) {
         echo "<pre>";
         print_r( $d );
         echo "</pre>";
         exit();

     }



 }

 /**
  * @function load_views
  * @description to load views without loading header and footer
  *
  * @param string $customView view name with it's path
  * @param array $data data to transfer in view
  */
 function load_views( $customView, $data = array () ) {
     $CI = &get_instance();
     $CI->load->view( 'templates/header', $data );
     $CI->load->view( $customView, $data );
     $CI->load->view( 'templates/footer', $data );

 }



 /**
  * @function load_outer_views
  * @descripition to load Outer common view
  *
  * @param string $customView view name with it's path
  * @param array $data data to transfer in view
  */
 function load_outer_views( $customView, $data = array () ) {
     $CI = &get_instance();
     $CI->load->view( '/admin/header', $data );
     $CI->load->view( $customView, $data );
     $CI->load->view( '/admin/footer', $data );

 }



 /**
  * @function getConfig
  * @description to set File upload Configuration
  *
  * @param string $uploadPath Path to upload File
  * @param string $acptFormat accepted file format
  * @param int $maxSize Max File Size
  * @param int $maxWidth Image Max Height
  * @param int $maxHeight Image Max Width
  * @param Boolean $encryptName File name should be encrypted or not
  * @return type
  */
 function getConfig( $uploadPath, $acptFormat, $maxSize = 3000, $maxWidth = 1024, $maxHeight = 768, $encryptName = TRUE ) {
     $config                  = [];
     $config['upload_path']   = $uploadPath;
     $config['allowed_types'] = $acptFormat;
     $config['max_size']      = $maxSize;
     $config['max_width']     = $maxWidth;
     $config['max_height']    = $maxHeight;
     $config['encrypt_name']  = $encryptName;
     return $config;

 }



 /**
  * @function create_access_token
  * @description TO generate access token
  *
  * @param int $user_id User's id
  * @param string $email User's email address
  * @return string
  */
 function create_access_token( $user_id = '1', $email = 'dummyemail@gmail.com' ) {
     $session_private_key         = chr( mt_rand( ord( 'a' ), ord( 'z' ) ) ).substr( md5( time() ), 1 );
     $session_public_key          = encrypt( $user_id.$email, $session_private_key, true );
     $access_token['private_key'] = base64_encode( $session_private_key );
     $access_token['public_key']  = base64_encode( $session_public_key );
     return $access_token;

 }



 function encrypt( $text, $salt = 'A3p@pI#%!nVeNiT@#&vNaZiM', $isBaseEncode = true ) {
     if ( $isBaseEncode ) {
         return trim( base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, $salt, $text, MCRYPT_MODE_ECB, mcrypt_create_iv( mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB ), MCRYPT_RAND ) ) ) );
     }
     else {
         return trim( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, $salt, $text, MCRYPT_MODE_ECB, mcrypt_create_iv( mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB ), MCRYPT_RAND ) ) );
     }

 }



 function decrypt( $text, $salt = 'A3p@pI#%!nVeNiT@#&vNaZiM' ) {
     return trim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, $salt, base64_decode( $text ), MCRYPT_MODE_ECB, mcrypt_create_iv( mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB ), MCRYPT_RAND ) ) );

 }



 /**
  * @function datetime
  * @description to return date and time
  *
  * @return date
  */
 function datetime() {
     return date( 'Y-m-d H:i:s' );

 }



 /**
  * @function encryptDecrypt
  * @description A common function to encrypt or decrypt desired string
  *
  * @param string $string String to Encrypt
  * @param string $type option encrypt or decrypt the string
  * @return type
  */
 function encryptDecrypt( $string, $type = 'encrypt' ) {

     if ( $type == 'decrypt' ) {
         $enc_string = base64decryption( $string );
     }
     if ( $type == 'encrypt' ) {
         $enc_string = base64encryption( $string );
     }
     return $enc_string;

 }



 /**
  * @function sendPostRequest
  * @description to hit CURL using post method
  *
  * @param array $data required data array to hit CURL
  * @return array|boolean
  */
 function sendPostRequest( $data ) {
     $ch       = curl_init();
     curl_setopt( $ch, CURLOPT_URL, $data['url'] );
     curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
     curl_setopt( $ch, CURLOPT_HEADER, false );
     curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $data ) );
     $respData = curl_exec( $ch );
     curl_close( $ch );
     return $respData;

 }



 /**
  * @function sendGetRequest
  * @description to hit CURL using GET method
  *
  * @param array $data required data array to hit CURL
  * @return array|boolean
  */
 function sendGetRequest( $data ) {

     $ch       = curl_init();
     $timeout  = 1;
     curl_setopt( $ch, CURLOPT_URL, $data['url'] );
     curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
     curl_setopt( $ch, CURLOPT_HEADER, false );
     curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
     curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
     $respData = curl_exec( $ch );
     curl_close( $ch );
     return $respData;

 }



 /**
  * @function isValidDate
  * @description to check is date in valid format
  *
  * @param date $date
  * @param string $format
  * @return type
  */
 function isValidDate( $date, $format ) {
     $d = DateTime::createFromFormat( $format, $date );
     return ($d && $d->format( $format ) == $date);

 }



 /**
  * @function setSessionVariables
  * @descriptoin set data array to set SESSION
  *
  * @param array $data Required data to set session
  * @param string $accessToken access token
  * @return array
  */
 function setSessionVariables( $data, $accessToken ) {

     $sessionDataArr = [
         "user_id"      => $data['user_id'],
         "device_id"    => isset( $data["device_id"] ) ? trim( $data["device_id"] ) : "",
         "device_token" => isset( $data["device_token"] ) ? trim( $data["device_token"] ) : "",
         "platform"     => isset( $data["platform"] ) ? $data["platform"] : "",
         "login_time"   => datetime(),
         "public_key"   => isset( $accessToken['public_key'] ) ? $accessToken['public_key'] : "",
         "private_key"  => isset( $accessToken['private_key'] ) ? $accessToken['private_key'] : "",
         "login_status" => ACTIVE
     ];
     return $sessionDataArr;

 }



 /**
  * @function sendAndroidPush
  * @description Send android push notification
  *
  * @param array $pushDataArr An array of Andriod devices tokens id
  * @return boolean
  */
 function sendAndroidPush( $pushDataArr ) {

     $CI        = &get_instance();
     $CI->load->library( 'commonfn' );
     $isSuccess = $CI->commonfn->androidPush( $pushDataArr['deviceTokens'], $pushDataArr['payload'] );

     return $isSuccess;

 }



 /**
  * @function sendIosPush
  * @description Send ios push notification
  *
  * @param array $pushDataArr An array of IOS devices tokens id
  * @return boolean
  */
 function sendIosPush( $pushDataArr ) {
     $CI        = &get_instance();
     $CI->load->library( 'commonfn' );
     $isSuccess = $CI->commonfn->iosPush( $pushDataArr['deviceTokens'], $pushDataArr['payload'] );
     return $isSuccess;

 }



 /**
  * @function show404
  * @description  to load error page for 4xx errors
  *
  * @param string $err_msg Error message to show
  * @param string $redurl Redirect URL
  */
 function show404( $err_msg = "", $redurl = 'admin' ) {
     $data['err_msg'] = (empty( $err_msg )) ? 'Invalid Request' : $err_msg;
     $data['redurl']  = $redurl;
     #echo $err_msg;
     #die('<a href="' . $redurl . '"><br>Click here to redirect</a>');
     header( "HTTP/1.0 404 Not Found" );
     $CI              = &get_instance();
     $CI->load->view( '/admin/header' );
     $CI->load->view( '/admin/error', $data );
     $CI->load->view( '/admin/footer' );

 }



 /**
  * @function load_views_web
  * @descrition Load Web views
  *
  * @param string $customView view name to load (With path)
  * @param aray $data data to render on view
  */
 function load_views_web( $customView, $data = array () ) {
     $CI = &get_instance();
     $CI->load->view( 'header', $data );
     $CI->load->view( $customView, $data );
     $CI->load->view( 'footer', $data );

 }



 /**
  * @funciton RccErrorHandler
  * @description Will record all error, notice, warning and user triggered error also
  *
  * @param string/int $errno
  * @param string $errstr error message
  * @param string $errfile file name in which error is generated
  * @param int $errline file line no. on which error is generated
  * @param array $error_context array object with some additional information for error
  * @return int 0;
  */
 function RccErrorHandler( $errno = "", $errstr = "", $errfile = "", $errline = "'" ) {

     $data['url']       = $_SERVER['REDIRECT_URL'];
     $data['file_path'] = isset( $_SERVER['ORIG_PATH_INFO'] ) ? $_SERVER['ORIG_PATH_INFO'] : '';
     $data['file_name'] = $errfile;
     $data['line']      = $errline;

     switch ( $errno ) {
         case 2:
             $data['error_no'] = "Non-fatal run-time errors. Execution of the script is not halted";
             break;
         case 8:
             $data['error_no'] = "Run-time notices. The script found something that might be an error, but could also happen when running a script normally";
             break;
         case 256:
             $data['error_no'] = "Fatal user-generated error. This is like an E_ERROR set by the programmer";
             break;
         case 512:
             $data['error_no'] = "Non-fatal user-generated warning. This is like an E_WARNING set by the programmer";
             break;
         case 1024:
             $data['error_no'] = "User-generated notice. Triggered manually 1024";
             break;
         case 4096:
             $data['error_no'] = "Catchable fatal error. This is like an E_ERROR but can be caught by a user ";
             break;
         case 8191:
             $data['error_no'] = "All errors and warnings ";
             break;
         default :
             $data['error_no'] = $errno;
     }

     $data['msg'] = $errstr;
     __errorLogging( $data );
     if ( $errno === E_WARNING ) {
         throw new Exception( $errstr );
         return false;
     }

     showException( $errstr );
     return 0;

 }



 if ( !function_exists( '__errorLogging' ) ) {

     /**
      *
      * @param array $msg array holding information to log error
      */
     function __errorLogging( $msg = array () ) {
         /*
          * get Instance of CI
          */
         $CI = &get_instance();

         /* Getting logging details
          * is logging is active or not
          *
          * if varible has set in config.php file then get value from it
          * else it will set to false
          */
         $logging = empty( $CI->config->item( 'logging' ) ) ? FALSE : $CI->config->item( 'logging' );


         /*
          * getting logging saving loction
          * in DB or in file
          *
          * if variable has set in config.php then getting value from it
          * else it will set to false
          */
         $logging_in_db = empty( $CI->config->item( 'logging_in_db' ) ) ? FALSE : $CI->config->item( 'logging_in_db' );


         if ( $logging ) {

             $data['url']       = $msg['url'];
             $data['file_path'] = $msg['file_path'];
             $data['file_name'] = $msg['file_name'];
             $data['line']      = $msg['line'];
             $data['error_no']  = $msg['error_no'];
             $data['msg']       = empty( $msg ) ? " No message setted" : $msg['msg'];

             if ( !$logging_in_db ) {
                 writeLogInFile( $data );
             }
             else {

             }
         }

     }



 }

 /**
  * @function writeLogInFile
  * @description if any log want to written on file
  *
  * @param type $data
  * @return boolean
  */
 function writeLogInFile( $data = array () ) {
     /*
      * get Instance of CI
      */
     $CI = &get_instance();

     $logging       = empty( $CI->config->item( 'logging' ) ) ? FALSE : $CI->config->item( 'logging' );
     ;
     $logging_in_db = empty( $CI->config->item( 'logging_in_db' ) ) ? FALSE : $CI->config->item( 'logging_in_db' );

     if ( $logging_in_db === TRUE && $logging === FALSE ) {
         return FALSE;
     }

     $log_path = empty( $CI->config->item( 'logging_file_path' ) ) ? "/error_logs" : $CI->config->item( 'logging_file_path' );
     $fileName = "error_".date( "Y_m_d" ).".txt";


     /*
      * Checking is logging folder is exixts or not
      * if not then create
      */
     file_exists( getcwd().$log_path ) OR mkdir( getcwd().$log_path, 0755, TRUE );

     $filepath = getcwd().$log_path."/".$fileName;

     if ( !$file = @fopen( $filepath, 'a' ) ) {
         return FALSE;
     }


     /*
      * Lock file exclusive
      */
     flock( $file, LOCK_EX );
     $message = "";
     $message .= "================".date( "Y-m-d H:i:s" )." ================ \r\n";

     $message .= "URL : ".$data['url']."\r\n";
     $message .= "File Path : ".$data['file_path']."\r\n";
     $message .= "File : ".$data['file_name']."\r\n";
     $message .= "Line no. : ".$data['line']."\r\n";
     $message .= "Message : ".$data['msg']."\r\n";
     $message .= "Error No. : ".$data['error_no']."\r\n";
     $message .= "================================ \r\n\r\n\r\n";

     fwrite( $file, $message );

     /*
      * Unlock file exclusive
      */
     flock( $file, LOCK_UN );


     /*
      * Close open file instance
      */
     fclose( $file );

 }



 /**
  * @funciton base64encryption
  * @description will Encrypt data in base64
  *
  * @param type $string
  */
 function base64encryption( $string ) {
     return base64_encode( $string );

 }



 /**
  * @funciton base64decryption
  * @description will decrypt data in base64
  *
  * @param type $string
  */
 function base64decryption( $string ) {
     return base64_decode( $string );

 }



 /**
  * @function queryStringBuilder
  * @desc will convert query string into encrypted string
  *
  * @param sting/array $QueryString
  * @return string encrypted query string
  */
 function queryStringBuilder( $QueryString ) {

     if ( !is_array( $QueryString ) ) {
         $QueryString = queryStringToArray( $QueryString );
     }

     return encryptDecrypt( json_encode( $QueryString ) );

 }



 /**
  * @function getRequertParams
  * @description to convert encrypted query string to array
  *
  * @param string $string
  * @return array Array of Requested parameters
  */
 function getRequestParams( $string ) {

     if ( !empty( $string ) ) {

         $temp = encryptDecrypt( $string, 'decrypt' );

         // validate JSON
         if ( !jsonValidation( $temp ) ) {
             // If JSON is not valid than error will triggred
             log_message( 'error', sprintf( '%s : %s : Query String tempered.', __CLASS__, __FUNCTION__ ) );
             trigger_error( "Query String tempered => STRING : ".$string." ||  Converted : => ".$temp );
             exit;
         }

         // JSON to Array Conversion
         $encQuery = json_decode( $temp, true );

         foreach ( $encQuery as $key => $value ) {
             $_GET[$key] = $value;
         }
         unset( $_GET['data'] );
     }

     return NULL;

 }



 /**
  * @function queryStringToArray
  * @description Function will convert query string into array
  *
  * @param string $queryString query string or array as input from calling function
  * @return array
  *
  * Ex :
  * queryString : "id=1&type=a"
  * result  : array("id"=>1, "type" => a)
  */
 function queryStringToArray( $queryString ) {

     if ( is_array( $queryString ) ) {
         return $queryString;
     }

     parse_str( trim( $queryString ), $temp );
     return $temp;

 }



 /**
  * @function jsonValidation
  * @description function to validate input JSON
  *
  * @param string $string String in JOSN Format
  */
 function jsonValidation( $string ) {
     json_decode( $string );
     switch ( json_last_error() ) {
         case JSON_ERROR_NONE:
             return true;
             break;
         case JSON_ERROR_DEPTH:
             return false;
             break;
         case JSON_ERROR_STATE_MISMATCH:
             return false;
             break;
         case JSON_ERROR_CTRL_CHAR:
             return false;
             break;
         case JSON_ERROR_SYNTAX:
             return false;
             break;
         case JSON_ERROR_UTF8:
             return false;
             break;
         default:
             return false;
             break;
     }

 }



 /**
  * @function twitter_auth
  * @description function for twitter login authentication
  *
  * @param none
  */
 function twitter_auth() {
     try {
         /* load twitter sdk libraries */
         include_once APPPATH."libraries/Twitteroauth.php";
         include_once APPPATH."libraries/tmhoauth.php";
         include_once APPPATH."libraries/tmhutilities.php";

         $var                     = new tmhutilities();
         $here                    = $var->php_self();
         $tmhOAuth                = new tmhoauth( array ('consumer_key' => TWITTER_CONSUMER_TOKEN, 'consumer_secret' => TWITTER_CONSUMER_SECRET, 'user_token' => '', 'user_secret' => '') );
         $callback                = base_url().'web/twitter';
         $params                  = array ('oauth_callback' => $callback);
         $_REQUEST['force_write'] = 1;

         if ( isset( $_GET['force_write'] ) && !empty( $_GET['force_write'] ) ) :
             $params['x_auth_access_type'] = 'write';
         elseif ( isset( $_GETs['force_read'] ) && !empty( $_GETs['force_read'] ) ) :
             $params['x_auth_access_type'] = 'read';
         endif;

         if ( !isset( $_GET['oauth_verifier'] ) ) {
             $code = $tmhOAuth->request( 'POST', $tmhOAuth->url( 'oauth/request_token', '' ), $params );
             if ( $code == 200 ) {
                 $_REQUEST['authenticate'] = 1;
                 $_REQUEST['force']        = 1;
                 $_SESSION['oauth']        = $tmhOAuth->extract_params( $tmhOAuth->response['response'] );
                 $method                   = isset( $_REQUEST['authenticate'] ) ? 'authenticate' : 'authorize';
                 $force                    = isset( $_REQUEST['force'] ) ? '&force_login=1' : '';
                 return $authurl                  = $tmhOAuth->url( "oauth/{$method}", '' )."?oauth_token={$_SESSION['oauth']['oauth_token']}{$force}";
                 // $data['twit'] = $authurl;
                 //  return $data['twit'];
             }
         }
     }
     catch ( Exception $ex ) {
         echo $ex->getMesssage();
         die;
     }

 }



 /**
  * @funciton showException
  * @description ||to display Exception Errors ||
  *              ||if Environment is development then it will show exact Error
  *              ||otherwise it will show Something went wrong page
  */
 function showException( $errMsg = "" ) {
     log_message( 'error', sprintf( '%s : %s : %s: .', __CLASS__, __FUNCTION__, $errMsg ) );
     switch ( ENVIRONMENT ) {
         case 'development':
             echo $errMsg;
             die;
             break;
         default:
             redirect( "/admin/NotFound/" );
     }

 }



 /**
  * Used to set default values to needed array index
  * pass to array First for values and Second for default values with same indexes
  * If in value array index has value then Ok
  * Otherwise second array index value will set
  *
  *
  * @function defaultValue
  * @description To set default value to the arrays required fields
  *
  * @param array $value array to check values
  * @param array $default Array having default values
  */
 function defaultValue( $value = array (), $default = array () ) {
     $response = array ();
     foreach ( array_keys( $default ) as $key ) {
         $response[$key] = (isset( $value[$key] ) && !empty( $value[$key] )) ? $value[$key] : $default[$key];
     }
     return( $response );
     exit();

 }



 /**
  * To set Formatted date time
  *
  * @funciton setDate
  * @description TO set formatted date time
  *
  * @param string $date
  * @return date
  */
 function setDate( $date ) {
     return date( "d M Y H:i A", strtotime( $date ) );

 }



 /**
  * @function setDefaultPermission
  * @description Set Default Permissions if hook is not working
  *
  * @param array $array return default permissions array
  */
 function setDefaultPermission() {
     $GLOBALS['permission']['action'] = true;
     $CI                              = &get_instance();
     $CI->config->load( 'ACL_config', TRUE );

     //Fetching Permission from Config File
     $acl_config = $CI->config->item( 'permission', 'ACL_config' );

     $method = [];
     #getting all method
     foreach ( $acl_config as $value ) {
         foreach ( $value as $access_key => $access_array ) {
             $method[strtolower( $access_array['class'] )][$access_key] = $access_array['method'];
         }
     }

     foreach ( $method as $mth ) {
         foreach ( array_keys( $mth ) as $cls ) {
             $$cls                                        = $cls;
             $GLOBALS['permission'][$$cls]                = "style='visibility:visible'";
             $GLOBALS['permission']['permissions'][$$cls] = true;
         }
     }

 }



 /**
  * @function show403
  * @description to show access denied page | 403
  *
  * @param string $err_msg Message to Show on page
  */
 function show403( $err_msg = "", $redurl = "" ) {
     $data['err_msg'] = (empty( $err_msg )) ? 'Access denied, Contact to administrator' : $err_msg;
     $data['redurl']  = $redurl;
     #echo $err_msg;
     #die('<a href="' . $redurl . '"><br>Click here to redirect</a>');
     header( "HTTP/1.0 403 Forbidden" );
     $CI              = &get_instance();
     $CI->load->view( '/admin/header' );
     $CI->load->view( '/admin/pageaccessdenied', $data );
     $CI->load->view( '/admin/footer' );

 }



 /**
  * Checks for empty parameters
  * @param array $data
  *
  * @param array $mandatoryFields
  * @return array error status
  */
 if ( !function_exists( 'check_empty_parameters' ) ) {

     function check_empty_parameters( $data, $mandatoryFields ) {
         foreach ( $mandatoryFields as $value ) {
             if ( !isset( $data[$value] ) || empty( trim( $data[$value] ) ) ) {
                 return [
                     "error"     => true,
                     "parameter" => $value
                 ];
             }
         }

         return [
             "error" => false
         ];

     }



 }
?>
