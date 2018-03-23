<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );
 require APPPATH.'/libraries/REST_Controller.php';

 class Logout extends REST_Controller {

     function __construct() {
         parent::__construct();

     }



     /**
      * @SWG\Put(path="/Managefriends",
      *   tags={"Friends"},
      *   summary="Manage friend request",
      *   description="Manage friend request",
      *   operationId="index_put",
      *   consumes ={"multipart/form-data"},
      *   produces={"application/json"},
      *   @SWG\Parameter(
      *     name="Authorization",
      *     in="header",
      *     description="",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="accesstoken",
      *     in="header",
      *     description="Access token received during signup or login",
      *     required=true,
      *     type="string"
      *   )
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      * )
      */
     public function index_put() {
         try {

             $response_array = [];
             $putDataArr     = $this->put();
             $accessToken    = $GLOBALS['login_user']['Accesstoken'];
             $accessTokenArr = explode( "||", $accessToken );

             #if count is equal to 2
             if ( 2 === count( $accessTokenArr ) ) {
                 $whereArr          = [];
                 $whereArr['where'] = ['public_key' => $accessTokenArr[0], 'private_key' => $accessTokenArr[1]];

                 #$isSuccess = $this->Common_model->update_single('ai_session', ['login_status' => 0], $whereArr);
                 #Delete Session details from DB
                 $isSuccess = $this->Common_model->delete_data( 'ai_session', $whereArr );

                 if ( $isSuccess ) { #if Deleted successfuly
                     #setting Response
                     $response_array = [
                         'code'   => SUCCESS_CODE,
                         'msg'    => $this->lang->line( 'logout_successful' ),
                         'result' => []
                     ];
                 }#if END
                 else {

                     #setting Response
                     $response_array = [
                         'code'   => TRY_AGAIN_CODE,
                         'msg'    => $this->lang->line( 'try_again' ),
                         'result' => []
                     ];
                 }#else END
             }#IF END
             else {#Else IF access token is not in Valid form
                 #setting Response
                 $response_array = [
                     'code'   => PARAM_REQ,
                     'msg'    => $this->lang->line( 'try_again' ),
                     'result' => []
                 ];
             }#else end
             #sending Response
             $this->response( $response_array );
         }#try end
         catch ( Exception $e ) {#catch start
             $error = $e->getMessage();
             list($msg, $code) = explode( " || ", $error );

             #setting response
             $response_array = [
                 'code'   => $code,
                 'msg'    => $msg,
                 'result' => []
             ];

             #sending response
             $this->response( $response_array );
         }#catch end

     }



 }
