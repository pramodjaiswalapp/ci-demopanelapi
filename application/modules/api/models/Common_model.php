<?php

 class Common_model extends CI_Model {

     public $totalrows = array ();

     public function __construct() {
         $this->load->database();
         $this->load->library( 'session' );

     }



     /**
      * Fetch data from any table based on different conditions
      *
      * @access	public
      * @param	string
      * @param	string
      * @param	array
      * @return	bool
      */
     public function fetch_data( $table, $fields = '*', $conditions = array (), $returnRow = false ) {
         //Preparing query
         $this->db->select( $fields, false );
         $this->db->from( $table );

         //If there are conditions
         if ( count( $conditions ) > 0 ) {
             $this->condition_handler( $conditions );
         }
         $query = $this->db->get();

         $this->totalrows = $this->db->query( 'SELECT FOUND_ROWS() count;' )->row()->count;
         return $returnRow ? $query->row_array() : $query->result_array();

     }



     /**
      * Insert data in DB
      *
      * @access	public
      * @param	string
      * @param	array
      * @param	string
      * @return	string
      */
     public function insert_single( $table, $data = array () ) {
         //Check if any data to insert
         if ( count( $data ) < 1 ) {
             return false;
         }

         $this->db->insert( $table, $data );

         return $this->db->insert_id();

     }



     public function insert_multiple( $table, $data = array () ) {
         //Check if any data to insert
         if ( count( $data ) < 1 ) {
             return false;
         }
         $this->db->insert_batch( $table, $data );

         return true;

     }



     /**
      * Insert batch data
      *
      * @access	public
      * @param	string
      * @param	array
      * @param	array
      * @param	bool
      * @return	bool
      */
     public function insert_batch( $table, $defaultArray, $dynamicArray = array (), $updatedTime = false ) {
         //Check if default array has values
         if ( count( $dynamicArray ) < 1 ) {
             return false;
         }

         //If updatedTime is true
         if ( $updatedTime ) {
             $defaultArray['UpdatedTime'] = time();
         }

         //Iterate it
         foreach ( $dynamicArray as $val ) {
             $updates[] = array_merge( $defaultArray, $val );
         }
         return $this->db->insert_batch( $table, $updates );

     }



     /**
      * Delete data from DB
      *
      * @access	public
      * @param	string
      * @param	array
      * @param	string
      * @return	string
      */
     public function delete_data( $table, $conditions = array () ) {
         //If there are conditions
         if ( count( $conditions ) > 0 ) {
             $this->condition_handler( $conditions );
         }
         return $this->db->delete( $table );

     }



     /**
      * Handle different conditions of query
      *
      * @access	public
      * @param	array
      * @return	bool
      */
     private function condition_handler( $conditions ) {
         //Custom Where
         if ( array_key_exists( 'customWhere', $conditions ) ) {
             $this->db->where( $conditions['customWhere'] );
         }
         //Where
         if ( array_key_exists( 'where', $conditions ) ) {

             //Iterate all where's
             foreach ( $conditions['where'] as $key => $val ) {
                 $this->db->where( $key, $val );
             }
         }
         if ( isset( $conditions["join"] ) && !empty( $conditions["join"] ) ) {
             foreach ( $conditions["join"] as $key => $value ) {
                 $this->db->join( $key, $value );
             }
         }

         if ( isset( $conditions["left_join"] ) && !empty( $conditions["left_join"] ) ) {
             foreach ( $conditions["left_join"] as $key => $value ) {
                 $this->db->join( $key, $value, 'LEFT' );
             }
         }
         //Where OR
         if ( array_key_exists( 'or_where', $conditions ) ) {

             //Iterate all where or's
             foreach ( $conditions['or_where'] as $key => $val ) {
                 $this->db->or_where( $key, $val );
             }
         }

         //Where In
         if ( array_key_exists( 'where_in', $conditions ) ) {

             //Iterate all where in's
             foreach ( $conditions['where_in'] as $key => $val ) {
                 $this->db->where_in( $key, $val );
             }
         }

         //Where Not In
         if ( array_key_exists( 'where_not_in', $conditions ) ) {

             //Iterate all where in's
             foreach ( $conditions['where_not_in'] as $key => $val ) {
                 $this->db->where_not_in( $key, $val );
             }
         }

         //Having
         if ( array_key_exists( 'having', $conditions ) ) {
             $this->db->having( $conditions['having'] );
         }

         //Group By
         if ( array_key_exists( 'group_by', $conditions ) ) {
             $this->db->group_by( $conditions['group_by'] );
         }

         //Order By
         if ( array_key_exists( 'order_by', $conditions ) ) {

             //Iterate all order by's
             foreach ( $conditions['order_by'] as $key => $val ) {
                 $this->db->order_by( $key, $val );
             }
         }

         //Order By
         if ( array_key_exists( 'like', $conditions ) ) {

             //Iterate all likes
             foreach ( $conditions['like'] as $key => $val ) {
                 $this->db->like( $key, $val );
             }
         }

         //Limit
         if ( array_key_exists( 'limit', $conditions ) ) {

             //If offset is there too?
             if ( count( $conditions['limit'] ) == 1 ) {
                 $this->db->limit( $conditions['limit'][0] );
             }
             else {
                 $this->db->limit( $conditions['limit'][0], $conditions['limit'][1] );
             }
         }

     }



     /**
      * Update Batch
      *
      * @access	public
      * @param	string
      * @param	array
      * @return	boolean
      */
     public function update_batch_data( $table, $defaultArray, $dynamicArray = array (), $key ) {
         //Check if any data
         if ( count( $dynamicArray ) < 1 ) {
             return false;
         }

         //Prepare data for insertion
         foreach ( $dynamicArray as $val ) {
             $data[] = array_merge( $defaultArray, $val );
         }
         return $this->db->update_batch( $table, $data, $key );

     }



     /**
      * Update details in DB
      *
      * @access	public
      * @param	string
      * @param	array
      * @param	array
      * @return	string
      */
     public function update_single( $table, $updates, $conditions = array () ) {
         //If there are conditions
         if ( count( $conditions ) > 0 ) {
             $this->condition_handler( $conditions );
         }
         return $this->db->update( $table, $updates );

         return $this->db->affected_rows();

     }



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
      * Count all records
      *
      * @access	public
      * @param	string
      * @return	array
      */
     public function fetch_count( $table, $conditions = array () ) {
         $this->db->from( $table );
         //If there are conditions
         if ( count( $conditions ) > 0 ) {
             $this->condition_handler( $conditions );
         }
         return $this->db->count_all_results();

     }



     /**
      *
      * @param string $email e-mail address
      * @param string $subject Email Subject
      * @param string $message Message body for email
      * @param string $single
      * @param string $param
      * @param string $templet
      * @return type
      */
     public function sendmailnew( $email, $subject, $message = false, $single = true, $param = false, $templet = false ) {
         if ( $single == true ) {
             $this->load->library( 'email' );
         }

         $this->config->load( 'email' );
         $this->email->from( $this->config->item( 'from' ), $this->config->item( 'from_name' ) );
         $this->email->reply_to( $this->config->item( 'repy_to' ), $this->config->item( 'reply_to_name' ) );
         $this->email->to( $email );
         $this->email->subject( $subject );
         if ( $param && $templet ) {
             $body = $this->load->view( 'mail/'.$templet, $param, TRUE );
             $this->email->message( $body );
         }
         else {
             $this->email->message( $message );
         }
         return $this->email->send() ? true : false;

     }



     /**
      * For sending mail
      *
      * @access	public
      * @param	string
      * @param	string
      * @param	string
      * @param	boolean
      * @return	array
      */
     public function sendmail( $email, $subject, $message = false, $single = true, $param = false, $templet = false ) {

         if ( $single == true ) {
             $this->load->library( 'email' );
         }
         $this->config->load( 'email' );
         $this->email->set_newline( "\r\n" );
         $this->email->from( $this->config->item( 'from_name' ), $this->config->item( 'From' ) );
         $this->email->reply_to( $this->config->item( 'Reply-To' ), $this->config->item( 'reply_to_name' ) );

         $email1 = $this->email->to( $email );
         $this->email->subject( $subject );
         if ( $templet ) {

             $this->email->message( $templet );
         }
         else {

             $this->email->message( $message );
         }
         return $this->email->send() ? true : false;

     }



     function getmcrypt_data( $input ) {
         /* Return mcrypted data */
         $key1      = "ShareSpark";
         $key2      = "Org";
         $key       = $key1.$key2;
         $encrypted = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $key ), $input, MCRYPT_MODE_CBC, md5( md5( $key ) ) ) );
         //var_dump($encrypted);
         return $encrypted;

     }



     function demcrypt_data( $input ) {
         /* Return De-mcrypted data */
         $key1      = "ShareSpark";
         $key2      = "Org";
         $key       = $key1.$key2;
         $decrypted = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $key ), base64_decode( $input ), MCRYPT_MODE_CBC, md5( md5( $key ) ) ), "\0" );
         return $decrypted;

     }



     function bcrypt_data( $input ) {
         $salt = substr( str_replace( '+', '.', base64_encode( sha1( microtime( true ), true ) ) ), 0, 22 );
         $hash = crypt( $input, '$2a$12$'.$salt );
         return $hash;

     }



     public function simplify_array( $array, $key ) {
         $returnArray = array ();
         foreach ( $array as $val ) {
             $returnArray[] = $val[$key];
         }
         return $returnArray;

     }



     //Validate date
     function validateDate( $date, $format = 'Y-m-d H:i:s' ) {
         $d = DateTime::createFromFormat( $format, $date );
         return $d && $d->format( $format ) == $date;

     }



     // for layout
     function load_views( $customView, $data = array () ) {
         // $this->load->view('admin/templates/left_panel', $data);
         $this->load->view( 'admin/templates/header', $data );
         $this->load->view( $customView, $data );
         $this->load->view( 'admin/templates/footer', $data );

     }



     /**
      * Handle Pagination
      *
      * @access	public
      */
     public function handlePagination( $totalRows ) {

         //Load Pagination Library
         $this->load->config( 'pagination' );
         $this->load->library( 'pagination' );

         //First validate if there are any rows
         if ( $totalRows > 0 ) {

             //Basic Pagination Config
             $finalSegment         = $this->uri->segment( 2 );
             $config['per_page']   = $this->config->item( 'per_page_'.$finalSegment );
             $showMore             = $this->input->get( 'show_more' );
             $pageNumber           = (!empty( $showMore ) and is_numeric( $showMore )) ? $showMore - 1 : 0;
             $start                = $config['per_page'] * $pageNumber;
             $config['total_rows'] = $totalRows;

             //Handle get params
             $additionalParams = '';
             $get              = count( $_GET ) > 0 ? $_GET : array ();
             $pageNumberKey    = $this->config->item( 'query_string_segment' );
             if ( array_key_exists( $pageNumberKey, $get ) ) {
                 unset( $get[$pageNumberKey] );
             }
             if ( count( $get ) > 0 ) {
                 $additionalParams = http_build_query( $get );
             }
             $config['base_url']      = base_url().'index.php/view/'.$finalSegment.'?'.$additionalParams;
             $config['full_tag_open'] = '<div class="row"><div class="col-sm-5"><div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Showing '.($start + 1).' to '.($start + $config['per_page']).' of '.$totalRows.' entries</div></div><div class="col-sm-7"><div class="dataTables_paginate paging_simple_numbers" id="example2_paginate"><ul class="pagination">';
             $this->pagination->initialize( $config );

             return array (
                 'totalRecords' => $config['total_rows'],
                 'startCount'   => $start
             );
         }
         else {
             return array (
                 'totalRecords' => 0,
                 'startCount'   => 0
             );
         }

     }



     /**
      *
      * @param type $length
      * @return type
      */
     public function randomstring( $length ) {
         return $a = mt_rand( 1000, 9999 );

     }



     public function sendSMS( $toArray, $text, $values = array () ) {

         /* Send SMS using PHP */

         //Your authentication key
         $authKey = "112806AdtmkKVJ57333318";

         //Multiple mobiles numbers separated by comma
         $mobileNumber = "919015347316";

         //Sender ID,While using route4 sender id should be 6 characters long.
         $senderId = "777777";

         //Your message to send, Add URL encoding here.
         $message = urlencode( "Test message" );

         //Define route
         $route    = "default";
         //Prepare you post parameters
         $postData = array (
             'authkey' => $authKey,
             'mobiles' => $mobileNumber,
             'message' => $message,
             'sender'  => $senderId,
             'route'   => $route
         );

         //API URL
         $url = "https://control.msg91.com/api/sendhttp.php";

         // init the resource
         $ch = curl_init();
         curl_setopt_array( $ch,
                            array (
             CURLOPT_URL            => $url,
             CURLOPT_RETURNTRANSFER => true,
             CURLOPT_POST           => true,
             CURLOPT_POSTFIELDS     => $postData
             //,CURLOPT_FOLLOWLOCATION => true
         ) );


         //Ignore SSL certificate verification
         curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
         curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );


         //get response
         $output = curl_exec( $ch );

         //Print error if any
         if ( curl_errno( $ch ) ) {
             echo 'error:'.curl_error( $ch );
         }

         curl_close( $ch );

         echo $output;

     }



     public function generateRandomString( $length = 30 ) {
         $characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
         $charactersLength = strlen( $characters );
         $randomString     = '';
         for ( $i = 0; $i < $length; $i++ ) {
             $randomString .= $characters[rand( 0, $charactersLength - 1 )];
         }
         return $randomString;

     }



     function sendIphonePushMessage( $deviceToken, $payload ) {

         $date        = @strtotime( date( 'Y-m-d' ) );
         $data['aps'] = $payload;
         $apnsHost    = 'gateway.sandbox.push.apple.com';
         //$apnsHost = 'gateway.push.apple.com';
         $apnsPort    = '2195';

         //$apnsCert = getcwd().'/public/ckpem/ROVO_dev.pem'; // this is for development mode (development mode)
         $apnsCert = getcwd().'/public/ckpem/pushcertdevelopment.pem'; // this is for production mode (distribution mode)
         //$passphrase = '1234';

         $ctx = stream_context_create();
         stream_context_set_option( $ctx, 'ssl', 'local_cert', $apnsCert );
         //stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
         //$fp = stream_socket_client( $apnsHost, $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
         $fp  = stream_socket_client( 'ssl://'.$apnsHost.':'.$apnsPort, $error, $errorString, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx );

         if ( !$fp ) return false;


         $sec_payload = json_encode( $data );
         $msg         = chr( 0 ).pack( 'n', 32 ).pack( 'H*', $deviceToken ).pack( 'n', strlen( $sec_payload ) ).$sec_payload;
         // Send it to the server
         $result      = @fwrite( $fp, $msg, strlen( $msg ) );
         if ( $result ) {
             //echo "true";
             return true;
         }
         else {

             return false;
         }
         fclose( $fp );

     }



     public function andriodPush( $deviceToken, $payload ) {

         ini_set( 'display_errors', '1' );
         $registrationIDs = array ($deviceToken);

         $apiKey               = 'AIzaSyDhFhYukI2Uj1RdiD-cAOBiXjc6cG4thpU'; //Please change API Key
         $url                  = 'https://android.googleapis.com/gcm/send';
         $push_data['payload'] = $payload;
         $fields               = array (
             'registration_ids' => $registrationIDs,
             'data'             => $push_data,
         );
         $headers              = array (
             'Authorization: key='.$apiKey,
             'Content-Type: application/json'
         );
         $ch                   = curl_init();
         $u                    = curl_setopt( $ch, CURLOPT_URL, $url );
         $p                    = curl_setopt( $ch, CURLOPT_POST, true );
         $f                    = curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
         $h                    = curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
         $t                    = curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
         $c                    = curl_setopt( $ch, CURLOPT_VERBOSE, true );
         curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
         $j                    = curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );
         $jsonn                = json_encode( $fields );
         $result               = curl_exec( $ch );
         curl_close( $ch );

         return $result;

     }



     public function checkParameters( $arrdata ) {
         foreach ( $arrdata as $key => $ar ) {
             if ( $ar[$key] == '' ) {

                 return false;
             }
         }

     }



     //to validate email
     public function validate_email( $e ) {
         return ( bool ) preg_match( "`^[a-z0-9!#$%&'*+\/=?^_\`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_\`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$`i", trim( $e ) );

     }



     public function encrypt( $text, $salt, $isBaseEncode = true ) {
         if ( $isBaseEncode ) {
             return trim( base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, $salt, $text, MCRYPT_MODE_ECB, mcrypt_create_iv( mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB ), MCRYPT_RAND ) ) ) );
         }
         else {
             return trim( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, $salt, $text, MCRYPT_MODE_ECB, mcrypt_create_iv( mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB ), MCRYPT_RAND ) ) );
         }

     }



     public function sendMailToUser( $email, $message, $subject = 'No Subject', $from = FROM, $replyTo = NO_REPLY ) {
         $extraKey = '-f'.$replyTo;

         $headers = 'MIME-Version: 1.0'."\r\n";
         $headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
         $headers .= 'From: '.$from.' <'.$replyTo.'>'."\r\n";

         if ( is_array( $message ) ) {
             $message = json_encode( $message );
         }

         return mail( $email, $subject, $message, $headers, $extraKey );

         /* $config = Array(
           'protocol' => 'smtp',
           'smtp_host' => 'mail.applaurels.com',
           'smtp_port' => 25,
           'smtp_user' => 'noreply@applaurels.com',
           'smtp_pass' => 'noreply@321',
           'mailtype'  => 'html',
           'charset'   => 'iso-8859-1'
           );
           $this->load->library('email', $config);
           $this->email->set_newline("\r\n");

           // Sender email address
           $this->email->from(NO_REPLY, FROM);
           // Receiver email address
           $this->email->to($email);
           // Subject of email
           $this->email->subject($subject);
           // Message in email
           $this->email->message($message);

           $result = $this->email->send(); */

     }



     /**
      * @function        getUserInfoByTable
      * @author          Pramod
      * @description     get user details
      * @param           $where
      * @data            18-11-2016
      * @return          boolean
      */
     public function getUserInfoByTable( $table, $Id, $idColumn ) {
         $this->db->select( 't.userId,t.fullName,t.email,u.deviceType,u.deviceToken,u.notificationSetting' )
             ->from( $table.' as t' )
             ->join( 'user as u', 'u.userId = t.userId', 'LEFT' );


         if ( $Id ) {
             $this->db->where( 't.'.$idColumn, $Id );
         }

         $query = $this->db->get();

         return $query->row_array();

     }



     /**
      * @name  fetch_using_join
      * @description fetch data from join
      * @param string $select
      * @param string $from
      * @param string $joinCondition
      * @param string $joinType
      * @param string $where
      * @return arrray
      */
     public function fetch_using_join( $select, $from, $join, $where, $asArray = NULL, $offset = NULL, $orderBy = NULL ) {

         $this->db->select( $select, FALSE );
         $this->db->from( $from );
         for ( $i = 0; $i < count( $join ); $i++ ) {
             $this->db->join( $join[$i]["table"], $join[$i]["condition"], $join[$i]["type"] );
         }
         $this->db->where( $where );
         if ( isset( $orderBy['order'] ) && $orderBy !== NULL ) {
             $this->db->order_by( $orderBy["order"], $orderBy["sort"] );
         }

         if ( $offset !== NULL ) {
             $this->db->limit( PAGINATION_LIMIT, $offset );
         }
         $query = $this->db->get();
         return ($asArray !== NULL) ? $query->row() : $query->result_array();

     }



     /**
      * @name rawquery
      * @access public
      * @description  Performs raw query. Optionally gives in array or object format
      * @return array/object
      */
     public function rawquery( $data, $resultArray = NULL ) {
         $query = $this->db->query( $data );
         return ($resultArray !== NULL) ? $query->result_array() : $query->row();

     }



     /**
      * @name uploadfile
      * @param type $filename
      * @param type $filearr
      * @param type $restype
      * @param type $foldername
      * @return boolean
      */
     public function uploadfile( $filename = '', $filearr, $restype = 'name', $foldername = '', $allowedType = NULL ) {

         if ( !is_dir( COMMON_UPLOAD_PATH.'/'.$foldername ) ) {
             mkdir( COMMON_UPLOAD_PATH.'/'.$foldername );
             chmod( COMMON_UPLOAD_PATH.'/'.$foldername, 0755 );
         }

         if ( $filearr[$filename]['name'] != '' ) {
             $config['upload_path'] = COMMON_UPLOAD_PATH.$foldername;
             if ( !empty( $allowedType ) ) {
                 $config['allowed_types'] = $allowedType;
             }
             else {
                 $config['allowed_types'] = '*';
             }
             $new_name            = date( 'Y/m/d' ).'_'.time().'_'.$filearr[$filename]['name'];
             $config['file_name'] = $new_name;
             $this->load->library( 'upload', $config );
             if ( $this->upload->do_upload( $filename ) ) {
                 $res = $this->upload->data();
                 if ( $restype == 'name' ) {
                     unset( $foldername );
                     return $res['file_name'];
                 }
                 elseif ( $restype == 'url' ) {
                     return COMMON_FILE_URL.$foldername.'/'.$res['file_name'];
                 }
             }
             else {
                 return false;
             }
         }

     }



     /**
      * @name createvideothumb
      * @param type $vidurl
      * @param type $restype
      * @param type $foldername
      * @return string
      */
     public function createvideothumb( $vidurl, $restype = 'name', $foldername ) {

         $newthumbnail = time().'_video_thumbnail.jpg';
         $thumbnail    = COMMON_UPLOAD_PATH.$foldername.'/'.$newthumbnail;

         // shell command [highly simplified, please don't run it plain on your script!]
         shell_exec( "ffmpeg -i $vidurl -deinterlace -an -ss 11 -t 00:00:01 -r 1 -y -vcodec mjpeg -f mjpeg $thumbnail 2>&1" );

         if ( $restype == 'name' ) {
             return $newthumbnail;
         }
         else if ( $restype == 'url' ) {
             return COMMON_FILE_URL.$foldername.'/'.$newthumbnail;
         }

     }



     /**
      * @name createImagethumb
      * @param type $filename
      * @param type $restype
      * @param type $foldername
      * @return string
      */
     public function createImagethumb( $filename, $restype = 'name', $foldername ) {

         $newthumbnail = date( 'Y/m/d' ).time().'_image_thumbnail.jpg';
         $thumbnail    = COMMON_UPLOAD_PATH.$foldername.'/'.$newthumbnail;

         $config_manip = array (
             'image_library'  => 'gd2',
             'source_image'   => COMMON_UPLOAD_PATH.$foldername.'/'.$filename,
             'new_image'      => $thumbnail,
             'maintain_ratio' => False,
             'create_thumb'   => False,
             'width'          => 100,
             'height'         => 100
         );
         $this->load->library( 'image_lib' );
         $this->image_lib->initialize( $config_manip );
         //$this->load->library('image_lib', $config_manip);

         if ( $this->image_lib->resize() ) {
             return $newthumbnail;
         }
         $this->image_lib->clear();

     }



     /**
      * @name  insertAll
      * @description function for insert_batch
      * @param string $table
      * @param array $data
      * @return boolean
      */
     public function insertAll( $table, $data ) {

         return $this->db->insert_batch( $table, $data );

     }



     public function sendFCMNotification( $devices, $message ) {
         $url     = 'https://fcm.googleapis.com/fcm/send';
         $fields  = array (
             'registration_ids' => $devices,
             'data'             => $message,
         );
         $data    = json_encode( $fields );
         $headers = array (
             'Authorization: key='."AAAAF7Ip-2I:APA91bEWcPV7JebecqFGRvUVitLJbIgc96qVkjoregT45P116DvYuLi0Q6ELiekaP9trQHe5wLmmB7rTnl_bRS9VnmAEreDkOARFG2-cNHvxMmLzlTfseN6g1InO0ck_SDYu_PRAu5oY",
             'Content-Type: application/json'
         );

         $ch     = curl_init();
         //Setting the curl url
         curl_setopt( $ch, CURLOPT_URL, $url );
         //setting the method as post
         curl_setopt( $ch, CURLOPT_POST, true );
         //adding headers
         curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
         curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
         //disabling ssl support
         curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
         //adding the fields in json format
         curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
         //finally executing the curl request
         $result = curl_exec( $ch );
         if ( $result === FALSE ) {
             die( 'Curl failed: '.curl_error( $ch ) );
         }
         //Now close the connection
         curl_close( $ch );
         //and return the result


         return $result;

     }



     /**
      *
      * @param type $to
      * @param type $body
      */
     public function sendsmsbytwillio( $To, $message ) {
         $To    = $To;
         $from  = "+12016764982
";
         $id    = "AC1bf83dd5e59115e430838752ff9682b7";
         $token = "83f14f7095c6fb56a16d51f058f09125";
         $y     = exec( "curl 'https://api.twilio.com/2010-04-01/Accounts/$id/Messages.json' -X POST \--data-urlencode 'To=+$To' \--data-urlencode 'From=+$from' \--data-urlencode 'Body=$message' \-u $id:$token" );

     }



     /*
      * Method to upload images
      *
      * @param array $files $_FILES array
      * @param string $fieldName Field Name i.e <input type="file" name="fieldName">
      * @param string $uploadPath Upload Path
      * @param array $validImage Array of valid image mime types eg. image/jpg, image/png
      * @param bool $rename Renames files to a sha256 value with extension
      *
      * @return array ["error"=> true|false, "message"=> error_message|"data" => image_name]
      */

     public function uploadImage( $files, $fieldName, $uploadPath, $fileSize = 2097152, $validImage = ["image/jpg", "image/jpeg", "image/png"], $rename = true ) {

         $image = getimagesize( $files[$fieldName]["tmp_name"] );

         if ( !$image ) {
             return [
                 "error"   => true,
                 "message" => "not an image."
             ];
         }

         if ( !in_array( $image["mime"], $validImage ) ) {
             return [
                 "error"   => true,
                 "message" => "image type not supported."
             ];
         }

         if ( $files[$fieldName]["size"] > $fileSize ) {
             return [
                 "error"   => true,
                 "message" => "file size not supported."
             ];
         }

         preg_match( "/^image\/(.*)$/", $image["mime"], $extension );
         $name = "";
         if ( $rename ) {
             $name = hash( "sha256", uniqid( "", true ) ).".".$extension[1];
         }
         else {
             $name = $files[$fieldName]["name"];
         }


         if ( move_uploaded_file( $files[$fieldName]["tmp_name"], $uploadPath.$name ) ) {
             return [
                 "error" => false,
                 "data"  => $name
             ];
         }

     }



     public function uploadImagefile( $filename = '', $filearr, $restype = 'name', $foldername = '', $uploadPath = NULL, $allowedType = NULL ) {

         if ( !is_dir( $uploadPath.'/'.$foldername ) ) {
             mkdir( $uploadPath.'/'.$foldername );
             chmod( $uploadPath.'/'.$foldername, 0755 );
         }

         if ( $filearr[$filename]['name'] != '' ) {
             $config['upload_path'] = $uploadPath.$foldername;
             if ( !empty( $allowedType ) ) {
                 $config['allowed_types'] = $allowedType;
             }
             else {
                 $config['allowed_types'] = '*';
             }
             //$new_name = date('Y/m/d').'_'.time().'_'.$filearr[$filename]['name'];
             $new_name                       = date( 'Y/m/d' ).'_'.time().'_'.$this->removeSpace( $filearr[$filename]['name'] );
             $config['file_name']            = $new_name;
             $filearr[$filename]['tmp_name'] = $new_name;
             $this->load->library( 'upload' );
             $this->upload->initialize( $config );
             if ( $this->upload->do_upload( $filename ) ) {
                 $res = $this->upload->data();
                 if ( $restype == 'name' ) {
                     unset( $foldername );
                     return $res['file_name'];
                 }
                 elseif ( $restype == 'url' ) {
                     return COMMON_FILE_URL.$foldername.'/'.$res['file_name'];
                 }
             }
             else {
                 return false;
             }
         }

     }



     public function removeSpace( $str ) {
         return str_replace( ' ', '', $str );

     }



// check Age for user
     /*
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
             $age = ( ( ( (time() - strtotime( "{$year}-{$month}-{$day}" ))/* timestamp */ / 365 )/* 365 */ / 24 )/* 24 */ / 60 )/* 60 */ / 60;

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



     /*
      * Inserts Data into database but throws exception
      *
      * @param array $data Data to be inserted into database
      * @param string $tableName Table Name
      * @param bool $returnLastInsertId Return Last Insert Id when set to true
      *
      * @returns bool|int|string Returns TRUE|Last Insert Id on successful insertion, FALSE otherwise.
      */

     public function insertTableData( $data, $tableName, $returnLastInsertId = false ) {
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



     /* notification table insert data */

     public function notificationTable( $notificationData ) {
         try {
             $id = $this->insertTableData( $notificationData, "user_notification", true );
             return $id;
         }
         catch ( Exception $error ) {
             throw new Exception( $error." - Notification Table" );
         }

     }



     /* push notification coomon function */

     public function sendPushToiphonesp( $device_detail_ios, $notificationData, $userdetail ) {

         $this->load->config( "notification_message" );

         $notificationMessages = $this->config->item( "notification_messages" );
         $iphone_push_array    = $temp                 = array ();
         if ( !empty( $device_detail_ios ) ) {
             $iphone_push_array[] = array ('alert'             => $notificationMessages[$notificationData['notification_type']],
                 'badge'             => 1,
                 'sound'             => 'default',
                 'Status'            => 1,
                 'Service_vendor_id' => $notificationData['sender_id'],
                 'type'              => $notificationData['notification_type'],
                 'time'              => strtotime( 'now' ),
                 // 'user_arrray'=>$userdata,
                 'request_id'        => $notificationData['request_id'],
                 'name'              => $userdetail['name'],
                 'phone_no'          => $userdetail['mobile_number']
             );
         }

         if ( $iphone_push_array ) {

             $this->User_Utilmodel->sendMultipleIphonePush( $device_detail_ios, $iphone_push_array );
         }

     }



     public function sendPushToandroidsp( $device_detail_android, $notificationData, $userdetail ) {

         $this->load->config( "notification_message" );
         $notificationMessages = $this->config->item( "notification_messages" );
         $android_push_array   = $temp                 = array ();
         if ( !empty( $device_detail_android ) ) {
             $android_push_array[] = array ('alert'             => $notificationMessages[$notificationData['notification_type']],
                 'badge'             => 1,
                 'sound'             => 'default',
                 'Status'            => 1,
                 'Service_vendor_id' => $notificationData['sender_id'],
                 'type'              => $notificationData['notification_type'],
                 'time'              => strtotime( 'now' ),
                 // 'user_arrray'=>$userdata,
                 'request_id'        => $notificationData['request_id'],
                 'name'              => $userdetail['name'],
                 'phone_no'          => $userdetail['mobile_number']
             );
         }

         if ( $android_push_array ) {

             $result = $this->User_Utilmodel->sendMultipleAndroidPush( $device_detail_android, $android_push_array );
         }

     }



     public function s3_uplode( $filename, $temp_name ) {
         $name = explode( '.', $filename );
         $ext  = array_pop( $name );
         $name = 'Bonapp-'.hash( 'sha1', shell_exec( "date +%s%N" ) ).'.'.$ext;

         $imgdata = $temp_name;
         $s3      = new S3( AWS_ACCESSKEY, AWS_SECRET_KEY );
         $uri     = AWS_URI.$name;
         $bucket  = AMAZONS3_BUCKET;
         $result  = $s3->putObjectFile( $imgdata, $bucket, $uri, S3::ACL_PUBLIC_READ );
         $url     = 'https://s3.amazonaws.com/'.AMAZONS3_BUCKET.'/'.$name;
         return $url;

     }



     /**
      * Generates unique token
      * @return array
      */
     public function generateRandomTokenPair() {
         $uniqueToken = uniqid( "", true );
         $uniqueToken = hash( "sha1", $uniqueToken );

         $tokenPair = [
             $uniqueToken,
             base64_encode( $uniqueToken )
         ];

         return $tokenPair;

     }



     public function sendNotification( $params ) {
         $this->db->select( 'first_name,u.user_id,s.device_token,s.platform' );
         $this->db->from( 'ai_user as u' );
         $this->db->join( 'ai_session as s', 'u.user_id=s.user_id', 'left' );
         if ( !empty( $params['platform'] ) && $params['platform'] != 1 ) {
             $platform = $params['platform'] - 1;
             $this->db->where( 's.platform', $platform );
         }
         if ( !empty( $params['gender'] ) ) {
             $this->db->where( 'u.gender', $params['gender'] );
         }

         $this->db->where( 's.login_status = 1' );
         $query = $this->db->get();

         return $query->result_array();

     }



     public function getRequest( $params ) {

         $this->db->select( 'SQL_CALC_FOUND_ROWS CONCAT(first_name," ",last_name) as name,u.user_id,IF(f.status IS NULL,"",f.status) as status', false );
         $this->db->from( 'ai_friend_request as f' );

         $this->db->join( 'ai_user as u', 'u.user_id = IF(sender_id='.$params['user_id'].',receiver_id,sender_id)', 'left', false );

         if ( $params['req_type'] == 1 ) {
             $this->db->where( "(f.status = 0 AND receiver_id = ".$params['user_id'].")" );
         }
         else if ( $params['req_type'] == 2 ) {
             $this->db->where( "(f.status = 0 AND sender_id = ".$params['user_id'].")" );
         }
         else if ( empty( $params['req_type'] ) ) {
             $this->db->where( "(f.status = 1 AND (sender_id=".$params['user_id']." OR receiver_id=".$params['user_id']."))" );
         }
         if ( !empty( $params['searchlike'] ) ) {
             $this->db->like( 'u.first_name', $params['searchlike'] );
         }
         $this->db->limit( $params['limit'], $params['offset'] );
         $query = $this->db->get();

         $respArr           = [];
         $respArr['result'] = $query->result_array();
         $respArr['count']  = $this->db->query( 'SELECT FOUND_ROWS() count;' )->row()->count;
         return $respArr;

     }



     public function getUserList( $params ) {
         $userlist_type = isset( $params['userlist_type'] ) ? $params['userlist_type'] : 1;
         if ( $userlist_type == 1 ) {
             $sql = 'SQL_CALC_FOUND_ROWS CONCAT(first_name," ",last_name) as name,u.user_id,IF(f.status IS NULL,"",f.status) as status';
         }
         else if ( $userlist_type == 2 ) {
             $sql = 'SQL_CALC_FOUND_ROWS CONCAT(first_name," ",last_name) as name,u.user_id,IF(f.id IS NULL,"",1) as status';
         }
         else if ( $userlist_type == 3 ) {
             $sql = 'SQL_CALC_FOUND_ROWS CONCAT(first_name," ",last_name) as name,u.user_id,IF(f.id IS NULL,"",1) as status';
         }
         $this->db->select( $sql, false );
         $this->db->from( 'ai_user as u' );
         if ( $userlist_type == 1 ) {
             $this->db->join( 'ai_friend_request as f', '((f.sender_id=u.user_id AND f.receiver_id='.$params['user_id'].') OR (f.sender_id='.$params['user_id'].' AND f.receiver_id=u.user_id))',
                              'left', false );
         }
         else if ( $userlist_type == 2 ) {
             $this->db->join( 'ai_follows as f', '(sender_id='.$params['user_id'].' AND receiver_id = u.user_id)', 'left', false );
         }
         else if ( $userlist_type == 3 ) {
             $this->db->join( 'ai_favorite as f', '(f.user_id='.$params['user_id'].' AND favorited_userid = u.user_id)', 'left', false );
         }

         if ( !empty( $params['searchlike'] ) ) {
             $this->db->like( 'u.first_name', $params['searchlike'] );
         }
         $this->db->limit( $params['limit'], $params['offset'] );
         $query = $this->db->get();

         $respArr           = [];
         $respArr['result'] = $query->result_array();
         $respArr['count']  = $this->db->query( 'SELECT FOUND_ROWS() count;' )->row()->count;
         return $respArr;

     }



     public function getCurrentUserInfo( $field, $where, $users_ids = NULL ) {
         $this->db->select( $field, FALSE );
         $this->db->from( 'ai_session as a' );
         $this->db->join( 'ai_user as u', 'u.user_id=a.user_id', 'left' );

         if ( !empty( $users_ids ) ) {
             $this->db->where( 'a.device_token IS NOT NULL' );
             $this->db->where_in( 'u.user_id', $users_ids );
             $this->db->order_by( 'a.session_id', 'DESC' );
         }
         else {
             $this->db->where( $where );
         }
         $query = $this->db->get();
         if ( $query->num_rows() ) {
             return (!empty( $users_ids )) ? $query->result_array() : $query->row_array();
         }
         else {
             return false;
         }

     }



     public function getUserInfo( $accessToken, $field = ['u.user_id'] ) {
         $respArr        = [];
         $accessTokenArr = explode( "||", $accessToken );
         if ( 2 != count( $accessTokenArr ) ) {
             $respArr = array ('code' => INVALID_ACCESS_TOKEN, 'msg' => 'Invalid Access Token', 'result' => []);
         }

         $whereArr = [];
         $whereArr = ['public_key' => $accessTokenArr[0], 'private_key' => $accessTokenArr[1], 'login_status' => 1];
         $userInfo = $this->getCurrentUserInfo( $field, $whereArr );
         if ( !empty( $userInfo ) ) {
             $respArr = array ('code' => SUCCESS_CODE, 'userinfo' => $userInfo);
         }
         else {
             $respArr = array ('code' => ACCESS_TOKEN_EXPIRED, 'msg' => 'Access Token Expired', 'result' => []);
         }
         return $respArr;

     }



     public function getAllUserPosts( $user_id, $hashtag, $post_id = NULL ) {

         $comma = ',';
         $this->db->select( "IF(post.create_date IS NULL,'',UNIX_TIMESTAMP(post.create_date)) as  create_date, IF(post.update_date IS NULL,'',UNIX_TIMESTAMP(post.update_date ))  as  update_date,share_map.shared,share_map.user_id as user_id,user.image ,CONCAT(user.first_name,' ',user.last_name) as username, post.post_id,post.post_description as description,share_map.like_count,share_map.comment_count,share_map.share_count,post.status,post.avg_rating,post.privacy,post.location,post.latitude,post.longitude, media.url,media.media_type,media.video_thumb as video_thumbnail , GROUP_CONCAT(DISTINCT utags.x_axis SEPARATOR ',') as x_axis,GROUP_CONCAT(DISTINCT utags.y_axis SEPARATOR ',') as y_axis,GROUP_CONCAT(DISTINCT utags.user_id SEPARATOR ',') as user_ids, GROUP_CONCAT( DISTINCT  custom.user_id,',')  as custom_ids,GROUP_CONCAT(DISTINCT ai_like_post.user_id SEPARATOR ',') as post_like_userids,GROUP_CONCAT(DISTINCT comment.user_id SEPARATOR ',') as comment_userids, GROUP_CONCAT( share_map.user_id SEPARATOR ',') as share_userids , share_map.id as shared_map_id ",
                            false );
         $this->db->from( 'ai_post as post' );
         $this->db->join( 'ai_media as media', 'post.post_id=media.post_id', 'left' );
         $this->db->join( 'ai_image_usertags as utags', 'utags.media_id=media.id', 'left' );
         $this->db->join( 'ai_post_custom as custom', 'custom.post_id=post.post_id', 'left' );
         $this->db->join( 'ai_share_post_mapping as share_map', 'post.post_id=share_map.post_id', 'left' );
         $this->db->join( 'ai_like_post', 'share_map.id=ai_like_post.share_id', 'left' );
         $this->db->join( 'ai_post_comment as comment', 'share_map.id=comment.share_id', 'left' );
         $this->db->join( 'ai_user as user', 'share_map.user_id=user.user_id', 'inner' );
         if ( !empty( $hashtag ) ) {
             $this->db->join( 'ai_post_hashtags as hashtag', 'post.post_id=hashtag.post_id', 'inner' );
         }
         /* To fetch data of a single post */
         if ( $post_id ) {
             $this->db->where( 'post.post_id', $post_id );
         }
         $this->db->where( '(post.privacy = 2 AND post.user_id = '.$user_id.' AND post.status =  '.ACTIVE.' )' );
         $this->db->or_where( '( post.privacy != 2 )' );

         if ( !empty( $hashtag ) ) {
             $this->db->where( 'hashtag.hash_tag', '#'.$hashtag );
         }
         $this->db->where( 'post.status', ACTIVE );
         $this->db->group_by( array ("media.id", "share_map.id") );
         $this->db->having( '( `post`.`privacy` = 3 AND (FIND_IN_SET ('.$user_id.',`custom_ids`)>0) OR  (`post`.`privacy` != 3 ) )' );
         $this->db->order_by( 'share_map.update_date', 'DESC' );
         $query = $this->db->get();

         return $query->result_array();

     }



     /* Update post counter */

     public function update_post_counter( $share_id, $column, $decrese_count = false ) {

         switch ( $column ) {
             case 'like':
                 $set = ( $decrese_count != UNLIKE) ? 'like_count+1' : 'like_count-1';
                 $this->db->set( 'like_count', $set, FALSE );
                 break;
             case 'comment':
                 /* Here UNLIKE means delete */
                 $set = ( $decrese_count != UNLIKE ) ? 'comment_count+1' : 'comment_count-1';
                 $this->db->set( 'comment_count', $set, FALSE );
                 break;
             case 'share':
                 /* Here UNLIKE means delete */
                 $set = ( $decrese_count != UNLIKE ) ? 'share_count+1' : 'share_count-1';
                 $this->db->set( 'share_count', $set, FALSE );
                 break;

             default:
                 break;
         }
         $table = 'ai_share_post_mapping';

         $where = ['id' => $share_id];
         /* In cas of share, do not update update_date  */
         if ( $column != 'share' ) {
             $this->db->set( 'update_date', datetime() );
         }
         $this->db->where( $where );

         return $result = $this->db->update( $table );

     }



     /**
      * get comments list
      * @param array
      * @return array
      */
     public function getComments( $params ) {

         $this->db->select( 'SQL_CALC_FOUND_ROWS id as comment_id,IF(c.user_id='.$params['user_id'].',1,0) as is_commented,CONCAT(first_name," ",last_name) as name,c.comment', false );
         $this->db->from( 'ai_post_comment as c' );
         $this->db->join( 'ai_user as u', 'u.user_id = c.user_id', 'left' );
         $this->db->where( 'share_id', $params['share_id'] );
         $this->db->order_by( 'c.create_date', 'DESC' );
         $this->db->limit( $params['limit'], $params['offset'] );
         $query            = $this->db->get();
         $resArr           = [];
         $resArr['result'] = $query->result_array();
         $resArr['count']  = $this->db->query( 'SELECT FOUND_ROWS() count;' )->row()->count;
         return $resArr;

     }



     /**
      * get all child ids of parent post
      * @param array
      * @return array
      */
     public function getPostSharedIds( $post_id, $table ) {
         $ids          = [];
         $query_result = $this->db->select( 'new_post_id' )->from( $table )->where( 'post_id', $post_id )->get();
         if ( $query_result->num_rows() > 0 ) {
             $result = $query_result->result_array();

             foreach ( $result as $key => $new_post_id ) {
                 $ids[] = $new_post_id['new_post_id'];
             }
         }
         return $ids;

     }



     /**
      * get hashtags list
      * @param array
      * @param string
      * @return array
      */
     public function getHashtags( $params, $search ) {

         $this->db->select( 'SQL_CALC_FOUND_ROWS DISTINCT(hash_tag)' );
         $this->db->from( 'ai_post_hashtags' );

         if ( !empty( $search ) ) {
             $this->db->where( 'hash_tag LIKE "%'.$search.'%"' );
         }
         $this->db->limit( $params['limit'], $params['offset'] );
         $query = $this->db->get();

         $resArr['data']  = $query->result_array();
         $resArr['count'] = $this->db->query( 'SELECT FOUND_ROWS() count;' )->row()->count;
         return $resArr;

     }



     /**
      * Get Post Owner Info
      * @param array
      * @param array
      * @return array
      */
     public function getPostownerInfo( $field, $where ) {
         $this->db->select( $field, FALSE );
         $this->db->from( 'ai_session as a' );
         $this->db->join( 'ai_user as u', 'u.user_id=a.user_id', 'left' );
         $this->db->join( 'ai_post as p', 'a.user_id=p.user_id', 'left' );
         $this->db->where( $where );
         $this->db->order_by( 'a.session_id', 'DESC' );

         $query = $this->db->get();

         return $query->row_array();

     }



     /**
      * Get Shared Post Owner Info
      * @param array
      * @param array
      * @return array
      */
     public function getSharedPostownerInfo( $field, $where ) {
         $this->db->select( $field, FALSE );
         $this->db->from( 'ai_session as a' );
         $this->db->join( 'ai_user as u', 'u.user_id=a.user_id', 'left' );
         $this->db->join( 'ai_share_post_mapping as p', 'a.user_id=p.user_id', 'left' );
         $this->db->where( $where );
         $this->db->order_by( 'a.session_id', 'DESC' );

         $query = $this->db->get();

         return $query->row_array();

     }



     /**
      * Fetch Tagged users In post media
      * @param array
      * @param string
      * @return array
      */
     public function getMediaTaggedUsers( $post_id ) {

         $this->db->select( 'user_id' );
         $this->db->from( 'ai_image_usertags' );
         $this->db->join( 'ai_media', 'ai_image_usertags.media_id = ai_media.id', 'inner' );
         $this->db->where( 'ai_media.post_id', $post_id );

         $query = $this->db->get();

         $resArr = $query->result_array();
         return $resArr;

     }



     /**
      * get events list
      * @param array
      * @param string
      * @return array
      */
     public function get_events( $params ) {

         $this->db->select( 'SQL_CALC_FOUND_ROWS ae.*', FALSE );
         $this->db->from( 'ai_events ae' );

         $this->db->where( 'ae.status', ACTIVE );
         //from date
         if ( !empty( $params['from_date'] ) && !empty( $params['to_date'] ) ) {
             $this->db->where( 'ae.start_date BETWEEN '.$params['from_date'].' AND '.$params['to_date'] );
         }
         else if ( !empty( $params['from_date'] ) && empty( $params['to_date'] ) ) {
             $this->db->where( 'ae.start_date > '.$params['from_date'] );
         }

         $this->db->limit( $params['limit'], $params['offset'] );
         $query = $this->db->get();

         $resArr['data']  = $query->result_array();
         $resArr['count'] = $this->db->query( 'SELECT FOUND_ROWS() count;' )->row()->count;
         return $resArr;

     }



     /**
      * get subscriptions list
      * @param array
      * @param string
      * @return array
      */
     public function get_subscriptions( $params ) {

         $this->db->select( 'SQL_CALC_FOUND_ROWS asub.subscription_id, asub.subscription_name,asub.status,asub.price,asub.subs_recurring,asub.description,UNIX_TIMESTAMP(asub.create_date) as create_date,UNIX_TIMESTAMP(asub.update_date) as update_date',
                            FALSE );
         $this->db->from( 'ai_subscriptions asub' );

         $this->db->where( 'asub.status', ACTIVE );

         $this->db->limit( $params['limit'], $params['offset'] );
         $query = $this->db->get();

         $resArr['data']  = $query->result_array();
         $resArr['count'] = $this->db->query( 'SELECT FOUND_ROWS() count;' )->row()->count;
         return $resArr;

     }



     /**
      * get subscription detail
      * @param array
      * @param string
      * @return array
      */
     public function get_subscription( $params ) {

         $this->db->select( 'SQL_CALC_FOUND_ROWS subscription_id, subscription_name,status,price,subs_recurring,description,create_date,update_date', FALSE );
         $this->db->from( 'ai_subscriptions' );
         $this->db->where( 'status', ACTIVE );

         if ( isset( $params['subscription_id'] ) ) {
             $this->db->where( 'subscription_id', $params['subscription_id'] );
         }

         $this->db->limit( $params['limit'], $params['offset'] );
         $query = $this->db->get();

         $resArr['data']  = $query->result_array();
         $resArr['count'] = $this->db->query( 'SELECT FOUND_ROWS() count;' )->row()->count;
         return $resArr;

     }



     /**
      * get subscriptions list
      * @param array
      * @param string
      * @return array
      */
     public function get_user_subscriptions( $params ) {

         $this->db->select( 'SQL_CALC_FOUND_ROWS subscription_id,subscription_name,user_id,status,UNIX_TIMESTAMP(start_date) as valid_from,UNIX_TIMESTAMP(end_date) as valid_till, IF(renew_date IS NULL , "" , UNIX_TIMESTAMP(renew_date) ) as renew_date,FORMAT(price, 2) as price,card_last_four',
                            FALSE );
         $this->db->from( 'ai_user_subscriptions' );
         $this->db->where( 'status', ACTIVE );
         $this->db->where( 'user_id', $params["user_id"] );
         $this->db->limit( $params['limit'], $params['offset'] );
         $query = $this->db->get();

         $resArr['data']  = $query->result_array();
         $resArr['count'] = $this->db->query( 'SELECT FOUND_ROWS() count;' )->row()->count;
         return $resArr;

     }



 }
