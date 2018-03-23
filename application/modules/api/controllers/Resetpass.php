<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );
 require APPPATH.'/libraries/REST_Controller.php';

 class Resetpass extends REST_Controller {

     function __construct() {
         parent::__construct();

         $this->load->model( 'Common_model' );
         $this->load->library( 'commonfn' );

     }



     /**
      * @SWG\Post(path="/Resetpass",
      *   tags={"User"},
      *   summary="Reset Password",
      *   description="Reset Password",
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
      *     name="email",
      *     in="formData",
      *     description="userId",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="password",
      *     in="formData",
      *     description="New password",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Response(response=200, description="Reset Password Success"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=301, description="Password already set"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      *   @SWG\Response(response=501, description="Please try again"),
      * )
      */
     public function index_post() {
         try {
             $postDataArr    = $this->post();
             $response_array = [];

             #setting form validation configuration
             $config = array (
                 array (
                     'field' => 'userId',
                     'label' => 'user Id',
                     'rules' => 'required'
                 ),
                 array (
                     'field' => 'password',
                     'label' => 'Password',
                     'rules' => 'trim|required'
                 )
             );
             $this->form_validation->set_rules( $config );

             $this->form_validation->set_message( 'required', 'Please enter the %s' );

             #checking form validation
             if ( $this->form_validation->run() ) {

                 $userId   = $postDataArr['userId'];
                 $password = $postDataArr['password'];


                 $where    = array ('where' => array ('user_id' => $userId));
                 #fetching user details from db
                 $userInfo = $this->Common_model->fetch_data( 'ai_user', array ('isreset_link_sent'), $where, true );


                 if ( !empty( $userInfo ) && $userInfo['isreset_link_sent'] != 0 ) {#if start
                     #Encrypt the password
                     $password = encrypt( $password );

                     #setting update condition
                     $updatearr = array ('password' => $password, 'isreset_link_sent' => 0);

                     #setting where condition
                     $where = array ('where' => array ('user_id' => $userId));

                     #updating user password in DB
                     $issuccess = $this->Common_model->update_single( 'ai_user', $updatearr, $where );

                     #if details updated successfuly
                     if ( $issuccess ) {
                         #setting response
                         $response_array = [
                             'code' => SUCCESS_CODE,
                             'msg'  => $this->lang->line( 'password_reset_success' )
                         ];
                     }#end if
                     else {#if updating failed
                         #setting response
                         $response_array = [
                             'code' => TRY_AGAIN_CODE,
                             'msg'  => $this->lang->line( 'try_again' )
                         ];
                     }#else end
                 }#end if
                 else {
                     #setting response
                     $response_array = [
                         'code' => PASSWORD_ALREADY_SET,
                         'msg'  => $this->lang->line( 'password_already_reset' )
                     ];
                 }#else END
                 #sending response
                 $this->response( $response_array );
             }#end if
             else {

                 $err = $this->form_validation->error_array();
                 $arr = array_values( $err );

                 #setting response
                 $response_array = [
                     'code'   => PARAM_REQ,
                     'msg'    => $arr[0],
                     'result' => []
                 ];

                 #sending response
                 $this->response( $response_array );
             }#else end
         }#try end
         catch ( Exception $ex ) {

             #setting response
             $response_array = [
                 'code' => TRY_AGAIN_CODE,
                 'msg'  => $ex->getMessage()
             ];

             #sending response
             $this->response( $response_array );
         }#catch End

     }



 }
