<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

 require APPPATH.'/libraries/Rcc_Controller.php';

 /**
  * @SWG\Post(path="/Delete_list",
  *   tags={"Chat"},
  *   summary="Delete_list",
  *   description="delete chat thread",
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
  *     description="users id as string eg <1,2,3,4>",
  *     required=true,
  *     type="string"
  *   ),
  *   @SWG\Response(response=501, description="Parameter missing"),
  *   @SWG\Response(response=200, description="Success"),
  *   @SWG\Response(response=206, description="Unauthorized request"),
  *   @SWG\Response(response=207, description="Header is missing"),
  *
  * )
  */

 /**
  * Delete list
  *
  * @package RCC
  * @subpackage Api
  * @category chat
  */
 class Delete_list extends Rcc_Controller {

     /**
      * constructor
      */
     function __construct() {
         parent::__construct();

         /**
          * load model
          */
         $this->load->model( "Chat_model", "chat" );

     }



     public function index_post() {
         try {
             $response_array = [];
             $post           = $this->post();


             #setting form validation
             $config = array (
                 array (
                     'field' => 'users',
                     'label' => 'User ID\'s',
                     'rules' => 'required'
                 )
             );


             #setting Rule
             $this->form_validation->set_rules( $config );


             # Setting Error Messages for rules
             $this->form_validation->set_message( 'required', $this->lang->line( "msg_sfx" ).'%s' );


             #running form validation
             if ( $this->form_validation->run() ) {
                 $users_list = array_filter( explode( ",", $post['users'] ) );

                 #deleting user selected chat
                 $insert_arr = $this->get_insert_data( $GLOBALS['api_user_id'], $users_list );

                 if ( isset( $insert_arr ) && !empty( $insert_arr ) && FALSE !== $insert_arr ) {

                     $this->db->insert_batch( "chat_clear", $insert_arr );

                     #setting Response
                     $response_array = [
                         'code'    => SUCCESS_CODE,
                         'message' => $this->lang->line( 'chat_deleted' )
                     ];
                 }#end if
                 else {
                     throw new Exception( $this->lang->line( 'chat_not_deleted' ), TRY_AGAIN_CODE );
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
             }#else end

             $this->response( $response_array );
             #=======
         }#try end
         catch ( Exception $ex ) {

             #setting response
             $response_array = [
                 "CODE"    => $ex->getCode(),
                 "MESSAGE" => $ex->getMessage()
             ];

             #sending response
             $this->response( $response_array );
         }#catch end

     }



     /**
      * @function get_insert_data
      * @description to get user chat list, to delete
      * @param type $login_user
      * @param type $users
      * @return boolean
      */
     private function get_insert_data( $login_user, $users ) {

         foreach ( $users as $val ) {
             $data_arr[] = ["clear_by" => $login_user, "chat_of" => $val, "clear_at" => time()];
         }
         if ( isset( $data_arr ) && !empty( $data_arr ) ) {
             return $data_arr;
         }
         else {
             return FALSE;
         }

     }



 }
