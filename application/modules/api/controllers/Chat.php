<?php
 require APPPATH.'/libraries/Rcc_Controller.php';

 /**
  * Chat
  * @package RCC
  * @subpackage Api
  * @category chat
  * Rcc_Controller for common function check authentication
  * @Date 08/12/2018
  */
 class Chat extends Rcc_Controller {

     public function __construct() {
         parent::__construct();
         $this->load->model( 'Chat_model' );

     }



     /**
      * @SWG\Get(path="/Chat",
      *   tags={"Chat"},
      *   summary="Chat_list",
      *   description="get chat Threads",
      *   operationId="index_get",
      *   produces={"application/json"},
      *   @SWG\Parameter(
      *     name="Authorization",
      *     in="header",
      *     description="",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="Accesstoken",
      *     in="header",
      *     description="",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="action",
      *     in="query",
      *     description="Get 3 sec msg action 1 Get list of all people whom we have chatted out 2
      *     View Detail  between two users 3 Delete spacific chat 4 Delete chat between two users 5",
      *     required=true,
      *     type="string"
      *   ),
      * @SWG\Parameter(
      *     name="from_user_id",
      *     in="query",
      *     description="Mendatry for action 3 and 5 when action 5 then in json formate multy user id {"0":"102","1":"101"} ",
      *     required=false,
      *     type="string"
      *   ),
      * @SWG\Parameter(
      *     name="page",
      *     in="query",
      *     description="for pagination page ",
      *     required=false,
      *     type="string"
      *   ),
      * @SWG\Parameter(
      *     name="msg_id",
      *     in="query",
      *     description="message id for delete",
      *     required=false,
      *     type="string"
      *   ),
      * @SWG\Parameter(
      *     name="last_msg_time",
      *     in="query",
      *     description="Latest chat after last message time in unixtimestamp",
      *     required=false,
      *     type="string"
      *   ),
      *   @SWG\Response(response=418, description="Parameter missing"),
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=307, description="No record found"),
      * )
      */

     /**
      * All get action perform depends on action
      */
     public function index_get() {
         try {
             $getDataArr = $this->get();

             #response array
             $response_array = [];

             #setting form data
             $set_data = array (
                 'action' => isset( $getDataArr['action'] ) ? $getDataArr['action'] : ''
             );

             $this->form_validation->set_data( $set_data );

             #setting form validation
             $config = [];
             $config = array (
                 array (
                     'field' => 'action',
                     'label' => 'Action ID',
                     'rules' => 'required'
                 )
             );
             $this->form_validation->set_rules( $config );

             # Setting Error Messages for rules
             $this->form_validation->set_message( 'required', 'Please enter the %s' );



             if ( $this->form_validation->run() ) {

                 $getDataArr['user_id'] = $GLOBALS['api_user_id'];
                 $action                = isset( $getDataArr['action'] ) ? $getDataArr['action'] : "";

                 if ( $action == 1 ) {
                     #Get 3 sec msg action 1
                     #@param last_msg_time , page
                     $this->getmessagebyuser( $getDataArr );
                 }
                 else if ( $action == 2 ) {
                     /*
                      * Get list of all people whom we have chatted out
                      * @param page
                      */
                     $this->getchathistory( $getDataArr );
                 }
                 else if ( $action == 3 ) {
                     /*
                      * View Detail  between two users
                      * @param from_user_id , last_msg_time , page
                      */
                     $this->getconversationdetail( $getDataArr );
                 }
                 else if ( $action == 4 ) {
                     /**
                      * Delete spacific chat
                      * @param array $name msg_id
                      */
                     $this->deletespecificmsg( $getDataArr );
                 }
                 else if ( $action == 5 ) {
                     /*
                      * Delete chat between two users
                      * @param from_user_id
                      */
                     $this->deleteallmsg( $getDataArr );
                 }
                 else {
                     /**
                      * Default get user list
                      */
                     $this->getuserlist( $getDataArr );
                 }
             }
             else {#if form validation goes wrong
                 $err = $this->form_validation->error_array();
                 $arr = array_values( $err );

                 #setting Response
                 $response_array = [
                     'code' => PARAM_REQ,
                     'msg'  => $arr
                 ];
                 #sending response
                 $this->response( $response_array );
             }
         }#try end
         catch ( Exception $ex ) {#catch start
             $response_array = [
                 "CODE"    => $ex->getCode(),
                 "MESSAGE" => $ex->getMessage()
             ];

             $this->response( $response_array );
         }#catch End

     }



     /**
      * @SWG\Post(path="/chat",
      *   tags={"Chat"},
      *   summary="Send message",
      *   description="Send chat messages",
      *   operationId="index_post",
      *   consumes ={"multipart/form-data"},
      *   produces={"application/json"},
      *   @SWG\Parameter(
      *     name="Authorization",
      *     in="header",
      *     description="",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="Accesstoken",
      *     in="header",
      *     description="",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="to_user_id",
      *     in="formData",
      *     description="other user",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="chat_type",
      *     in="formData",
      *     description="type of message 1: text message, 2: image ",
      *     required=true,
      *     type="string"
      *   ),
      *    @SWG\Parameter(
      *     name="msg",
      *     in="formData",
      *     description="text message",
      *      required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="media_url",
      *     in="formData",
      *     description="image url if any",
      *     type="string"
      *   ),
      *   @SWG\Response(response=418, description="Parameter missing"),
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=201, description="Try again"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      * )
      */

     /**
      * Type Send message
      */
     public function index_post() {
         try {

             #get post data
             $postDataArr    = $this->post();
             #=================================
             #response array
             $response_array = [];

             #setting form validation rule
             $config = array (
                 array (
                     'field' => 'to_user_id',
                     'label' => 'User ID',
                     'rules' => 'required'
                 ), array (
                     'field' => 'chat_type',
                     'label' => 'Chat Type',
                     'rules' => 'required'
                 )
             );
             #setting Rules
             $this->form_validation->set_rules( $config );
             #Setting Error Messages for rules
             $this->form_validation->set_message( 'required', 'Please enter the %s' );


             if ( $this->form_validation->run() ) {


                 $any_one = ["msg", "media_url"];
                 if ( isset( $mandatory ) && !empty( $mandatory ) &&
                     !$this->check_mandatory( $postDataArr, [], $any_one ) ) {
                     $this->response( ['CODE'    => PARAM_REQ,
                         'MESSAGE' => $this->lang->line( 'missing_parameter' )] );
                 }

                 $this->sendmessage( $postDataArr );
             }#form validation if end
             else {#if form validation goes wrong
                 $err = $this->form_validation->error_array();
                 $arr = array_values( $err );

                 #setting Response
                 $response_array = [
                     'code' => PARAM_REQ,
                     'msg'  => $arr
                 ];
             }

             $this->response( $response_array );
         }
         catch ( Exception $ex ) {
             $this->response( ["CODE" => $ex->getCode(), "MESSAGE" => $ex->getMessage()] );
         }

     }



     /**
      * @function sendmessage
      * @description function to send message
      *
      * @param type $postDataArr
      */
     private function sendmessage( $postDataArr ) {
         try {
             $response_array = [];
             $default_array  = [
                 "to_user_id"        => $postDataArr['to_user_id'],
                 "from_user_id"      => $GLOBALS['api_user_id'],
                 "chat_type"         => 1,
                 "msg"               => "",
                 "media_url"         => "",
                 "chat_created_time" => date( 'Y-m-d H:i:s' ),
                 "chat_time"         => (strtotime( (date( 'Y-m-d H:i:s' ) ) ) * 1000)
             ];
             $chatdata       = defaultValue( $postDataArr, $default_array );

             $this->db->trans_begin();
             $msg_id = $this->Common_model->insert_single( 'ai_chat', $chatdata );
             $this->Chat_model->insert_history( $chatdata );
             if ( $this->db->trans_status() == TRUE ) {
                 // commit transaction
                 $this->db->trans_commit();
             }
             else {
                 $this->db->trans_rollback();
             }
             if ( !empty( $msg_id ) ) {


                 $chatdata['msg_id']       = ( string ) $msg_id;
                 $chatdata['message_time'] = ( string ) (strtotime( ($chatdata['chat_created_time'] ) ) * 1000);
                 unset( $chatdata['chat_created_time'] );
                 unset( $chatdata['chat_time'] );
                 /*
                  * Send Request Push
                  */
                 $pushData                 = [];
                 $pushData['receiver_id']  = $chatdata['to_user_id'];
                 $pushData['msg']          = "You have received a new message by ".$postDataArr['name'];
                 $pushData['type']         = CHAT_PUSH;

                 $this->sendPush( $pushData );


                 $response_array = [
                     'CODE'    => SUCCESS_CODE,
                     'MESSAGE' => $this->lang->line( 'message_sent' ),
                     'RESULT'  => $chatdata
                 ];
             }
             else {

                 $response_array = [
                     'CODE'    => TRY_AGAIN_CODE,
                     'MESSAGE' => $this->lang->line( 'try_again' ),
                     'RESULT'  => array ()
                 ];
             }
             $this->response( $response_array );
         }
         catch ( Exception $ex ) {
             $response_array = [
                 "CODE"    => $ex->getCode(),
                 "MESSAGE" => $ex->getMessage()
             ];
             $this->response( $response_array );
         }

     }



     /**
      *
      * Use only for send notification
      * @param type $pushData
      */
     private function sendPush( $pushData ) {
         $this->load->library( 'commonfn' );
         $whereArr             = [];
         $whereArr['where']    = ['user_id' => $pushData['receiver_id']];
         $whereArr['group_by'] = 'device_token';
         $whereArr['order_by'] = ['login_time' => 'desc'];
         $userInfo             = $this->Common_model->fetch_data( 'ai_session', ['user_id', 'platform', 'device_token', 'login_status'], $whereArr );

         foreach ( $userInfo as $user ) {

             if ( !empty( $user['device_token'] ) && strlen( $user['device_token'] ) > 60 && $user['login_status'] == 1 ) {
                 if ( $user['platform'] == 1 ) {
                     /*
                      * Create Android Payload
                      */
                     $payload            = [];
                     $payload['message'] = $pushData['msg'];
                     $payload['user_id'] = $this->login_user['user_id'];
                     $payload['type']    = $pushData['type'];
                     $payload['time']    = time();
                     /*
                      * Send Push
                      */
                     $this->commonfn->androidPush( $user['device_token'], $payload );
                 }
                 else if ( $user['platform'] == 2 ) {
                     /*
                      * Create iOS Payload
                      */
                     $payload          = [];
                     $payload['alert'] = array ('title' => $pushData['msg'], 'user_id' => $this->login_user['user_id']);
                     $payload['badge'] = 0;
                     $payload['type']  = $pushData['type'];
                     $payload['sound'] = 'beep.mp3';
                     /*
                      * Send Push
                      */
                     $this->commonfn->iosPush( $user['device_token'], $payload );
                 }
             }
         }

     }



     /**
      * @function deletespecificmsg
      * #descriptio to delete particular Message from chat
      *
      * @param type $getDataArr
      */
     public function deletespecificmsg( $getDataArr ) {
         try {

             #new code
             $response_array = [];

             #rest Previously setted Validation Rules
             $this->form_validation->reset_validation();
             #code Start
             #configureform data
             $set_data = array (
                 'msg_id' => isset( $getDataArr['msg_id'] ) ? $getDataArr['msg_id'] : ''
             );

             #setting Form data
             $this->form_validation->set_data( $set_data );

             #setting form validation
             $config = array (
                 array (
                     'field' => 'msg_id',
                     'label' => 'Message ID',
                     'rules' => 'required|numeric'
                 )
             );

             #setting Rule
             $this->form_validation->set_rules( $config );

             # Setting Error Messages for rules
             $this->form_validation->set_message( 'required', 'Please enter the %s' );

             if ( $this->form_validation->run() ) {

                 $issuccess = $this->Chat_model->delete_specific( $getDataArr );
                 if ( !empty( $issuccess ) ) {
                     $response_array = [
                         'CODE'    => SUCCESS_CODE,
                         'MESSAGE' => $this->lang->line( 'success' ),
                         'RESULT'  => []
                     ];
                 }
                 else {
                     $response_array = [
                         'CODE'    => TRY_AGAIN,
                         'MESSAGE' => $this->lang->line( 'try_again' ),
                         'RESULT'  => []
                     ];
                 }
             }#form validation if end
             else {#if form validation goes wrong
                 $err = $this->form_validation->error_array();
                 $arr = array_values( $err );

                 #setting Response
                 $response_array = [
                     'code' => PARAM_REQ,
                     'msg'  => $arr
                 ];
             }

             $this->response( $response_array );
             #new code Ends
         }
         catch ( Exception $ex ) {

             /**
              * response exception
              */
             $response_array = [
                 "CODE"    => $ex->getCode(),
                 "MESSAGE" => $ex->getMessage()
             ];
             $this->response( $response_array );
         }

     }



     /**
      * @function deleteallmsg
      * @description to delete all message
      *
      * @param type $getDataArr
      */
     public function deleteallmsg( $getDataArr ) {

         try {

             $response_array = [];

             #rest Previously setted Validation Rules
             $this->form_validation->reset_validation();
             #code Start
             #configure form data
             $set_data = array (
                 'from_user_id' => isset( $getDataArr['from_user_id'] ) ? $getDataArr['from_user_id'] : ''
             );

             #setting Form data
             $this->form_validation->set_data( $set_data );

             #setting form validation
             $config = array (
                 array (
                     'field' => 'from_user_id',
                     'label' => 'From User ID',
                     'rules' => 'required|numeric'
                 )
             );

             #setting Rule
             $this->form_validation->set_rules( $config );

             # Setting Error Messages for rules
             $this->form_validation->set_message( 'required', 'Please enter the %s' );



             if ( $this->form_validation->run() ) {

                 $params['user_id']      = $getDataArr['user_id'];
                 $params['from_user_id'] = explode( ',', $getDataArr['from_user_id'] );

                 /**
                  * Check About Message In Chat Table
                  */
                 $issuccess = $this->Chat_model->delete_all( $params );
                 if ( !empty( $issuccess ) ) {

                     $response_array = [
                         'CODE'    => SUCCESS_CODE,
                         'MESSAGE' => $this->lang->line( 'success' ),
                         'RESULT'  => []
                     ];
                 }
                 else {
                     $response_array = [
                         'CODE'    => TRY_AGAIN,
                         'MESSAGE' => $this->lang->line( 'try_again' ),
                         'RESULT'  => []
                     ];
                 }
             }#form validation if end
             else {#if form validation goes wrong
                 $err = $this->form_validation->error_array();
                 $arr = array_values( $err );

                 #setting Response
                 $response_array = [
                     'code' => PARAM_REQ,
                     'msg'  => $arr
                 ];
             }

             #sending Response
             $this->response( $response_array );
         }
         catch ( Exception $ex ) {

             #setting Response
             $response_array = [
                 "CODE"    => $ex->getCode(),
                 "MESSAGE" => $ex->getMessage()
             ];

             #sending Response
             $this->response( $response_array );
         }

     }



     public function getchathistory( $getDataArr ) {
         try {

             $response_array = [];

             #Default values array
             $default_array     = [
                 "user_id" => $getDataArr['user_id'],
                 "page"    => 1,
                 "limit"   => 10
             ];
             $params            = defaultValue( $getDataArr, $default_array );
             $params['user_id'] = $getDataArr['user_id'];
             $page              = $params['page'];
             $offset            = ($params['page'] - 1) * $params['limit'];

             #fetching user chat history
             $chathistory = $this->Chat_model->fetch_history( $params['limit'], $offset, $params );

             #total chat message count
             $total_count = $this->Chat_model->totalmsg;

             #check is data remaining or not
             if ( ($total_count > ($page * $params['limit']) ) ) {
                 $page++;
             }
             else {
                 $page = 0;
             }

             #if user history available
             if ( !empty( $chathistory ) ) {

                 #setting response
                 $response_array = [
                     'CODE'    => SUCCESS_CODE,
                     'MESSAGE' => $this->lang->line( 'success' ),
                     'RESULT'  => $chathistory,
                     'NEXT'    => $page,
                     'TOTAL'   => $total_count
                 ];
             }#IF END
             else {

                 #setting response
                 $response_array = [
                     'CODE'    => NO_DATA_FOUND,
                     'MESSAGE' => $this->lang->line( 'no_data_found' ),
                     'RESULT'  => []
                 ];
             }#else END
             #sending response
             $this->response( $response_array );
         }
         catch ( Exception $ex ) {

             #setting response
             $response_array = [
                 "CODE"    => $ex->getCode(),
                 "MESSAGE" => $ex->getMessage()
             ];

             #sending response
             $this->response( $response_array );
         }#catch End

     }



     public function getconversationdetail( $getDataArr ) {
         try {

             #new code
             $response_array = [];

             #rest Previously setted Validation Rules
             $this->form_validation->reset_validation();
             #code Start
             #configureform data
             $set_data = array (
                 'from_user_id' => isset( $getDataArr['from_user_id'] ) ? $getDataArr['from_user_id'] : ''
             );

             #setting Form data
             $this->form_validation->set_data( $set_data );

             #setting form validation
             $config = array (
                 array (
                     'field' => 'from_user_id',
                     'label' => 'From User ID',
                     'rules' => 'required|numeric'
                 )
             );

             #setting Rule
             $this->form_validation->set_rules( $config );

             # Setting Error Messages for rules
             $this->form_validation->set_message( 'required', 'Please enter the %s' );

             if ( $this->form_validation->run() ) {#if form valid
                 $default_array = [
                     "page"          => 1,
                     "user_id"       => $getDataArr['user_id'],
                     "from_user_id"  => $getDataArr['from_user_id'],
                     "last_msg_time" => "",
                     "limit"         => LIMIT
                 ];
                 $params        = defaultValue( $getDataArr, $default_array );
                 $page          = $params['page'];
                 $offset        = ($page - 1) * $params['limit'];


                 #gettin chat history
                 $chathistory = $this->Chat_model->fetch_chat_detail( $params['limit'], $offset, $params );

                 #total message in history
                 $total_count = $this->Chat_model->totalmsg;

                 #check is data remaining or not
                 if ( ($total_count > ($page * $params['limit']) ) ) {
                     $page++;
                 }
                 else {
                     $page = 0;
                 }


                 if ( !empty( $chathistory ) ) {
                     $where = array ();
                     $where = array ('user_id' => $params['user_id'], 'from_user_id' => $params['from_user_id']);

                     #DB transaction start
                     $this->db->trans_begin();
                     $this->Chat_model->mark_as_read( $where );

                     #Check if all DB queries executed successfully
                     if ( $this->db->trans_status() ) {

                         #commiting transaction
                         $this->db->trans_commit();

                         #setting Response
                         $response_array = [
                             'CODE'    => SUCCESS_CODE,
                             'MESSAGE' => $this->lang->line( 'success' ),
                             'RESULT'  => $chathistory,
                             'NEXT'    => $page,
                             'TOTAL'   => $total_count
                         ];
                     }#if end
                     else {#IF transaction failed
                         #rolling back
                         $this->db->trans_rollback();

                         #setting Response Array
                         $response_array = [
                             'CODE'    => TRY_AGAIN_CODE,
                             'MESSAGE' => $this->lang->line( 'try_again' ),
                             'RESULT'  => []
                         ];
                     }
                 }
                 else {
                     $response_array = [
                         'CODE'    => NO_DATA_FOUND,
                         'MESSAGE' => $this->lang->line( 'no_data_found' ),
                         'RESULT'  => array ()
                     ];
                 }
             }
             else {
                 $err = $this->form_validation->error_array();
                 $arr = array_values( $err );

                 #setting Response
                 $response_array = [
                     'code' => PARAM_REQ,
                     'msg'  => $arr
                 ];
             }
             $this->response( $response_array );
             #new Code End
         }
         catch ( Exception $ex ) {
             $response_array = [
                 "CODE"    => $ex->getCode(),
                 "MESSAGE" => $ex->getMessage()
             ];
             $this->response( $response_array );
         }

     }



     /**
      * @function getmessagebyuser
      * @description getting messages for logged in user
      *
      * @param type $getDataArr
      */
     public function getmessagebyuser( $getDataArr ) {
         try {

             $response_array = [];

             #rest Previously setted Validation Rules
             $this->form_validation->reset_validation();
             #code Start
             #configureform data
             $set_data = array (
                 'from_user_id' => isset( $getDataArr['from_user_id'] ) ? $getDataArr['from_user_id'] : ''
             );

             #setting Form data
             $this->form_validation->set_data( $set_data );

             #setting form validation
             $config = array (
                 array (
                     'field' => 'from_user_id',
                     'label' => 'From User ID',
                     'rules' => 'required|numeric'
                 )
             );

             #setting Rule
             $this->form_validation->set_rules( $config );

             # Setting Error Messages for rules
             $this->form_validation->set_message( 'required', 'Please enter the %s' );

             #form validation check
             if ( $this->form_validation->run() ) {
                 #form is valid

                 $defaultAraay = [
                     "to_user_id"    => $getDataArr['user_id'],
                     "from_user_id"  => $getDataArr['from_user_id'],
                     "last_msg_time" => ""
                 ];
                 $params       = defaultValue( $getDataArr, $defaultAraay );
                 $chatdata     = $this->Chat_model->fetch_chatdata( $params );

                 if ( !empty( $chatdata ) ) {
                     $where = array ();
                     $where = array ('user_id' => $getDataArr['user_id'], 'from_user_id' => $getDataArr['from_user_id']);
                     $this->Chat_model->mark_as_read( $where );

                     $response_array = [
                         'CODE'        => SUCCESS_CODE,
                         'MESSAGE'     => $this->lang->line( 'success' ),
                         'READ_STATUS' => $chatdata['readstatus'],
                         'RESULT'      => $chatdata['msg']
                     ];
                 }
                 else {
                     $response_array = [
                         'CODE'    => NO_DATA_FOUND,
                         'MESSAGE' => $this->lang->line( 'no_data_found' ),
                         'RESULT'  => []
                     ];
                 }
             }#Form validation if END
             else {#if form validation goes wrong
                 $err = $this->form_validation->error_array();
                 $arr = array_values( $err );

                 #setting Response
                 $response_array = [
                     'code' => PARAM_REQ,
                     'msg'  => $arr
                 ];
             }
             $this->response( $response_array );
             #NEW code Ends
         }
         catch ( Exception $ex ) {
             /**
              * log exception
              */
             log_message( "error", $ex->getMessage() );
             /**
              * response exception
              */
             $this->response( ["CODE"    => $ex->getCode(),
                 "MESSAGE" => $ex->getMessage()] );
         }

     }



     /**
      * @function getuserlist
      * @description send all users list
      *
      * @param type $getDataArr
      */
     public function getuserlist( $getDataArr ) {
         try {
             $response_array = [];

             $default_array = [
                 "user_id"    => $getDataArr['user_id'],
                 "page"       => 1,
                 "searchlike" => "",
                 "limit"      => 10
             ];
             $params        = defaultValue( $getDataArr, $default_array );

             $page   = $params['page'];
             $offset = ($page - 1) * $params['limit'];

             $chatdata    = $this->Chat_model->getuserlist( $params['limit'], $offset, $params );
             $total_count = $this->Chat_model->totalmsg;
             /**
              * check is data remaining or not
              */
             if ( ($total_count > ($page * $params['limit']) ) ) {
                 $page++;
             }
             else {
                 $page = 0;
             }
             if ( !empty( $chatdata ) ) {

                 #setting response
                 $response_array = [
                     'CODE'    => SUCCESS,
                     'MESSAGE' => $this->lang->line( 'success' ),
                     'RESULT'  => $chatdata,
                     'NEXT'    => $page,
                     'TOTAL'   => $total_count
                 ];
             }
             else {

                 #setting response
                 $response_array = [
                     'CODE'    => NO_DATA_FOUND,
                     'MESSAGE' => $this->lang->line( 'no_data_found' ),
                     'RESULT'  => array ()
                 ];
             }

             #sending response
             $this->response( $response_array );
         }
         catch ( Exception $ex ) {

             #setting response
             $response_array = [
                 "CODE"    => $ex->getCode(),
                 "MESSAGE" => $ex->getMessage()
             ];

             #sending response
             $this->response( $response_array );
         }

     }



 }
