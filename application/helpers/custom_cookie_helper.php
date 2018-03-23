<?php
 if ( !defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );


 if ( !function_exists( "validate_admin_cookie" ) ) {

     function validate_admin_cookie( $cookieName, $tableName ) {
         $CI            = &get_instance();
         $CI->load->model( "CommonModel" );
         $sessionFields = [
             "admin_id",
             "admin_name",
             "admin_email",
             "admin_profile_pic",
             "admin_profile_thumb",
             "role_id"
         ];
         $dataFields    = [
             "admin_id",
             "admin_name",
             "admin_email",
             "admin_profile_pic",
             "admin_profile_thumb",
             "role_id"
         ];

         $cookieCookieData = $CI->CommonModel->validateCookie( $cookieName, $tableName, $sessionFields, $dataFields );

         return $cookieCookieData;

     }



 }

 if ( !function_exists( "validate_user_cookie" ) ) {

     function validate_user_cookie( $cookieName, $tableName ) {
         $CI            = &get_instance();
         $CI->load->model( "CommonModel" );
         $sessionFields = [
             "id",
             "name",
             "email"
         ];
         $dataFields    = [
             "user_id",
             "first_name",
             "email"
         ];

         $cookieCookieData = $CI->CommonModel->validateCookie( $cookieName, $tableName, $sessionFields, $dataFields, 'registered_date' );

         return $cookieCookieData;

     }



 }