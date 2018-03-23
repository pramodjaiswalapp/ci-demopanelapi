<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

 class AjaxUtil extends MX_Controller {

     public function __construct() {
         $this->load->model( "CommonModel" );
         $this->load->model( "Common_model" );
         $this->load->library( "session" );
         $this->load->library( 'S3' );
         $this->lang->load( 'common', "english" );
         $this->admininfo = $this->session->userdata( 'admininfo' );
         if ( !$this->input->is_ajax_request() ) {
             exit( 'No direct script access allowed' );
         }
         $this->csrftoken = $this->security->get_csrf_hash();

     }



     /**
      * @function emailExistsAjax
      * @description AJAX Handler for email exists
      */
     public function emailExistsAjax() {
         $postData = $this->input->post();
         if ( !isset( $postData["email"] ) || empty( $postData["email"] ) ) {
             $errorData = [
                 "error"      => true,
                 "message"    => "fields are not set",
                 "csrf_token" => $this->security->get_csrf_hash()
             ];
             $this->CommonModel->response( $errorData );
         }
         else {
             if ( $this->CommonModel->fetchData( ["id"], "users", ["email" => $postData["email"]] ) ) {
                 $errorData = [
                     "error"      => true,
                     "message"    => "Email already in use.",
                     "csrf_token" => $this->security->get_csrf_hash()
                 ];
                 $this->CommonModel->response( $errorData );
             }
             else {
                 $errorData = [
                     "error"      => false,
                     "message"    => "Email available.",
                     "csrf_token" => $this->security->get_csrf_hash()
                 ];
                 $this->CommonModel->response( $errorData );
             }
         }

     }



     /**
      * @function mobileExistsAjax
      * @description AJAX HANDLER FOR MOBILE NUMBER EXISTS
      */
     public function mobileExistsAjax() {
         $postData = $this->input->post();
         if ( !isset( $postData["mobile_number"] ) || empty( $postData["mobile_number"] ) ) {
             $errorData = [
                 "error"      => true,
                 "message"    => "fields are not set",
                 "csrf_token" => $this->security->get_csrf_hash()
             ];
             $this->CommonModel->response( $errorData );
         }
         else {
             if ( $this->CommonModel->fetchData( ["id"], "users", ["mobile_number" => $postData["mobile_number"]] ) ) {
                 $errorData = [
                     "error"      => true,
                     "message"    => "Mobile number already in use.",
                     "csrf_token" => $this->security->get_csrf_hash()
                 ];
                 $this->CommonModel->response( $errorData );
             }
             else {
                 $errorData = [
                     "error"      => false,
                     "message"    => "Mobile number available.",
                     "csrf_token" => $this->security->get_csrf_hash()
                 ];
                 $this->CommonModel->response( $errorData );
             }
         }

     }



     /**
      * @function profilePictureUpload
      * @description Profile Picture Upload using Amazon s3 storage
      *
      */
     public function profilePictureUpload() {

         $image     = $_FILES['image'];
         $imageSize = getimagesize( $image['tmp_name'] );

         $validMimeTypes = ['image/png', 'image/jpg', 'image/jpeg'];

         if ( !$imageSize || null === $imageSize ) {
             $response = [
                 "success"    => false,
                 "message"    => $this->lang->line( "not_an_image" ),
                 "code"       => NOT_AN_IMAGE,
                 "csrf_token" => $this->security->get_csrf_hash()
             ];
             $this->CommonModel->response( $response );
         }
         else {

         }

         if ( !in_array( $imageSize['mime'], $validMimeTypes ) ) {
             $response = [
                 "success"    => false,
                 "message"    => $this->lang->line( "not_an_image" ),
                 "code"       => NOT_AN_IMAGE,
                 "csrf_token" => $this->security->get_csrf_hash()
             ];
             $this->CommonModel->response( $response );
         }
         else {

         }

         if ( $image['size'] > MAX_IMAGE_SIZE ) {
             $response = [
                 "success"    => false,
                 "message"    => $this->lang->line( "image_too_big" ),
                 "code"       => IMAGE_TOO_BIG,
                 "csrf_token" => $this->security->get_csrf_hash()
             ];
             $this->CommonModel->response( $response );
         }
         else {

         }

         $extension = pathinfo( $image['name'], PATHINFO_EXTENSION );
         $imageName = "Bonapp_".time().".".$extension;

         $result = $this->Common_model->s3_uplode( $imageName, $image['tmp_name'] );
         if ( $result ) {
             $response = [
                 "success"    => true,
                 "csrf_token" => $this->security->get_csrf_hash(),
                 "data"       => $result
             ];
         }
         $this->CommonModel->response( $response );

     }



     /**
      * Handles location for google maps
      * https://maps.googleapis.com/maps/api/geocode/json?key=API_KEY&address=appinventiv%20noida
      */
     public function getLocation() {
         $postData = $this->input->post();
         return $postData;

     }



     /**
      * @function oldpasswordExistsAjax
      * @description AJAX Handler for checking old password
      */
     public function oldpasswordExistsAjax() {
         $postData = $this->input->post();
         $id       = encryptDecrypt( $postData['userid'], 'decrypt' );
         if ( (!isset( $postData["oldpassword"] ) || empty( $postData["oldpassword"] ) ) ) {
             $errorData = [
                 "error"      => true,
                 "message"    => "fields are not set",
                 "csrf_token" => $this->security->get_csrf_hash()
             ];

             $this->CommonModel->response( $errorData );
         }
         else {
             if ( $this->CommonModel->fetchData(
                     ["admin_id"], "admin", ["password" => hash( "sha256", base64_decode( $postData["oldpassword"] ) ), 'admin_id' => $id]
                 )
             ) {

                 $errorData = [
                     "error"      => true,
                     "message"    => "Old password matched.",
                     "csrf_token" => $this->security->get_csrf_hash()
                 ];
                 $this->CommonModel->response( $errorData );
             }
             else {
                 $errorData = [
                     "error"      => false,
                     "message"    => "Old password not matched",
                     "csrf_token" => $this->security->get_csrf_hash()
                 ];
                 $this->CommonModel->response( $errorData );
             }
         }

     }



     //check edit mobile number
     public function editmobileExistsAjax() {
         $postData = $this->input->post();
         $id       = encryptDecrypt( $postData['userid'], 'decrypt' );
         if ( !isset( $postData["mobile_number"] ) || empty( $postData["mobile_number"] ) ) {
             $errorData = [
                 "error"      => true,
                 "message"    => "fields are not set",
                 "csrf_token" => $this->security->get_csrf_hash()
             ];
             $this->CommonModel->response( $errorData );
         }
         else {
             if ( $this->CommonModel->fetchData(
                     ["id"], "users", ["mobile_number" => $postData["mobile_number"], 'id!=' => $id]
                 )
             ) {
                 $errorData = [
                     "error"      => true,
                     "message"    => "Mobile number already in use.",
                     "csrf_token" => $this->security->get_csrf_hash()
                 ];
                 $this->CommonModel->response( $errorData );
             }
             else {
                 $errorData = [
                     "error"      => false,
                     "message"    => "Mobile number available.",
                     "csrf_token" => $this->security->get_csrf_hash()
                 ];
                 $this->CommonModel->response( $errorData );
             }
         }

     }



     /* Check for edit merchant email address */

     /**
      * AJAX Handler for email exists
      */
     public function editemailExistsAjax() {
         $postData = $this->input->post();
         $id       = encryptDecrypt( $postData['userid'], 'decrypt' );

         if ( !isset( $postData["email"] ) || empty( $postData["email"] ) ) {
             $errorData = [
                 "error"      => true,
                 "message"    => "fields are not set",
                 "csrf_token" => $this->security->get_csrf_hash()
             ];
             $this->CommonModel->response( $errorData );
         }
         else {
             if ( $this->CommonModel->fetchData(
                     ["id"], "users", ["email" => $postData["email"], "id!=" => $id]
                 )
             ) {
                 $errorData = [
                     "error"      => true,
                     "message"    => "Email already in use.",
                     "csrf_token" => $this->security->get_csrf_hash()
                 ];
                 $this->CommonModel->response( $errorData );
             }
             else {
                 $errorData = [
                     "error"      => false,
                     "message"    => "Email available.",
                     "csrf_token" => $this->security->get_csrf_hash()
                 ];
                 $this->CommonModel->response( $errorData );
             }
         }

     }



     /**
      * @function changestatus
      * @description change the status of user to block or unblock
      */
     public function changestatus() {
         try {
             $resparr   = array ();
             $userid    = $this->input->post( 'id' );
             $id        = encryptDecrypt( $userid, 'decrypt' );
             $status    = $this->input->post( 'is_blocked' );
             $where     = array ('where' => array ('id' => $id));
             $updateArr = array ('user_status' => $status);
             $result    = $this->Common_model->update_single( 'users', $updateArr, $where );
             $csrftoken = $this->security->get_csrf_hash();
             if ( $result == true ) {
                 $resparr = array ("code" => 200, 'msg' => SUCCESS, "csrf_token" => $csrftoken);
             }
             else {
                 $resparr = array ("code" => 201, 'msg' => TRY_AGAIN, "csrf_token" => $csrftoken);
             }
             echo json_encode( $resparr );
             die;
         }
         catch ( Exception $ex ) {
             $resparr = array ("code" => 201, 'msg' => $ex->getMessage());
         }

     }



//-----------------------------------------------------------------------------------------
     /**
      * @name getStatesByCountry
      * @description This method is used to get all the states name via country using the get method.
      * @access public
      */
     public function getStatesByCountry() {
         try {
             if ( $this->input->is_ajax_request() ) {
                 $req       = $this->input->get();
                 $statedata = $this->Common_model->fetch_data( 'states', 'id,name', ['where' => ['country_id' => $req['id']]] );
                 echo json_encode( $statedata );
                 exit;
             }
         }
         catch ( Exception $e ) {
             echo json_encode( $e->getTraceAsString() );
         }

     }



//-----------------------------------------------------------------------------------------
     /**
      * @name getCityByState
      * @description This method is used to get all the cities as per the state using get method.
      * @access public.
      */
     public function getCityByState() {
         try {
             if ( $this->input->is_ajax_request() ) {
                 $req      = $this->input->get();
                 $citydata = $this->Common_model->fetch_data( 'cities', 'id,name', ['where' => ['state_id' => $req['id']]] );
                 echo json_encode( $citydata );
                 exit;
             }
         }
         catch ( Exception $e ) {
             echo json_encode( $e->getTraceAsString() );
         }

     }



//-----------------------------------------------------------------------------------------
     /**
      * @name change-user-status
      * @description This action is used to handle all the block events.
      *
      */
     public function changeUserStatus() {
         try {

             $req = $this->input->post();
             /*
              * Check for permission on runtime
              */
             if ( $this->admininfo['role_id'] != 1 ) {
                 #$this->validatePermission( $req );
             }
             $id               = encryptDecrypt( $req['id'], 'decrypt' );
             /*
              * Set alert message according to condition
              */
             $alertMsg         = [];
             $alertMsg['text'] = $this->lang->line( 'delete_success' );
             $alertMsg['type'] = $this->lang->line( 'success' );
             switch ( $req['type'] ) {
                 case 'user':
                     $updateArr = [];
                     $updateArr = ['status' => $req['new_status']];
                     $updateId  = $this->Common_model->update_single( 'ai_user', $updateArr, ['where' => ['user_id' => $id]] );
                     if ( $req['new_status'] == 2 || $req['new_status'] == 3 ) {
                         $updateId = $this->Common_model->update_single( 'ai_session', ['login_status' => 0], ['where' => ['user_id' => $id]] );
                     }
                     if ( $req['new_status'] == DELETED ) {
                         $updateArr['field'] = 'email';
                         $updateArr['value'] = 'CONCAT(email,"-","deleted")';
                         $updateId           = $this->Common_model->update_single_withcurrent( 'ai_user', $updateArr, ['where' => ['user_id' => $id]] );
                     }
                     break;
                 case 'cms':
                     $whereArr          = [];
                     $whereArr['where'] = ['id' => $id];
                     $updateId          = $this->Common_model->delete_data( 'page_master', $whereArr );
                     break;
                 case 'version':
                     $whereArr          = [];
                     $whereArr['where'] = ['vid' => $id];
                     $updateId          = $this->Common_model->delete_data( 'app_version', $whereArr );
                     break;
                 case 'notification':
                     $whereArr          = [];
                     $whereArr['where'] = ['id' => $id];
                     $updateId          = $this->Common_model->delete_data( 'admin_notification', $whereArr );
                     break;
                 case 'feedback':
                     $updateId          = $this->Common_model->update_single( 'student_feedback', ['status' => $req['new_status']], ['where' => ['feedback_id' => $id]] );
                     break;
                 case 'subscriptions':
                     $updateId          = $this->Common_model->update_single( 'ai_subscriptions', ['status' => $req['new_status']], ['where' => ['subscription_id' => $id]] );
                     break;
                 case 'user_subscriptions':
                     $updateId          = $this->Common_model->update_single( 'ai_user_subscriptions', ['status' => $req['new_status']], ['where' => ['id' => $id]] );
                     break;
                 case 'subadmin':
                     $updateArr         = [];
                     $updateArr         = ['status' => $req['new_status']];
                     $updateId          = $this->Common_model->update_single( 'admin', $updateArr, ['where' => ['admin_id' => $id]] );
                     $updateId          = $this->Common_model->update_single( 'sub_admin', $updateArr, ['where' => ['admin_id' => $id]] );
                     if ( $req['new_status'] == DELETED ) {
                         $updateArr['field'] = 'admin_email';
                         $updateArr['value'] = 'CONCAT(admin_email,"-","deleted")';
                         $updateId           = $this->Common_model->update_single_withcurrent( 'admin', $updateArr, ['where' => ['admin_id' => $id]] );
                     }
                     break;
             }

             $csrftoken = $this->security->get_csrf_hash();

             if ( $updateId ) {
                 if ( $req['new_status'] == 3 ) {
                     $this->session->set_flashdata( 'alertMsg', $alertMsg );
                 }

                 $resparr = array ("code" => 200, 'msg' => $this->lang->line( 'success' ), "csrf_token" => $csrftoken, 'id' => $id);
             }
             else {
                 $resparr = array ("code" => 201, 'msg' => $this->lang->line( 'try_again' ), "csrf_token" => $csrftoken, 'id' => $id);
             }
             echo json_encode( $resparr );
             exit;
         }
         catch ( Exception $e ) {
             echo json_encode( $e->getTraceAsString() );
         }

     }



//    public function sidebar_state() {
//        $adminInfo = $this->session->userdata("admininfo");
//        $postData = $this->input->post();
//        if ( !isset($postData["sidebar_state"]) || empty($postData["sidebar_state"]) ) {
//            $this->CommonModel->response([
//                "success" => false,
//                "message" => $this->lang->line("missing_parameter"),
//                "csrf_token" => $this->security->get_csrf_hash()
//            ]);
//        }
//        $state = "";
//        if ( $postData["sidebar_state"] == "expanded" ) {
//            $adminInfo["sidebar_state"] = "left-panel-show";
//            $this->session->set_userdata("admininfo", $adminInfo);
//            $state = "minimized";
//        } else if ( $postData["sidebar_state"] == "minimized" ) {
//            $adminInfo["sidebar_state"] = "";
//            $this->session->set_userdata("admininfo", $adminInfo);
//            $state = "expanded";
//        }
//
//        $this->CommonModel->response([
//            "success" => true,
//            "message" => $this->lang->line("success"),
//            "state" => $state,
//            "csrf_token" => $this->security->get_csrf_hash(),
//            "admin" => $this->session->userdata()
//        ]);
//    }

     public function manageSideBar() {
         $this->load->helper( 'cookie' );
         $action     = $this->input->post( 'action' );
         set_cookie( 'sideBar', $action, time() + 3600 );
         $csrf_token = $this->security->get_csrf_hash();
         $respArr    = [];
         $respArr    = ['code' => 200, 'msg' => 'req success', 'csrf' => $csrf_token];
         echo json_encode( $respArr );
         die;

     }



     public function ajax_post_login() {
         $postData = $this->input->post();
         print_r( $postData );
         die();

     }



 }
