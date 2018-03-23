<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

 require APPPATH.'/libraries/REST_Controller.php';

 /**
  * Recent message
  *
  * @package RCC
  * @subpackage Api
  * @category chat
  */
 class Recent_message extends REST_Controller {

     /**
      * constructor
      */
     function __construct() {
         parent::__construct();

         /**
          * load model
          */
         $this->load->model( "Common_model", "cm" );
         $this->load->model( "Chat_model", "chat" );

         /**
          * init time
          */
         $this->timestamp = time();

     }



     /**
      * index
      * @method get
      */
     public function index_get() {
         try {

             $get    = $this->get();
             $config = [
                 [
                     'field' => 'user_id',
                     'label' => 'User Id',
                     'rules' => 'required'
                 ],
                 [
                     'field' => 'message_id',
                     'label' => 'Message ID',
                     'rules' => 'required'
                 ]
             ];

             $default      = [
                 "user_id"    => "",
                 "message_id" => "",
             ];
             #Setting Default Value
             $defaultValue = defaultValue( $get, $default );

             $user_id    = $defaultValue['user_id'];
             $message_id = $defaultValue['message_id'];

             $set_data = [
                 'user_id'    => $user_id,
                 'message_id' => $message_id
             ];

             /* Set Data , Rules and Error messages for API request parameter validation */
             $this->form_validation->set_data( $set_data );
             $this->form_validation->set_rules( $config );
             $this->form_validation->set_message( 'required', 'Please enter the %s' );

             #if validation runs and returns error(s)
             if ( !$this->form_validation->run() ) {
                 $err            = $this->form_validation->error_array();
                 $arr            = array_values( $err );
                 $error          = ( isset( $arr[0] ) ) ? $arr[0] : 'parameter missing';
                 $response_array = array ('code' => PARAM_REQ, 'msg' => $error);
             }
             #if validation runs successfully without returning any errors
             else if ( $this->form_validation->run() ) {
                 #get chat list
                 $this->get_chat_details( $get['user_id'], $get['message_id'] );
             }
             #send response
             $this->response( $response_array );
         }#TRY END
         catch ( Exception $ex ) {
             #log exception
             log_message( "error", $ex->getMessage() );

             #response exception
             $this->response( ["code" => $ex->getCode(), "message" => $ex->getMessage()] );
         }#CATCH END

     }



     /**
      * get chat list of logged in user
      * @param int $userid
      */
     private function get_chat_details( $userid, $message_id ) {
         try {

             #limit =20
             $where['limit'] = 20;

             #Type identify its a hit 3 second and get latest chat
             $where['type'] = '1';

             $all_data = $this->chat->get_user_chatdetail( $this->login_user['user_id'], $userid, $where, $message_id );

             #result set
             $list = $all_data['list'];

             #total record
             $resonse_array = array ("CODE" => RECORD_NOT_EXISTS, 'MESSAGE' => $this->lang->line( 'NO_RECORD_FOUND' ), 'RESULT' => array ());
             if ( isset( $list ) && !empty( $list ) ) {

                 $resonse_array = array ('CODE' => SUCCESS_CODE, 'MESSAGE' => 'success', 'RESULT' => $list);
             }
             #send response
             $this->response( $resonse_array );
         }#TRY END
         catch ( Exception $ex ) {

             #log exception
             log_message( 'error', $ex->getMessage() );
             #response exception
             $this->response( ["code" => $ex->getCode(), "message" => $ex->getMessage()] );
         }#CATCH END

     }



     /**
      * destructor
      */
     function __destruct() {
         parent::__destruct();

         unset( $this->chat );
         unset( $this->cm );
         unset( $this->login_user );

     }



 }
