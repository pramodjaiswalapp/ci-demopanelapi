<?php
 if ( !defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

 /**
  * @function validate_cookie
  * @description To validate cookie data, called each and every time when any class initiate using HOOK
  *
  * @param array $params Parameters passes from Hook to execute the function
  * @return NA
  */
 function validate_cookie( $params ) {

     $cookieName = $params[0];
     $tableName  = $params[1];

     //getting CI instance
     $CI = &get_instance();

     //Loading Common Model
     $CI->load->model( "CommonModel" );
     $CI->load->helper( 'file' );

     $sessionFields = [
         "admin_id",
         "admin_name",
         "admin_email",
         "admin_profile_pic",
         "admin_profile_thumb",
         "role_id"
     ];

     $dataFields = [
         "admin_id",
         "admin_name",
         "admin_email",
         "admin_profile_pic",
         "admin_profile_thumb",
         "role_id"
     ];

     $cookieCookieData = $CI->CommonModel->validateCookie( $cookieName, $tableName, $sessionFields, $dataFields );

     if ( $cookieCookieData ) {
         $CI->session->set_userdata( 'admininfo', $cookieCookieData );
     }

     if ( isset( $_SERVER["REQUEST_URI"] ) && preg_match( '/.*\/(api)\/.*/', $_SERVER["REQUEST_URI"] ) == TRUE ) {
         return;
     }

     if ( "Login" === $CI->router->fetch_class() || "SocialLogin" === $CI->router->fetch_class() || "Dashboard" === $CI->router->fetch_class() ) {
         return;
     }
     elseif ( "logout" !== strtolower( $CI->router->fetch_class() ) && "admin" !== strtolower( $CI->router->fetch_class() ) ) {
         if ( empty( $CI->session->userdata( 'admininfo' ) ) ) {
             redirect( base_url().'admin' );
             exit;
         }
     }
     $GLOBALS['permission'] = NULL;
     if ( "production" !== ENVIRONMENT ) {
         set_error_handler( "RccErrorHandler" );
     }

 }



 /**
  * This is a HOOK function to call each and every time to check Class and Function accessibility
  *
  * @function checkAccessPermission
  * @description This is a HOOK function to call each and every time to check Class and Function accessibility
  * => Get Class and method name from router
  * => Loading All permission from config file
  * => Match Calling function and calling Class name with permissions
  * => Find permission name is provided to user or not
  */
 function checkAccessPermission() {

     #IF API services are running than permission will not be checked
     if ( isset( $_SERVER["REQUEST_URI"] ) && preg_match( '/.*\/(api)\/.*/', $_SERVER["REQUEST_URI"] ) == TRUE ) {
         return;
     }

     if ( isset( $_SERVER["REQUEST_URI"] ) && preg_match( '/.*\/(api)\/.*/', $_SERVER["REQUEST_URI"] ) == TRUE ) {
         return;
     }
     $CI          = &get_instance();
     $byPassClass = array ("sociallogin", "login", "dashboard", "admin", "logout", "ajaxutil", "notfound", "admin_profile");
     if ( in_array( strtolower( $CI->router->fetch_class() ), $byPassClass ) ) {
         return;
     }

     #FETCHING USER PERMISSION ON EVERY HIT START
     $admininfo = $CI->session->userdata( 'admininfo' );

     $whereArr          = [];
     $whereArr['where'] = array ('admin_id' => $admininfo['admin_id']);
     $access_detail     = $CI->Common_model->fetch_data( 'admin', ['permission'], $whereArr, true );

     #FETCHING USER PERMISSION END
     if ( !empty( $access_detail['permission'] ) ) {
         $arr        = json_decode( $access_detail['permission'], true );
         $permission = $arr['permission'];
     }
     else {
         $permission = ["admin"];
     }


     $CI->config->load( 'ACL_config', TRUE );

     //Initiated CLASS
     $called_class = strtolower( $CI->router->fetch_class() );

     //Initiated Method
     $called_method = $CI->router->fetch_method();

     //Fetching Permission from Config File
     $acl_config = $CI->config->item( 'permission', 'ACL_config' );

     //Permission Name
     $permission_name = NULL;

     $method = array ();

     #getting all method
     foreach ( $acl_config as $key => $value ) {
         foreach ( $value as $access_key => $access_array ) {
             $method[strtolower( $access_array['class'] )][$access_key] = $access_array['method'];
         }
     }

     #exit;
     if ( 1 === count( $permission ) ) {//$permission is not array
         if ( "admin" === $permission[0] ) {//is user an Admin. IF yes, return true
             foreach ( $method as $per => $mth ) {
                 foreach ( $mth as $cls => $cls_value ) {
                     $$cls                                        = $cls;
                     $GLOBALS['permission'][$$cls]                = "style='visibility:visible'";
                     $GLOBALS['permission']['permissions'][$$cls] = true;
                 }
             }
             $GLOBALS['permission']["action"] = true;
             return true;
         }
     }//if end



     $permission_for_action = NULL;
     #Check required permission

     if ( isset( $method[$called_class] ) ) {
         $methodParm            = array_flip( $method[$called_class] );
         $permission_for_action = $methodParm[$called_method];
     }//IF END
     #echo $called_class;
     #exit;
     $all_css = [];
     ## Creating Class ##
     if ( isset( $method[$called_class] ) ) {
         $perarr = array_merge( $method[$called_class], $method['ajaxutil'] );
     }
     else {
         $perarr = $method['ajaxutil'];
     }
     $forPage = $acl_config[$called_class];

     foreach ( $perarr as $per => $mth ) {
         $all_css[]                                   = $per;
         $$per                                        = $per;
         $GLOBALS['permission'][$$per]                = "style='visibility:hidden'";
         $GLOBALS['permission']['permissions'][$$per] = FALSE;
     }


     ## Creating Class Ends ##

     $required                        = array_intersect( $all_css, $permission );
     $GLOBALS['permission']["action"] = FALSE;
     if ( $required ) {
         foreach ( $required as $tmp ) {
             $$tmp                                        = $tmp;
             $GLOBALS['permission'][$$tmp]                = "style='visibility:visibile'";
             $GLOBALS['permission']['permissions'][$$tmp] = TRUE;

             if ( isset( $forPage[$tmp] ) && $forPage[$tmp]['in_column'] ) {
                 $GLOBALS['permission']["action"] = TRUE;
             }
         }
     }

     if ( !in_array( $permission_for_action, $permission ) ) {
         redirect( base_url().'access-denied' );
     }
     else {
         $CI->session->unset_userdata( 'admin_permission' );
         return TRUE;
     }

 }



 /*
   |===================================================
  */

 /**
  * @function get_parameters
  * @description function will called by hook to convert encrypted query string data in decrypted GET array
  *
  * @reutrn NA
  */
 function get_parameters() {
     $CI = &get_instance();
     $CI->load->helper( 'url' );

     $get_array = $CI->input->get( "data" );
     getRequestParams( $get_array );

 }



 /**
  * @function user_authentication
  * @description function will be called by hook to authenticate user's access token hitting the API
  *
  * @return NA
  */
 function user_authentication() {
     $CI = &get_instance();
     $CI->load->helper( 'url' );

     /**
      * Check authentication if api is called
      */
     if ( isset( $_SERVER["REQUEST_URI"] ) && preg_match( '/.*\/(api)\/.*/', $_SERVER["REQUEST_URI"] ) == TRUE ) {

         /**
          * Classes to run Authentication
          */
         $authenticate_for = [
             "subscriptions",
             "chat",
             "changepassword",
             "events",
             "logout",
             "chat_details",
             "delete_list",
             "delete_message",
             "managecomments",
             "managefavorite",
             "managefollow",
             "mark_read",
             "managefriends",
             "managereviews",
             "send_message",
             "recent_message",
             "managefeeds"
         ];

         #checking authenticatoin
         if ( in_array( strtolower( $CI->router->fetch_class() ), $authenticate_for ) ) {

             // authenticate user
             $CI->load->library( 'Rcc_Controller' );
             $Rcc_Controller = new Rcc_Controller();
             $login_user     = $Rcc_Controller->authenticate_user();

             //Check if account is blocked
             if ( BLOCKED == $login_user['userinfo']['status'] ) {
                 $Rcc_Controller->response( array ('code' => ACCOUNT_BLOCKED, 'msg' => $CI->lang->line( 'account_blocked' ), 'result' => []) );
             }
             $GLOBALS['api_user_id'] = $login_user['userinfo']['user_id'];
             $GLOBALS['login_user']  = $login_user;
         }
     }

 }


