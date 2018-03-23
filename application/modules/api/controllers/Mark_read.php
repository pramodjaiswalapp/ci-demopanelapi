<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

 require APPPATH.'/libraries/Rcc_Controller.php';

 /**
  * @SWG\Post(path="/Mark_read",
  *   tags={"Chat"},
  *   summary="Mark as read",
  *   description="Mark message as read",
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
  *     name="users",
  *     in="formData",
  *     description="users eg<1,2,3>",
  *     required=true,
  *     type="string"
  *   ),
  *   @SWG\Response(response=501, description="Parameter missing"),
  *   @SWG\Response(response=200, description="Chat read successfully"),
  *   @SWG\Response(response=206, description="Unauthorized request"),
  *   @SWG\Response(response=207, description="Header is missing"),
  *
  * )
  */

 /**
  * Mark read
  *
  * @package RCC
  * @subpackage Api
  * @category chat
  */
 class Mark_read extends Rcc_Controller {

     function __construct() {
         parent::__construct();

         #load model
         $this->load->model( "Common_model", "cm" );
         $this->load->model( "Chat_model", "chat" );

     }



     public function index_post() {

         #try Start
         try {
             #response array
             $response_array        = [];
             $login_user['user_id'] = $GLOBALS['api_user_id'];

             #getting values from POST
             $post = $this->post();

             #setting form validation rule array
             $config = array (
                 array (
                     'field' => 'users',
                     'label' => 'user list',
                     'rules' => 'required'
                 ),
                 array (
                     'field' => 'from_user_id',
                     'label' => 'From user ID',
                     'rules' => 'required|numeric'
                 )
             );

             #setting Rule
             $this->form_validation->set_rules( $config );

             # Setting Error Messages for rules
             $this->form_validation->set_message( 'required', $this->lang->line( "msg_sfx" ).'%s' );

             #checking form validation
             if ( $this->form_validation->run() ) {

                 $login_user['from_user_id'] = $post['from_user_id'];

                 $is_update = $this->chat->mark_as_read( $login_user );

                 if ( isset( $is_update ) && !empty( $is_update ) && $is_update == TRUE ) {

                     #setting Response
                     $response_array = [
                         "CODE"    => SUCCESS_CODE,
                         "MESSAGE" => $this->lang->line( 'chat_read' )
                     ];
                 }
                 else {
                     throw new Exception( $this->lang->line( 'chat_not_read' ), TRY_AGAIN_CODE );
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
         }#try END
         catch ( Exception $ex ) {
             #setting error response
             $response_array = [
                 "CODE"    => $ex->getCode(),
                 "MESSAGE" => $ex->getMessage()
             ];

             #sending error response
             $this->response( $response_array );
         }#catch end

     }



 }
