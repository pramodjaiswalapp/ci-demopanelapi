<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

 require APPPATH.'/libraries/Rcc_Controller.php';


 /**
  * @SWG\Post(path="/Delete_message",
  *   tags={"Chat"},
  *   summary="Delete_message",
  *   description="delete chat message",
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
  *     name="message_id",
  *     in="formData",
  *     description="message id to delete",
  *     required=true,
  *     type="string"
  *   ),
  *   @SWG\Parameter(
  *     name="sender_id",
  *     in="formData",
  *     description="sender id of that message ",
  *     required=true,
  *     type="string"
  *   ),
  *   @SWG\Response(response=501, description="Parameter missing"),
  *   @SWG\Response(response=200, description="Message deleted successfully"),
  *   @SWG\Response(response=206, description="Unauthorized request"),
  *   @SWG\Response(response=207, description="Header is missing"),
  *
  * )
  */

 /**
  * Delete message
  *
  * @package RCC
  * @subpackage Api
  * @category chat
  */
 class Delete_message extends Rcc_Controller {

     /**
      * constructor
      */
     function __construct() {
         parent::__construct();

         /**
          * load model
          */
         $this->load->model( "Common_model", "cm" );

     }



     public function index_post() {
         try {

             $response_array = [];
             $post           = $this->post();

             #===================
             #setting form validation
             $config = array (
                 array (
                     'field' => 'message_id',
                     'label' => 'Message ID',
                     'rules' => 'required|numeric'
                 ), array (
                     'field' => 'sender_id',
                     'label' => 'Sender ID',
                     'rules' => 'required|numeric'
                 )
             );

             #setting Rule
             $this->form_validation->set_rules( $config );

             # Setting Error Messages for rules
             $this->form_validation->set_message( 'required', $this->lang->line( "msg_sfx" ).'%s' );


             #if form is valid
             if ( $this->form_validation->run() ) {

                 #check for post array
                 if ( $GLOBALS['api_user_id'] == $post['sender_id'] ) {
                     $update_arr = array (
                         'del_by_sender' => 1
                     );
                 }#if end
                 else {
                     $update_arr = array (
                         'del_by_receiver' => 1
                     );
                 }#else end
                 #
                 #
                 #update message to deleted
                 $is_update = $this->cm->update_single( "rc_chat", $update_arr, array ("where" => array ("message_id" => $post['message_id'])) );

                 #if deleted
                 if ( $is_update ) {
                     $response_array = [
                         'CODE'    => SUCCESS_CODE,
                         'MESSAGE' => $this->lang->line( 'message_deleted' )
                     ];
                 }#if end
                 else {
                     throw new Exception( $this->lang->line( 'message_not_deleted' ), TRY_AGAIN_CODE );
                 }#else end
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


             #sending rsponse
             $this->response( $response_array );
             #===================
         }#try end
         catch ( Exception $ex ) {

             $response_array = [
                 "CODE"    => $ex->getCode(),
                 "MESSAGE" => $ex->getMessage()
             ];
             $this->response( $response_array );
         }#catch end

     }



 }
