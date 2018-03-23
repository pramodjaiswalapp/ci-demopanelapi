<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );
 require APPPATH.'/libraries/REST_Controller.php';

 class Signup extends REST_Controller {

     function __construct() {
         parent::__construct();
         $this->load->helper( 'security' );
         $this->load->library( 'commonfn' );

     }



     /**
      * @SWG\Post(path="/Signup",
      *   tags={"User"},
      *   summary="Singup Information",
      *   description="Singup Information",
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
      *     name="first_name",
      *     in="formData",
      *     description="Users first name",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="last_name",
      *     in="formData",
      *     description="Users last name",
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="email",
      *     in="formData",
      *     description="Email",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="password",
      *     in="formData",
      *     description="Password",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="gender",
      *     in="formData",
      *     description="1: Male, 2: Female",
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="phone",
      *     in="formData",
      *     description="Phone Number",
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="dob",
      *     in="formData",
      *     description="Date of Birth m/d/Y",
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="address",
      *     in="formData",
      *     description="Address",
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="user_lat",
      *     in="formData",
      *     description="Lattitude",
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="user_long",
      *     in="formData",
      *     description="Longitude",
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="country_id",
      *     in="formData",
      *     description="country Id",
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="state_id",
      *     in="formData",
      *     description="State Id",
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="city_id",
      *     in="formData",
      *     description="City Id",
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="device_id",
      *     in="formData",
      *     description="Unique Device Id",
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="device_token",
      *     in="formData",
      *     description="Device Token required to send push",
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="platform",
      *     in="formData",
      *     description="1: Android and 2: iOS",
      *     type="string"
      *   ),
      *   @SWG\Response(response=200, description="Signup Success"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=421, description="File Upload Failed"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      * )
      */
     public function index_post() {
         try {
             $postDataArr    = $this->post();
             $response_array = [];

             #setting Form  validation Rules
             $required_fields_arr = array (
                 array (
                     'field' => 'first_name',
                     'label' => 'First Name',
                     'rules' => 'trim|required'
                 ),
                 array (
                     'field' => 'email',
                     'label' => 'Email',
                     'rules' => 'trim|required|valid_email'
                 ),
                 array (
                     'field' => 'password',
                     'label' => 'Password',
                     'rules' => 'trim|required'
                 ),
                 array (
                     'field' => 'device_id',
                     'label' => 'Device Id',
                     'rules' => 'trim|required'
                 )
             );


             #Setting Error Messages for rules
             $this->form_validation->set_rules( $required_fields_arr );
             $this->form_validation->set_message( 'is_unique', 'The %s is already registered with us' );
             $this->form_validation->set_message( 'required', 'Please enter the %s' );

             #checking if form fields are valid or not
             if ( $this->form_validation->run() ) {

                 #setting Default values to array KEYS
                 $default   = array (
                     "middle_name" => 20,
                     "last_name"   => "",
                     "gender"      => "",
                     "dob"         => "",
                     "age"         => 1,
                     "phone"       => "",
                     "address"     => "",
                     "user_lat"    => "",
                     "user_long"   => "",
                     "country_id"  => "",
                     "state_id"    => "",
                     "city_id"     => ""
                 );
                 $signupArr = defaultValue( $postDataArr, $default );


                 #checking phone number is valid or not
                 if ( "" != $signupArr['phone'] ) {
                     $this->validate_phone( $signupArr['phone'] );
                 }


                 #Checking date of birth is valid or not
                 if ( "" != $signupArr['dob'] ) {
                     $this->validate_dob( $signupArr['dob'] );
                 }

                 #Check if email if already registered and is it blocked
                 $whereArr          = [];
                 $whereArr['where'] = ['email' => $postDataArr['email']];
                 $user_info         = $this->Common_model->fetch_data( 'ai_user', ['email', 'status'], $whereArr, true );

                 #is user Blocked
                 if ( !empty( $user_info ) && 2 == $user_info['status'] ) {
                     #user blocked/Setting Response
                     $response_array = [
                         'code'   => ACCOUNT_BLOCKED,
                         'msg'    => $this->lang->line( 'account_blocked' ),
                         'result' => []
                     ];

                     $this->response( $response_array );
                 }
                 else if ( !empty( $user_info ) && 1 == $user_info['status'] ) {

                     #user is already Registered
                     $response_array = [
                         'code'   => EMAIL_ALREADY_EXIST,
                         'msg'    => $this->lang->line( 'account_exist' ),
                         'result' => []
                     ];
                 }
                 else {

                     $signupArr                    = [];
                     $signupArr["first_name"]      = trim( $postDataArr["first_name"] );
                     $signupArr["email"]           = $postDataArr["email"];
                     $signupArr["password"]        = encrypt( $postDataArr["password"] );
                     $signupArr["registered_date"] = date( 'Y-m-d H:i:s' );


                     #Uploading Use Profile Image if available in Request
                     if ( isset( $_FILES['profile_image'] ) && !empty( $_FILES['profile_image'] ) ) {
                         /*
                          * get configuration file for upload (common helper)
                          * @params: Target Upload Path,Accepted Format,Max Size,Max Width,Max Hieght,Encrpt Name
                          */
                         $config = [];
                         $config = getConfig( UPLOAD_IMAGE_PATH, 'jpeg|jpg|png', 3000, 1024, 768 );
                         $this->load->library( 'upload', $config );

                         #Do-Upload if start
                         if ( $this->upload->do_upload( 'profile_image' ) ) {

                             $upload_data   = $this->upload->data();
                             $imageName     = $upload_data['file_name'];
                             $thumbFileName = $upload_data['file_name'];
                             $fileSource    = UPLOAD_IMAGE_PATH.$thumbFileName;
                             $targetPath    = UPLOAD_THUMB_IMAGE_PATH;
                             $isSuccess     = $this->commonfn->thumb_create( $thumbFileName, $fileSource, $targetPath );

                             if ( $isSuccess ) {
                                 $thumbName = $imageName;
                             }
                         }#END Do-Upload If
                         else {#ELSE Start
                             $response_array = [
                                 'code'   => ERROR_UPLOAD_FILE,
                                 'msg'    => strip_tags( $this->upload->display_errors() ),
                                 'result' => $signupArr
                             ];
                             $this->response( $response_array );
                         }#END Else


                         $signupArr["image"]       = $imageName;
                         $signupArr["image_thumb"] = $thumbName;
                     }#Profile pIc end


                     $this->db->trans_begin(); #DB transaction Start


                     $userId = $this->Common_model->insert_single( 'ai_user', $signupArr ); #save values in DB


                     if ( !$userId ) {
                         throw new Exception( $this->lang->line( 'try_again' ) );
                     }


                     $postDataArr['user_id'] = $userId;


                     #Generate Public and Private Access Token
                     $accessToken = create_access_token( $userId, $signupArr['email'] );

                     #Access Token for signup
                     $signupArr['accesstoken'] = $accessToken['public_key'].'||'.$accessToken['private_key'];
                     $signupArr["image"]       = isset( $signupArr['image'] ) ? IMAGE_PATH.$signupArr['image'] : "";
                     $signupArr["image_thumb"] = isset( $signupArr['image_thumb'] ) ? THUMB_IMAGE_PATH.$signupArr['image_thumb'] : "";
                     $signupArr['user_id']     = $userId;

                     #Setting session after Signup
                     $sessionArr = setSessionVariables( $postDataArr, $accessToken );

                     /*
                      * Insert Session Data
                      */
                     $whereArr          = [];
                     $device_id         = isset( $postDataArr['device_id'] ) ? $postDataArr['device_id'] : "";
                     $whereArr['where'] = ['device_id' => $device_id];
                     $isExist           = $this->Common_model->fetch_data( 'ai_session', array ('session_id'), $whereArr, true );

                     /*
                      * If user has logged in previously with same device then update his detail
                      * or insert as a new row
                      */
                     if ( !empty( $isExist ) ) {
                         #updating session table
                         $sessionId = $this->Common_model->update_single( 'ai_session', $sessionArr, $whereArr );
                     }
                     else {
                         #inserting session details
                         $sessionId = $this->Common_model->insert_single( 'ai_session', $sessionArr );
                     }


                     #Checking transaction status
                     if ( $this->db->trans_status() ) {

                         #comminting Transaction
                         $this->db->trans_commit();

                         if ( !empty( $sessionId ) && !empty( $userId ) ) {#if Start
                             unset( $signupArr['password'] );
                             $mailData          = [];
                             $mailData['name']  = $postDataArr['first_name'].' '.$postDataArr['last_name'];
                             $mailData['email'] = $postDataArr['email'];

                             #sending welcome mail
                             $this->sendWelcomeMail( $mailData );

                             #setting response
                             $response_array = [
                                 'code'   => SUCCESS_CODE,
                                 'msg'    => $this->lang->line( 'registration_success' ),
                                 'result' => $signupArr
                             ];
                         }#if End
                     }
                     else {
                         $this->db->trans_rollback();

                         #setting response
                         $response_array = [
                             'code'   => TRY_AGAIN_CODE,
                             'msg'    => $this->lang->line( 'try_again' ),
                             'result' => []
                         ];
                     }
                 }
             }#form validation End
             else {
                 $err = $this->form_validation->error_array();
                 $arr = array_values( $err );
                 $this->response( array ('code' => PARAM_REQ, 'msg' => $arr[0], 'result' => []) );
             }
             $this->response( $response_array );
         }#TRY END
         catch ( Exception $e ) {

             #if transaction failed than rollback
             $this->db->trans_rollback();
             $error = $e->getMessage();

             #setting response
             $response_array = [
                 'code'   => TRY_AGAIN_CODE,
                 'msg'    => $error,
                 'result' => []
             ];

             #sending response
             $this->response( $response_array );
         }#Catch End

     }



     /**
      * @function sendWelcomeMail
      * @description Sending welcome mail to user
      *
      * @param array $mailData user data needed to send mail, user email address and user name
      */
     public function sendWelcomeMail( $mailData ) {

         $this->load->helper( 'url' );
         $data        = [];
         $data['url'] = base_url().'request/welcomeMail?email='.$mailData['email'].'&name='.urlencode( $mailData['name'] );
         sendGetRequest( $data );

     }



     /**
      * @funciton validate_phone
      * @description Custom validation rules to validate phone number
      *
      * @param  $phone user phone number to validate phone number
      * @return boolean|json
      */
     public function validate_phone( $phone ) {

         if ( isset( $phone ) && !preg_match( "/^[0-9]{10}$/", $phone ) && !empty( $phone ) ) {
             #setting Response
             $response_array = [
                 'code'   => PARAM_REQ,
                 'msg'    => $this->lang->line( 'invalid_phone' ),
                 'result' => []
             ];

             #sending Response
             $this->response( $response_array );
         }
         else {
             return true;
         }

     }



     /*
      * Custom Rule Validate Dob
      * @param: user dob
      */

     /**
      * @function validate_dob
      * @description Custom Rule Validation For Date Of Birth
      *
      * @param date $dob user date of birth to check
      * @return boolean
      */
     public function validate_dob( $dob ) {
         if ( !(isValidDate( $dob, 'm-d-Y' )) ) {
             #setting response
             $response_array = [
                 'code'   => PARAM_REQ,
                 'msg'    => $this->lang->line( 'invalid_dob' ),
                 'result' => []
             ];

             #sending Response
             $this->response( $response_array );
         }
         else {
             return true;
         }

     }



 }
