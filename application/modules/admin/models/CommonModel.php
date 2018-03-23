<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

 class CommonModel extends CI_Model {

     public function __construct() {
         $this->load->database();
         $this->load->library( "session" );
         $this->load->helper( "cookie" );

     }



     /**
      * Removes empty fields if they are empty or not set.
      *
      * @param array $dataArray holds the data for which the fields will be unset should they be empty
      * @param array $referenceArray holds field key to identify checks
      *
      * @return array new dataArray for update field
      */
     public function removeEmptyFields( $dataArray, $referenceArray ) {
         foreach ( $referenceArray as $reference ) {
             if ( isset( $dataArray[$reference] ) && !empty( $dataArray[$reference] ) ) {
                 $dataArray[$reference] = trim( $dataArray[$reference] );
             }
             elseif ( empty( $dataArray[$reference] ) ) {
                 unset( $dataArray[$reference] );
             }
         }

         return $dataArray;

     }



     /**
      * checks for required field in an post, put or in general array
      *
      * @access public
      * @param array $data POST or PUT array
      * @param array $mandatory Mandatory fields in a POST or PUT array
      *
      * @return array error status and missing field
      */
     public function checkRequiredFields( $data, $mandatory, $checkForEmpty = false ) {
         $error = false;
         foreach ( $mandatory as $value ) {
             if ( !array_key_exists( $value, $data ) || empty( $data[$value] ) ) {
                 $error = true;
                 break;
             }
         }

         $returnArray = [];

         if ( !$error ) {
             $emptyError = false;
             if ( $checkForEmpty ) {
                 foreach ( $data as $datum ) {
                     if ( !isset( $datum ) || empty( $datum ) ) {
                         $emptyError = true;
                     }
                 }
             }

             if ( $emptyError ) {
                 $returnArray = [
                     "error" => true
                 ];
             }
             else {
                 $returnArray = [
                     "error" => false
                 ];
             }
         }
         else {
             $returnArray = [
                 "error"   => true,
                 "message" => $this->lang->line( "missing_parameter" )
             ];
         }

         return $returnArray;

     }



     /**
      * Checks for Null or Empty value using reference array
      *
      * @param array $data DATA to check
      * @param array $referenceArray Reference array to check from
      *
      * @return bool returns true if all values of $referenceArray are set and not empty in $data, false otherwise.
      */
     public function nullOrEmpty( $data, $referenceArray ) {
         foreach ( $referenceArray as $value ) {
             if ( !isset( $data[$value] ) || empty( $data[$value] ) ) {
                 return true;
             }
         }
         return false;

     }



     /**
      * Converts Datetime formate form one timezone to another
      * @param string $dateFormat
      * @param string $dateTime
      * @param string $requiredFormat
      * @param string $timezoneFrom
      * @param string $timezoneTo
      *
      * @return string
      */
     public function convertDateTimeFormat( $dateFormat, $dateTime, $requriedFormat = "Y-m-d H:i:s", $timezoneFrom = "UTC", $timezoneTo = "UTC" ) {
         try {
             $date = DateTime::createFromFormat( $dateFormat, $dateTime, new DateTimeZone( $timezoneFrom ) );
         }
         catch ( Exception $error ) {
             $date = DateTime::createFromFormat( $dateFormat, $dateTime, new DateTimeZone( "utc" ) );
         }

         try {
             $date->setTimezone( new DateTimeZone( $timezoneTo ) );
         }
         catch ( Exception $error ) {
             $date->setTimezone( new DateTimeZone( "utc" ) );
         }

         return $date->format( $requriedFormat );

     }



     /**
      * Validates Email Field
      *
      * @access public
      * @param string $email email string
      *
      * @return bool return true if valid email format, false otherwise
      */
     public function validateEmail( $email ) {
         return ( bool ) preg_match( "`^[a-z0-9!#$%&'*+\/=?^_\`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_\`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$`i", trim( $email ) );

     }



     /**
      * Validates field values with regexp patterns
      * @access public
      * @param array $field Field values key with pattern value array
      *                     [$postData['field1']=>'^\d+$', $postData['field2']=>'^[a-zA-Z]+$']
      *
      * @return bool true if no error, false otherwise
      */
     public function validateField( $field ) {
         $success = true;
         foreach ( $field as $key => $value ) {
             if ( !preg_match( $value, $key ) ) {
                 $success = false;
             }
             else {
                 $success = true;
             }
         }

         return $success;

     }



     /**
      * Checks for valid date and age
      *
      * @access public
      *
      * @param int $day Day
      * @param int $month Month
      * @param int $year Year
      * @param int $requiredAge Minimum Required Age, defaults to 18
      *
      * @return array An array with "error" status and relavent message
      */
     public function checkAge( $day, $month, $year, $requiredAge = 18 ) {
         $returnArray = [];
         if ( checkdate( $month, $day, $year ) ) {
             $age = ( ( ( (time() - strtotime( "{$year}-{$month}-{$day}" ))/*                  * timestamp */ / 365 )/*                  * 365 */ / 24 )/*                  * 24 */ / 60 )/*                  * 60 */
                 / 60;

             if ( $age < $requiredAge ) {
                 $returnArray = [
                     "error"   => true,
                     "message" => "underage"
                 ];
             }
             else {
                 $returnArray = [
                     "error"   => false,
                     "data"    => $age,
                     "message" => "success"
                 ];
             }
         }
         else {
             $returnArray = [
                 "error"   => true,
                 "message" => "invalid date"
             ];
         }

         return $returnArray;

     }



     /**
      * Validates Names according to regexp pattern
      *
      * @param string $name name string
      * @param string $pattern regexp pattern, defaults to "^[a-zA-Z0-9\s]+$"
      *
      * @return bool true if valid name, false otherwise
      */
     public function validateName( $name, $pattern = "^[a-zA-Z0-9][a-zA-Z0-9\s]{0,99}$" ) {
         return ( bool ) preg_match( "/{$pattern}/", $name );

     }



     /**
      * Generates random string of specified length with or without base24 encoding
      *
      * @access public
      *
      * @param bool $base64encode If random string should be base64encoded
      * @param int $length length of random string
      *
      * @return string Random string
      */
     public function generateRandomString( $base64encode = false, $length = 10 ) {
         $sourceString = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
         $randomString = "";
         for ( $i = 0; $i < $length; $i++ ) {
             $randomString .= $sourceString[mt_rand( 0, strlen( $sourceString ) - 1 )];
         }

         if ( $base64encode ) {
             $randomString = base64_encode( $randomString );
         }

         return $randomString;

     }



     /**
      * HANDLES CURL REQUESTS
      *
      * @param string $url Enter URL to which curl will send request
      * @param array $data Data to be sent as post parameters, ["field" => "value"]
      * @param array $headers HTTP header strings ["header1", "header2"]
      *
      * @return mixed curl response
      */
     public function curlPostRequest( $url, $data, $headers = [] ) {
         $ch = curl_init( $url );
         curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
         if ( count( $headers ) > 0 ) {
             curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
         }
         curl_setopt( $ch, CURLOPT_POST, true );
         curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $data ) );

         $response = curl_exec( $ch );

         curl_close( $ch );

         return $response;

     }



     /**
      * Sends Async CURL requests
      * @param string $url Enter URL to which curl will send request
      * @param array $data Data to be sent as post parameters, ["field" => "value"]
      * @param array $headers HTTP header strings ["header1", "header2"]
      *
      * @return mixed curl response
      */
     public function asyncCurlPost( $url, $data, $headers = [] ) {
         $curl = curl_init( $url );
         curl_setopt( $curl, CURLOPT_RETURNTRANSFER, false );
         if ( count( $headers ) > 0 ) {
             curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
         }
         curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
         curl_setopt( $curl, CURLOPT_AUTOREFERER, true );
         curl_setopt( $curl, CURLOPT_TIMEOUT, 1 );
         curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
         curl_setopt( $curl, CURLOPT_FRESH_CONNECT, true );   // Always ensure the connection is fresh
         curl_setopt( $curl, CURLOPT_HEADER, false );         // Don't retrieve headers
         curl_setopt( $curl, CURLOPT_NOBODY, true );          // Don't retrieve the body
         curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, 10 );
         ignore_user_abort( true );
         #$contents = curl_exec( $curl );
         curl_setopt( $curl, CURLOPT_POSTFIELDS, $data );

         $response = curl_exec( $curl );

         curl_close( $curl );

         return $response;

     }



     /**
      * Converts date from UTC to another timezone
      *
      * @param
      * @param
      * @param
      *
      */
     public function convertDate() {

     }



     /**
      * Send Mail using PHPMailer
      *
      * @access private
      * @param array $toName Name and Email as Key Value Pair eg. ["John" => "john@aol.com"]
      * @param string $subject Subject of Mail
      * @param string $body Body of Mail
      * @param string $altBody Alternate Body should the user be using a Mail client that doesn't render HTML
      *
      * @return array with message status ["error"=>true, "message" => "error message"]
      */
     public function sendMail( $toName, $subject, $body, $altBody = "" ) {
         $mail = new PHPMailer;

         //$mail->SMTPDebug = 3;

         $mail->isSMTP();
         $mail->Host       = 'smtp.gmail.com';
         $mail->SMTPAuth   = true;
         $mail->Username   = 'rana.amritanshu.appinventiv@gmail.com';
         $mail->Password   = 'App@20171234';
         $mail->SMTPSecure = 'tls';
         $mail->Port       = 587;

         $mail->setFrom( "rana.amritanshu.appinventiv@gmail.com", "seerve" );
         $mail->addAddress( $toName[key( $toName )], key( $toName ) );

         // $mail->addAttachment('/var/tmp/file.tar.gz');
         // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');
         $mail->isHTML( true );

         $mail->Subject = $subject;
         $mail->Body    = $body;
         $mail->AltBody = $altBody;

         if ( !$mail->send() ) {
             return [
                 "error"   => true,
                 "message" => "Mailer Error".$mail->ErrorInfo
             ];
         }
         else {
             return [
                 "error"   => false,
                 "message" => "Message successfull sent"
             ];
         }

     }



     /**
      * Inserts Data into database
      *
      * @param array $data Data to be inserted into database
      * @param string $tableName Table Name
      * @param bool $returnLastInsertId Return Last Insert Id when set to true
      *
      * @return bool|int|string Return TRUE|Last Insert Id on successful insertion, FALSE otherwise.
      */
     public function insertData( $data, $tableName, $returnLastInsertId = false ) {
         if ( $this->db->set( $data )->insert( $tableName ) ) {
             if ( $this->db->affected_rows() ) {
                 if ( $returnLastInsertId == true ) {
                     return $this->db->insert_id();
                 }
                 else {
                     return true;
                 }
             }
             else {
                 return false;
             }
         }
         else {
             return false;
         }

     }



     /**
      * Inserts Data into database but throws exception
      *
      * @param array $data Data to be inserted into database
      * @param string $tableName Table Name
      * @param bool $returnLastInsertId Return Last Insert Id when set to true
      *
      * @return bool|int|string Return TRUE|Last Insert Id on successful insertion, FALSE otherwise.
      */
     public function insertTableData( $data, $tableName, $returnLastInsertId = false ) {
         // $this->db->set($data)->insert($tableName);
         // print_r($this->db->last_query());die;
         if ( $this->db->set( $data )->insert( $tableName ) ) {
             if ( $this->db->affected_rows() ) {
                 if ( $returnLastInsertId == true ) {
                     return $this->db->insert_id();
                 }
                 else {
                     return true;
                 }
             }
             else {
                 throw new Exception( "Insert Error" );
             }
         }
         else {
             throw new Exception( "Insert Error" );
         }

     }



     /**
      * Updates Data in Database
      *
      * @param array $data Data to be inserted
      * @param string $tableName Table Name to be inserted
      * @param array $where Key Value pair of field and data eg. ["email" =>"john@aol.com"]
      * @return bool TRUE on successfull Update, FALSE otherwise.
      */
     public function updateData( $data, $tableName, $where ) {
         $this->db->set( $data );
         foreach ( $where as $key => $value ) {
             $this->db->where( $key, $value );
         }
         if ( $this->db->update( $tableName ) ) {
             if ( $this->db->affected_rows() ) {
                 return true;
             }
             else {
                 return false;
             }
         }
         else {
             return false;
         }

     }



     /**
      * Updates Data in Database but throws exception when there's an error
      *
      * @param array $data Data to be inserted
      * @param string $tableName Table Name to be inserted
      * @param array $where Key Value pair of field and data eg. ["email" =>"john@aol.com"]
      * @return bool TRUE on successfull Update, FALSE otherwise.
      */
     public function updateTableData( $data, $tableName, $where ) {
         $this->db->set( $data );
         foreach ( $where as $key => $value ) {
             $this->db->where( $key, $value );
         }
         if ( !$this->db->update( $tableName ) ) {
             throw new Exception( "Update error" );
         }
         else {
             return true;
         }

     }



     /**
      * Fetches data from table
      *
      * @param array $fields Fields to fetch
      * @param string $tableName
      * @param array $where Key Value pair of field and data eg. ["email" =>"john@aol.com"]
      *
      * @return array|bool Result Set, false if no data
      */
     public function fetchData( $fields, $tableName, $where = [], $others = [] ) {
         $this->db->select( implode( ",", $fields ), false )
             ->from( $tableName );
         foreach ( $where as $key => $value ) {
             $this->db->where( $key, $value );
         }

         /*          * foreach ($join as $key => $value) {
           $this->db->join($key, $value);
           } */

         if ( count( $others ) > 0 ) {
             if ( isset( $others["or_where"] ) && !empty( $others["or_where"] ) ) {
                 foreach ( $others["or_where"] as $key => $value ) {
                     $this->db->or_where( $key, $value );
                 }
             }
             else {

             }

             if ( isset( $others["limit"] ) && !empty( $others["limit"] ) ) {
                 if ( count( $others["limit"] ) === 1 ) {
                     $this->db->limit( $others["limit"][0] );
                 }
                 else {
                     $this->db->limit( $others["limit"][0], $others["limit"][1] );
                 }
             }
             else {

             }

             if ( isset( $others["order_by"] ) && !empty( $others["order_by"] ) ) {
                 if ( count( $others["order_by"] ) === 1 ) {
                     $this->db->order_by( $others["order_by"][0] );
                 }
                 else {
                     $this->db->order_by( $others["order_by"][0], $others["order_by"][1] );
                 }
             }
             else {

             }
             $rowCount = 0;
             if ( isset( $others["found_rows"] ) && !empty( $others["found_rows"] ) && $others["found_rows"] ) {
                 $rowCount = $this->db->query( 'SELECT FOUND_ROWS() count;' )->row()->count;
             }
             else {

             }
         }

         $query     = $this->db->get();
         $resultSet = $query->result_array();

         if ( isset( $others["found_rows"] ) && !empty( $others["found_rows"] ) && $others["found_rows"] ) {
             $resultSet["row_count"] = $rowCount;
         }
         else {

         }

         if ( count( $resultSet ) > 0 ) {
             return $resultSet;
         }
         else {
             return false;
         }

     }



     /**
      * Fetches row count
      *
      * @param array $fields
      * @param string $tableName
      * @param array $others
      *
      * @return integer row count
      */
     public function fetchCount( $fields, $tableName, $others = [] ) {
         $this->db->select( implode( ",", $fields ) )->from( $tableName );
         if ( count( $others ) > 0 ) {
             $this->variationHandler( $others );
         }
         $query = $this->db->get();

         return $query->num_rows();

     }



     /**
      * Handles variations in database queries like where, or_where, order_by, limit, offset
      *
      * @param array $others
      */
     private function variationHandler( $others ) {
         if ( count( $others ) > 0 ) {
             if ( isset( $others["where"] ) && !empty( $others["where"] ) ) {
                 foreach ( $others["where"] as $key => $value ) {
                     $this->db->where( $key, $value );
                 }
             }
             else {

             }
             if ( isset( $others["or_where"] ) && !empty( $others["or_where"] ) ) {
                 foreach ( $others["or_where"] as $key => $value ) {
                     $this->db->or_where( $key, $value );
                 }
             }
             else {

             }

             if ( isset( $others["limit"] ) && !empty( $others["limit"] ) ) {
                 if ( count( $others["limit"] ) === 1 ) {
                     $this->db->limit( $others["limit"][0] );
                 }
                 else {
                     $this->db->limit( $others["limit"][0], $others["limit"][1] );
                 }
             }
             else {

             }

             if ( isset( $others["order_by"] ) && !empty( $others["order_by"] ) ) {
                 if ( count( $others["order_by"] ) === 1 ) {
                     $this->db->order_by( $others["order_by"][0] );
                 }
                 else {
                     $this->db->order_by( $others["order_by"][0], $others["order_by"][1] );
                 }
             }
             else {

             }
         }

     }



     // public function
     /**
      * Insert batch data
      *
      * @access public
      *
      * @param array $data Data Array
      * @param string $tableName Table Name
      *
      * @return bool Return true on insert, false otherwise.
      */
     public function insertBatch( $data, $tableName, $throwException = false ) {

         if ( $this->db->insert_batch( $tableName, $data ) ) {
             return true;
         }
         else {
             if ( $throwException ) {
                 throw new Exception( "Insert Batch Error" );
             }
             else {
                 return false;
             }
         }

     }



     /**
      * Checks for running session, i.e. session data is set and redirect to required URL.
      * use for checks after user has logged out. website signup, login etc.
      * @param string $field datafield
      * @param string $redirectURL
      *
      * */
     public function checkRunningSession( $field, $redirectURL ) {
         if ( null !== $this->session->userdata( $field ) ) {
             redirect( $redirectURL );
         }

     }



     /**
      * Checks for closesd session, i.e. session data is null and redirect to required URL.
      * use for checks after user has logged in. user home, profile, account page etc.
      * @param string $field datafield
      * @param string $redirectURL
      *
      * */
     public function checkClosedSession( $field, $redirectURL ) {
         if ( null === $this->session->userdata( $field ) ) {
             redirect( $redirectURL );
         }

     }



     /**
      * Echos JSON encoded string and exits
      * @param array $data
      *
      */
     public function response( $data ) {
         echo json_encode( $data );
         exit;

     }



     /**
      * Builds http query
      *
      * @param array $params query params key value pair
      * @param bool $prependAmp prepends & if true.
      *
      * @return string http query string
      */
     public function httpQuery( $params, $prependAmp = false ) {
         $httpString = "";
         if ( $prependAmp && count( $params ) > 0 ) {
             $httpString .= "&";
         }

         $httpString .= http_build_query( $params );

         return $httpString;

     }



     /**
      * Runs Select query with given options
      *
      * @param mixed $field accept array or string
      * @param string $tableName table name
      * @param array $options other options
      *
      * @return array Data in multidimensional array
      */
     public function selectQuery( $fields, $tableName = "", $options = [] ) {
         if ( is_array( $fields ) ) {
             $this->db->select( implode( ",", $fields ) );
         }
         else {
             $this->db->select( $fields );
         }
         if ( !empty( $tableName ) ) {
             $this->db->from( $tableName );
         }
         $this->optionHandler( $options );

         $query = $this->db->get();
         if ( !$query ) {
             print_r( $this->db->last_query() );
             die;
         }
         // print_r($this->db->last_query());die;
         $resultSet = [];

         $resultSet = $query->result_array();
         if ( count( $resultSet ) > 0 ) {
             return $resultSet;
         }
         else {
             return false;
         }

     }



     private function optionHandler( $options ) {
         $arrayFlag = true;
         if ( count( $options ) === 0 || empty( $options ) || null === $options ) {
             $arrayFlag = false;
         }

         if ( !$arrayFlag ) {
             return false;
         }

         if ( isset( $options["where"] ) && !empty( $options["where"] ) ) {
             if ( is_array( $options["where"] ) ) {
                 foreach ( $options["where"] as $key => $value ) {
                     $this->db->where( $key, $value );
                 }
             }
             else {
                 $this->db->where( $options["where"] );
             }
         }

         if ( isset( $options["join"] ) && !empty( $options["join"] ) ) {
             foreach ( $options["join"] as $key => $value ) {
                 $this->db->join( $key, $value );
             }
         }

         if ( isset( $options["left_join"] ) && !empty( $options["left_join"] ) ) {
             foreach ( $options["left_join"] as $key => $value ) {
                 $this->db->join( $key, $value, 'LEFT' );
             }
         }

         if ( isset( $options["sort"] ) && !empty( $options["sort"] ) ) {
             if ( is_array( $options["sort"] ) ) {
                 foreach ( $options["sort"] as $key => $value ) {
                     $this->db->order_by( $key, $value );
                 }
             }
             else {
                 $this->db->order_by( $options["sort"], "ASC" );
             }
         }

         if ( isset( $options["limit"] ) && !empty( $options["limit"] ) ) {
             if ( !is_array( $options["limit"] ) ) {
                 $this->db->limit( $options["limit"] );
             }
             else if ( count( $options["limit"] ) === 1 ) {
                 $this->db->limit( $options["limit"][0] );
             }
             else if ( count( $options["limit"] ) === 2 ) {
                 $this->db->limit( $options["limit"][0], $options["limit"][1] );
             }
             else {
                 return false;
             }
         }

         if ( isset( $options["group_by"] ) && !empty( $options["group_by"] ) ) {
             if ( is_array( $options["group_by"] ) ) {
                 foreach ( $options["group_by"] as $value ) {
                     $this->db->group_by( $value );
                 }
             }
             else {
                 $this->db->group_by( $options["group_by"] );
             }
         }

         if ( isset( $options["order_by"] ) && !empty( $options["order_by"] ) ) {
             if ( is_array( $options["order_by"] ) ) {
                 foreach ( $options["order_by"] as $value ) {
                     $this->db->order_by( $value );
                 }
             }
             else {
                 $this->db->order_by( $options["order_by"] );
             }
         }

     }



     /**
      * Get vendor service provider subcategory lists
      * @param int|string $userType
      * @return array
      */
     public function getCategory( $userId ) {
         $data = $this->selectQuery(
             "sub_category_id, sub_category_name", "user_category",
             [
             "where" => [
                 "user_id"     => $userId,
                 "is_approved" => 1
             ],
             "join"  => [
                 "services_sub_category" => "services_sub_category.category_id=user_category.category_id"
             ]
             ]
         );

         if ( $data ) {
             return $data;
         }
         else {
             return [];
         }

     }



     /**
      * Validates given Cookie
      * @param string $cookieName
      * @param string $tableName
      * @param array $sessionFields Session fields
      * @param array $dataFields DB data field
      * @param string $dataFields DB data field
      *
      * @return array|bool returns array if valid cookie
      */
     public function validateCookie( $cookieName, $tableName, $sessionFields, $dataFields, $hashingField = "create_date" ) {
         $loginCookie     = get_cookie( $cookieName );
         $tableFields     = implode( ",", $dataFields );
         $additionalField = !empty( $hashingField ) ? ",".$hashingField : "";
         $tableFields     = $tableFields.",cookie_validator".$additionalField;
         $cookieData      = $this->getDataFromCookie( $loginCookie, $tableName, $tableFields );

         if ( !$cookieData ) {
             return false;
         }

         unset( $cookieData[$hashingField] );

         $userData = array_combine( $sessionFields, $cookieData );

         return $userData;

     }



     /**
      * Gets user data from given cookie
      * @access public
      * @param string $cookie
      * @param string $tableName
      * @param string $fields
      * @return bool|array
      */
     public function getDataFromCookie( $cookie, $tableName, $fields ) {
         if ( !isset( $cookie ) || empty( $cookie ) ) {
             return false;
         }

         $loginCookie = $cookie;
         $loginCookie = explode( ":", $loginCookie );

         if ( is_array( $loginCookie ) && count( $loginCookie ) != 2 ) {
             return false;
         }

         $cookieData = $this->selectQuery( $fields, $tableName, [
             "where" => [
                 "cookie_selector" => $loginCookie[0]
             ]
             ] );

         if ( !$cookieData ) {
             return false;
         }

         $cookieData = $cookieData[0];

         $validator             = $cookieData["cookie_validator"];
         $hashedCookieValidator = hash( "sha256", $loginCookie[1].$cookieData["create_date"] );

         if ( $validator !== $hashedCookieValidator ) {
             return false;
         }

         unset( $cookieData["cookie_validator"] );

         return $cookieData;

     }



 }
