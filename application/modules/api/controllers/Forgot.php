<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );
 require APPPATH.'/libraries/REST_Controller.php';

 class Forgot extends REST_Controller {

     function __construct() {
         parent::__construct();

         $this->load->model( 'Common_model' );
         $this->load->library( 'commonfn' );

     }



     /**
      * @SWG\Post(path="/Forgot",
      *   tags={"User"},
      *   summary="Forgot Password",
      *   description="Forgot Password",
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
      *     description="Email",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Response(response=200, description="Forgot Email Send Success"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=211, description="Email Send failed"),
      *   @SWG\Response(response=302, description="Email in not registered"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      * )
      */
     public function index_post() {
         try {
             $post_data      = $this->post();
             $response_array = [];
             #Setting form validation
             $config         = array (
                 array (
                     'field' => 'email',
                     'label' => 'Email',
                     'rules' => 'trim|required|valid_email'
                 )
             );
             $this->form_validation->set_rules( $config );

             #Checking Fomr Fields Validation
             if ( $this->form_validation->run() ) {

                 $where          = [];
                 $where['where'] = array ('email' => $post_data['email']);
                 $userInfo       = $this->Common_model->fetch_data( 'ai_user', array ('CONCAT(first_name," ",middle_name) as name', 'email', 'status'), $where, true );

                 if ( !empty( $userInfo ) && 1 == $userInfo['status'] ) {

                     #Encrypt the user-email
                     $token                     = $userInfo['name'].uniqid();
                     $ciphertext                = encryptDecrypt( $token );
                     $mailInfoArr               = array ();
                     $mailInfoArr['subject']    = $this->lang->line( 'reset_password' );
                     $mailInfoArr['mailerName'] = 'reset.php';
                     $mailInfoArr['email']      = $userInfo['email'];
                     $mailInfoArr['name']       = $userInfo['name'];
                     $mailInfoArr['link']       = base_url().'reset?token='.$ciphertext;


                     #Send Email to user with above mentioned detail
                     $isSuccess = $this->commonfn->sendEmailToUser( $mailInfoArr );

                     if ( !$isSuccess ) {
                         throw new Exception( $this->email->print_debugger() );
                     }

                     #transaction Begin
                     $this->db->trans_begin();
                     $updatearr = array ('isreset_link_sent' => 1, 'reset_link_time' => datetime(), 'reset_token' => $ciphertext);
                     $where     = array ('where' => array ('email' => $userInfo['email']));

                     $isSuccess = $this->Common_model->update_single( 'ai_user', $updatearr, $where );

                     if ( !$isSuccess ) {
                         throw new Exception( $this->lang->line( 'try_again' ) );
                     }

                     #Checking transaction Status
                     if ( TRUE === $this->db->trans_status() ) {

                         #Commiting transation
                         $this->db->trans_commit();

                         #setting response
                         $response_array = [
                             'code'   => SUCCESS_CODE,
                             'msg'    => $this->lang->line( 'email_link_sent' ),
                             'result' => []
                         ];
                     }
                 }
                 else if ( !empty( $userInfo ) && $userInfo['status'] == 2 ) {

                     #setting response
                     $response_array = [
                         'code'   => ACCOUNT_BLOCKED,
                         'msg'    => $this->lang->line( 'account_blocked' ),
                         'result' => []
                     ];
                 }
                 else {

                     #setting response
                     $response_array = [
                         'code'   => EMAIL_NOT_EXIST,
                         'msg'    => $this->lang->line( 'email_not_exists' ),
                         'result' => []
                     ];
                 }
             }
             else {
                 $err = $this->form_validation->error_array();
                 $arr = array_values( $err );

                 #setting response
                 $response_array = [
                     'code'   => PARAM_REQ,
                     'msg'    => $arr[0],
                     'result' => []
                 ];
             }
             $this->response( $response_array );
         }
         catch ( Exception $e ) {
             $this->db->trans_rollback();
             $error = $e->getMessage();

             #setting response
             $response_array = [
                 'code'   => TRY_AGAIN_CODE,
                 'msg'    => $error,
                 'result' => []
             ];
             $this->response( $response_array );
         }

     }



 }
