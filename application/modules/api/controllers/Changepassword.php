<?php
 require APPPATH.'libraries/REST_Controller.php';

 /**
  * @class Changepassword
  * @description This api controller is used to change logged in user password
  *             User Authentication is checked using Hook in Custom_hook
  */
 class Changepassword extends REST_Controller {

     public function __construct() {
         parent::__construct();

     }



     /**
      * @SWG\Post(path="/Changepassword",
      *   tags={"User"},
      *   summary="Change Password",
      *   description="Change Password",
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
      *     name="oldpassword",
      *     in="formData",
      *     description="oldpassword",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="accesstoken",
      *     in="formData",
      *     description="Access Token",
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
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      *   @SWG\Response(response=490, description="Old password not matched"),
      *   @SWG\Response(response=491, description="New and Old password are same"),
      *   @SWG\Response(response=501, description="Please try again"),
      * )
      */
     public function index_post() {
         try {
             $postDataArr    = $this->post();
             $response_array = array ();

             #setting form validation rule
             $config = array (
                 array (
                     'field' => 'password',
                     'label' => 'Password',
                     'rules' => 'required|min_length[8]'
                 ),
                 array (
                     'field' => 'oldpassword',
                     'label' => 'Old Password',
                     'rules' => 'required'
                 )
             );
             #setting Rules
             $this->form_validation->set_rules( $config );

             #Setting Error Messages for rules
             $this->form_validation->set_message( 'required', 'Please enter the %s' );

             #Checking form validation
             if ( $this->form_validation->run() ) {
                 #getting Access Token from GLobal Array Using Hooks
                 $accesstoken = $GLOBALS['login_user']['Accesstoken'];
                 $respArr     = $this->Common_model->getUserInfo( $accesstoken, ['u.user_id', 'password', 'status'] );
                 $user_info   = [];

                 #Response is not success if session expired or invalid access token
                 if ( SUCCESS_CODE === $respArr['code'] ) {
                     $user_info = $respArr['userinfo'];

                     #Old password from POST array
                     $oldpassword = encrypt( $postDataArr['oldpassword'] );

                     #new password From POST Array
                     $newpassword = encrypt( $postDataArr['password'] );


                     #match old password and entered current password
                     if ( isset( $user_info['password'] ) && $user_info['password'] != $oldpassword ) {

                         #setting Response Array
                         $response_array = [
                             'CODE'    => OLD_PASSWORD_MISMATCH,
                             'MESSAGE' => $this->lang->line( 'old_password_wrong' ),
                             'result'  => []
                         ];
                     }#if end
                     else if ( $oldpassword == $newpassword ) {#if old password is same as new password
                         #setting Response Array
                         $response_array = [
                             'CODE'    => NEW_PASSWORD_SAME,
                             'MESSAGE' => $this->lang->line( 'password_exist' ),
                             'result'  => []
                         ];
                     }#else IF end
                     else {

                         #start transaction
                         $this->db->trans_begin();

                         $newdata = array ('password' => $newpassword, 'updated_at' => datetime());

                         #updating password and updated_at time
                         $isSuccess = $this->Common_model->update_single( 'ai_user', $newdata, array ('where' => array ('user_id' => $user_info['user_id'])) );

                         #If query failed to update password
                         #Throw Exception
                         if ( !$isSuccess ) {
                             $this->db->trans_rollback();
                             throw new Exception( $this->lang->line( 'try_again' ) );
                         }

                         #if transaction runs successfully
                         if ( TRUE === $this->db->trans_status() ) {

                             #Comminting changes
                             $this->db->trans_commit();

                             #setting Response Array
                             $response_array = [
                                 'CODE'    => SUCCESS_CODE,
                                 'MESSAGE' => $this->lang->line( 'password_change_success' ),
                                 'result'  => []
                             ];
                         }
                         else {#IF transaction failed
                             #rolling back
                             $this->db->trans_rollback();

                             #setting Response Array
                             $response_array = [
                                 'CODE'    => TRY_AGAIN_CODE,
                                 'MESSAGE' => $this->lang->line( 'try_again' ),
                                 'result'  => []
                             ];
                         }
                     }
                 }#if END
                 else {

                     #setting Response Array
                     $response_array = $respArr;
                 }
             }
             else {
                 $err = $this->form_validation->error_array();
                 $arr = array_values( $err );

                 #setting Response Array
                 $response_array = [
                     'code'   => PARAM_REQ,
                     'msg'    => $arr[0],
                     'result' => []
                 ];
             }

             #sending Response
             $this->response( $response_array );
         }#TRY END
         catch ( Exception $e ) {
             $this->db->trans_rollback();
             $error = $e->getMessage();

             #setting response
             $response_array = [
                 'code'   => TRY_AGAIN_CODE,
                 'msg'    => $error,
                 'result' => []
             ];

             #sending Response
             $this->response( $response_array );
         }#CATCH END

     }



 }
