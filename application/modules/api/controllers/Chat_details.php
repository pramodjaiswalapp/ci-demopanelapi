<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

 require APPPATH.'/libraries/Rcc_Controller.php';




 /**
  * @SWG\Get(path="/Chat_details",
  *   tags={"Chat"},
  *   summary="Chat_details",
  *   description="get chat conversation",
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
  *     name="count",
  *     in="query",
  *     description="initially count=0 after that count=next_count",
  *     required=true,
  *     type="string"
  *   ),
  *   @SWG\Parameter(
  *     name="user_id",
  *     in="query",
  *     description="user id ",
  *     required=true,
  *     type="string"
  *   ),
  *   @SWG\Parameter(
  *     name="timestamp",
  *     in="query",
  *     description="last message time for recent messages",
  *
  *     type="string"
  *   ),
  *   @SWG\Response(response=501, description="Parameter missing"),
  *   @SWG\Response(response=200, description="Success"),
  *   @SWG\Response(response=206, description="Unauthorized request"),
  *   @SWG\Response(response=207, description="Header is missing"),
  *   @SWG\Response(response=307, description="No record found"),
  * )
  */

 /**
  * Chat details
  *
  * @package RCC
  * @subpackage Api
  * @category chat
  */
 class Chat_details extends Rcc_Controller {

     /**
      * constructor
      */
     function __construct() {
         parent::__construct();
         $this->load->model( "Chat_model", "chat" );

     }



     /**
      * @function index_get
      * @description function to get chat details
      */
     public function index_get() {
         try {
             $response_array = [];
             $getDataArr     = $this->get();

             $default_array = [
                 "user_id"      => "",
                 "message_id"   => "",
                 "from_user_id" => ""
             ];
             $params        = defaultValue( $getDataArr, $default_array );

             #configureform data
             $set_data = array (
                 'user_id'      => $params['user_id'],
                 'from_user_id' => $params['from_user_id']
             );

             #setting Form data
             $this->form_validation->set_data( $set_data );

             #setting form validation
             $config = array (
                 array (
                     'field' => 'user_id',
                     'label' => 'User ID',
                     'rules' => 'required|numeric'
                 ), array (
                     'field' => 'from_user_id',
                     'label' => 'From User ID',
                     'rules' => 'required|numeric'
                 )
             );

             #setting Rule
             $this->form_validation->set_rules( $config );

             # Setting Error Messages for rules
             $this->form_validation->set_message( 'required', $this->lang->line( "msg_sfx" ).'%s' );

             if ( $this->form_validation->run() ) {


                 $messageId = isset( $getDataArr['message_id'] ) && !empty( $getDataArr['message_id'] ) ? $getDataArr['message_id'] : '';
                 /**
                  * get chat list
                  */
                 $this->get_chat_details( $getDataArr, $messageId );
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

             #sending response
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
      * @function get_chat_details
      * @description get chat list of logged in user
      *
      * @param type $userid
      * @param type $messageId
      */
     private function get_chat_details( $userid, $messageId ) {
         try {

             #limit =20
             $where['limit'] = 20;

             #next count
             $next_count = $where['limit'];

             #read message
             $this->chat->mark_as_read( $userid );

             #user chat details
             $all_data = $this->chat->get_user_chatdetail( $GLOBALS['api_user_id'], $userid['user_id'], $where, $messageId );

             #result set
             $list = $all_data['list'];

             #total record
             $total_count = $all_data['totalcount'];

             #check is data remaining or not
             if ( $total_count <= $next_count ) {
                 $next_count_record = -1;
             }
             else {
                 $next_count_record = 1;
             }

             if ( isset( $list ) && !empty( $list ) ) {

                 #setting response
                 $response_array = [
                     'CODE'    => SUCCESS_CODE,
                     'MESSAGE' => 'success',
                     'RESULT'  => $list,
                     'NEXT'    => $next_count_record,
                     'TOTAL'   => $total_count
                 ];
             }#if end
             else {

                 #setting response
                 $response_array = [
                     "CODE"    => RECORD_NOT_EXISTS,
                     'MESSAGE' => $this->lang->line( 'NO_RECORD_FOUND' ),
                     'RESULT'  => array ()
                 ];
             }#else end
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



     /**
      * @function __desctructor
      * @description function will call when class work is done
      */
     function __destruct() {
         parent::__destruct();

         unset( $this->chat );

     }



 }
