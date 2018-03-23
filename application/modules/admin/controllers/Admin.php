<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

 class Admin extends MY_Controller {

     function __construct() {
         parent::__construct();
         $this->load->model( 'Common_model' );
         $this->lang->load( 'common', "english" );
         $sessionData = validate_admin_cookie( 'rcc_appinventiv', 'admin' );
         if ( $sessionData ) {
             $this->session->set_userdata( 'admininfo', $sessionData );
         }
         $this->admininfo = $this->session->userdata( 'admininfo' );

         if ( $this->admininfo ) {
             redirect( base_url()."admin/Dashboard" );
         }

     }



     /**
      * @function:index
      * @description:if email and password are correct then he can login
      *
      * @param: string email
      * @param: string password
      */
     public function index() {
         try {//TRY
             $data        = [];
             $postDataArr = $this->input->post();
             if ( count( $postDataArr ) ) {//IF START
                 $this->form_validation->set_rules( 'email', $this->lang->line( 'email' ), 'trim|required' );
                 $this->form_validation->set_rules( 'password', $this->lang->line( 'password' ), 'trim|required' );

                 //Cheking Form validation
                 if ( $this->form_validation->run() ) {

                     //Value to Fetch for login
                     $value_to_featch = ['create_date', 'status', 'admin_id', 'admin_name', 'admin_email', 'admin_profile_pic', 'admin_profile_thumb', 'role_id'];

                     //Where Condition
                     $where = [
                         "where" => [
                             'admin_email'    => $postDataArr['email'],
                             'admin_password' => hash( 'sha256', $postDataArr['password'] )
                         ]
                     ];

                     $adminInfo = $this->Common_model->fetch_data( 'admin', $value_to_featch, $where, true );

                     //IF SUCCESSFULLY Logged in
                     if ( !empty( $adminInfo ) ) {

                         #IF USER STATUS IS ACTIVE
                         # 1 = Active
                         # 2 = Blocked
                         if ( 1 == $adminInfo['status'] ) {
                             $admindata = array (
                                 "admin_id"            => $adminInfo['admin_id'],
                                 "admin_name"          => $adminInfo['admin_name'],
                                 "admin_email"         => $adminInfo['admin_email'],
                                 "admin_profile_pic"   => $adminInfo['admin_profile_pic'],
                                 "admin_profile_thumb" => $adminInfo['admin_profile_thumb'],
                                 "role_id"             => $adminInfo['role_id'],
                             );

                             //IS user want stay logged in
                             if ( (isset( $postDataArr["remember_me"] ) && "remember_me" === $postDataArr["remember_me"] ) ) {
                                 $this->remember_me( $adminInfo );
                             }//END Rember me

                             $this->session->set_userdata( 'admininfo', $admindata );
                             $this->session->set_flashdata( "greetings", $this->lang->line( 'welcome' ) );
                             $this->session->set_flashdata( "message", $this->lang->line( 'login_welcome' ) );

                             redirect( base_url()."admin/dashboard" );
                         }//IF End
                         else {//USER IS Blocked
                             $data['email'] = $postDataArr['email'];
                             $this->session->set_flashdata( "greetings", $this->lang->line( 'sorry' ) );
                             $this->session->set_flashdata( "message", $this->lang->line( 'account_blocked' ) );
                         }
                     }//IF END
                     else {//User not Found
                         $data['email'] = $postDataArr['email'];
                         $this->session->set_flashdata( "greetings", $this->lang->line( 'sorry' ) );
                         $this->session->set_flashdata( "message", $this->lang->line( 'invalid_email_password' ) );
                     }//ELSE END
                 }//END IF
             }
             load_outer_views( '/admin/login', $data );
         }//TRY END
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }//CATCH END

     }



     /**
      * @function remember_me
      * @description If user want to stay logged in, this function will save a cookie for next 168 Hours
      *
      * @param array $adminInfo Logged In Admin details to generate cookies
      */
     private function remember_me( $adminInfo ) {
         $this->load->helper( ["cookie", "string"] );
         $cookieData["cookie_validator"] = random_string( 'alnum', 12 );
         $cookieData["cookie_selector"]  = hash( "sha256", date( "Y-m-d H:i:s" ).$adminInfo["admin_email"] );

         // Cookie Expiry time to Next 168 Hours or 7 Days
         $cookieExpiryTime = time() + COOKIE_EXPIRY_TIME;

         set_cookie(
             "rcc_appinventiv", "{$cookieData['cookie_selector']}:{$cookieData['cookie_validator']}", $cookieExpiryTime
         );

         $cookieData["cookie_validator"] = hash( "sha256", $cookieData["cookie_validator"].$adminInfo["create_date"] );

         $this->Common_model->update_single( "admin", $cookieData, ["where" => ["admin_id" => $adminInfo["admin_id"]]] );

     }



     /**
      * @function forgot
      * @description function to call forgot page and forgot functionality to send mail
      */
     public function forgot() {

         ##New Code start
         try {
             $data       = [];
             $post_array = $this->input->post();

             //Is post araay having value
             if ( count( $post_array ) ) {

                 //checking form validation
                 $this->form_validation->set_rules( 'email', $this->lang->line( 'email' ), 'trim|required' );

                 //is form validation run true
                 if ( $this->form_validation->run() ) {

                     //loading Commonfn library
                     $this->load->library( "commonfn" );

                     $admininfo = $this->Common_model->fetch_data( 'admin', 'admin_email, admin_name', array ('where' => array ('admin_email' => $post_array['email'])), true );

                     //if admin information are correct
                     if ( !empty( $admininfo ) && is_array( $admininfo ) ) {

                         $subject                = $this->lang->line( 'reset_password' );
                         $reset_token            = hash( 'sha256', date( "Y-m-d h:i:s" ) );
                         $timeexpire             = time() + (24 * 60 * 60);
                         $insert['reset_token']  = $reset_token;
                         $insert['timestampexp'] = $timeexpire;
                         $where                  = array ("where" => array ('admin_email' => $admininfo['admin_email']));

                         //update token in DB
                         $update    = $this->Common_model->update_single( 'admin', $insert, $where );
                         $isSuccess = false;
                         //Array to send mail
                         if ( $update ) {
                             $mailinfoarr = [
                                 "link"       => base_url().'admin/reset?token='.$reset_token,
                                 "email"      => $admininfo['admin_email'],
                                 "subject"    => $subject,
                                 "name"       => $admininfo['admin_name'],
                                 "mailerName" => 'forgot'
                             ];
                             //sending email
                             $isSuccess   = $this->commonfn->sendEmailToUser( $mailinfoarr );
                         }

                         //if mail send successfuly
                         if ( $isSuccess ) {
                             $this->session->set_flashdata( 'Success', $this->lang->line( 'success_prefix' ).$this->lang->line( 'reset_email' ).$this->lang->line( 'success_suffix' ) );

                             //redirect to forgot page
                             redirect( base_url().'admin/forgot' );
                         }//IF END
                         else {
                             $this->session->set_flashdata( 'Success', $this->lang->line( 'error_prefix' ).$this->lang->line( 'something_went_wrong' ).$this->lang->line( 'error_suffix' ) );
                         }//ELSE END
                     }//if end
                     else {
                         $data['error'] = $this->lang->line( 'invalid_email' );
                         $this->session->set_flashdata( 'Success', $this->lang->line( 'error_prefix' ).$this->lang->line( 'invalid_email' ).$this->lang->line( 'error_suffix' ) );
                     }
                 }//form validation if end
             }//if end
             load_outer_views( '/admin/forgotpassword', $data );
         }//TRY END
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }//CATCH END
         ##New Code End

     }



     /**
      * @function:reset password
      * @param:N/A
      * @description:admin can set the password again
      */
     public function reset() {
         try {//TRY
             $post  = $this->input->post();
             $token = $this->input->get( 'token' );

             //if token is missing
             if ( empty( $token ) ) {
                 show404( $this->lang->line( 'invalid_request' ) );
                 return;
             }#IF END


             /* getting token information from DB */
             $result = $this->Common_model->fetch_data( 'admin', 'admin_id, admin_email,reset_token,timestampexp', array ('where' => array ('reset_token' => $token)), true );

             //if post is not empty
             if ( !empty( $post ) ) {

                 $newPass = $post['password'];

                 //if password is not blank
                 if ( !empty( $newPass ) ) {

                     //getting Current time
                     $currenttime = time();

                     //IF 3
                     if ( !empty( $result ) ) {

                         $pass = hash( 'sha256', $newPass );

                         //IF 4 Checking token time expiration
                         if ( $currenttime < $result['timestampexp'] ) {

                             $updateArr         = [];
                             $whereArr['where'] = ['admin_email' => $result['admin_email']];

                             $updateArr = ['reset_token' => "", 'admin_password' => $pass];
                             $update    = $this->Common_model->update_single( 'admin', $updateArr, $whereArr );

                             if ( $update ) {//IF 5
                                 $this->session->set_flashdata( 'message', $this->lang->line( 'success_prefix' ).$this->lang->line( 'password_changed' ).$this->lang->line( 'success_suffix' ) );
                                 redirect( '/admin/' );
                             }//IF 5 END
                             else {
                                 $this->session->set_flashdata( 'message', $this->lang->line( 'error_prefix' ).$this->lang->line( 'error_suffix' ) );
                                 $data["csrfName"]  = $this->security->get_csrf_token_name();
                                 $data["csrfToken"] = $this->security->get_csrf_hash();
                             }//ELSE END
                         }//IF 4 END
                         else {
                             show404( $this->lang->line( 'link_expired' ) );
                             return;
                         }//ELSE END
                     }//IF 3 END
                     else {
                         show404( $this->lang->line( 'invalid_token' ) );
                         return;
                     }
                 }//IF 2 END
             }//IF 1 END
             else {
                 if ( !empty( $result ) ) {
                     $data["csrfName"]  = $this->security->get_csrf_token_name();
                     $data["csrfToken"] = $this->security->get_csrf_hash();
                 }
                 else {
                     show404( $this->lang->line( 'invalid_token' ) );
                     return;
                 }
             }

             load_outer_views( '/admin/resetpassword', $data );
         }//TRY END
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }//CATCH END

     }



     /**
      * @function:check_email_avalibility
      * @description:If email is exist in db or not
      * @param:N/A
      */
     public function check_email_avalibility() {
         try {//TRY
             //CHECKING IS REQUEST BY AJAX
             if ( !$this->input->is_ajax_request() ) {//IF 1
                 exit( 'No Direct Script allowed' );
             }//IF 1 END

             $postemail = $this->input->post( 'email' );
             $csrftoken = $this->security->get_csrf_hash();
             $respArr   = $this->Common_model->fetch_data( 'admin', '*', array ('where' => array ('admin_email' => $postemail)), true );

             if ( $respArr ) {//IF 2
                 $respArr = array ('code' => 201, 'msg' => $this->lang->line( 'email_exists' ), "csrf_token" => $csrftoken);
             }//IF 2 END
             else {
                 $respArr = array ('code' => 200, 'msg' => $this->lang->line( 'email_not_found' ), "csrf_token" => $csrftoken);
             }//ELSE END
             echo json_encode( $respArr );
             die;
         }//TRY END
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }//CATCH END

     }



 }
