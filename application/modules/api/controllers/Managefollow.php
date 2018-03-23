<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );
 require APPPATH.'/libraries/REST_Controller.php';

 class Managefollow extends REST_Controller {

     function __construct() {
         parent::__construct();
         $this->load->model( 'Follow_model' );

     }



     /**
      * @SWG\Post(path="/Managefollow",
      *   tags={"Follow"},
      *   summary="Follow a user",
      *   description="Follow a user",
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
      *  @SWG\Parameter(
      *     name="accesstoken",
      *     in="query",
      *     description="Access token received during signup or login",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="user_id",
      *     in="formData",
      *     description="Receiver Id",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      *   @SWG\Response(response=506, description="You have already followed that user")
      * )
      */
     public function index_post() {
         try {
             $response_array = [];
             $postDataArr    = $this->post();
             $config         = [];

             $config = array (
                 array (
                     'field' => 'user_id',
                     'label' => 'Receiver Id',
                     'rules' => 'required|numeric'
                 )
             );

             $this->form_validation->set_rules( $config );
             /*
              * Setting Error Messages for rules
              */
             $this->form_validation->set_message( 'required', 'Please enter the %s' );

             if ( $this->form_validation->run() ) {

                 $name = $GLOBALS['login_user']['userinfo']['name'];

                 $whereArr          = [];
                 $whereArr['where'] = ['sender_id' => $GLOBALS['api_user_id'], 'receiver_id' => $postDataArr['user_id']];

                 #checiking already following
                 $isRequestExist = $this->Common_model->fetch_data( 'ai_follows', [], $whereArr, true );

                 #if not following
                 if ( empty( $isRequestExist ) ) {


                     #Follow request data
                     $followInsertArr = [
                         "sender_id"   => $GLOBALS['api_user_id'],
                         "receiver_id" => $postDataArr['user_id'],
                         "created"     => datetime(),
                         "updated"     => datetime()
                     ];

                     #db transaction start
                     $this->db->trans_begin();

                     #saving following request inDB
                     $isRequestSuccess = $this->Common_model->insert_single( 'ai_follows', $followInsertArr );

                     #if saved successfully
                     if ( $isRequestSuccess ) {

                         #Create Android Payload
                         $androidPayload = [
                             "message" => $name." started following you",
                             "user_id" => $postDataArr['user_id'],
                             "type"    => FOLLOW_PUSH,
                             "time"    => time()
                         ];

                         #Create Ios Payload
                         $iosPayload = [
                             "alert" => array ('title' => $name." started following you", 'user_id' => $postDataArr['user_id']),
                             "badge" => 0,
                             "type"  => FOLLOW_PUSH,
                             "sound" => "beep.mp3"
                         ];

                         #Push data
                         $pushData = [
                             "receiver_id"    => $postDataArr['user_id'],
                             "androidPayload" => $androidPayload,
                             "iosPayload"     => $iosPayload
                         ];


                         #loading common function library
                         $this->load->library( 'commonfn' );

                         #sending push notiofication
                         $this->commonfn->sendPush( $pushData );

                         #checking DB transaction
                         if ( $this->db->trans_status() ) {

                             #commiting DB transaction
                             $this->db->trans_commit();

                             $response_array = [
                                 'code'   => SUCCESS_CODE,
                                 'msg'    => $this->lang->line( 'follow_success' ),
                                 'result' => []
                             ];
                         }#if end
                         else {#IF transaction failed
                             #rolling back
                             $this->db->trans_rollback();

                             #setting Response Array
                             $response_array = [
                                 'CODE'    => TRY_AGAIN_CODE,
                                 'MESSAGE' => $this->lang->line( 'try_again' ),
                                 'RESULT'  => []
                             ];
                         }#else transation end
                     }
                     else {
                         throw new Exception( $this->lang->line( 'try_again' ) );
                     }
                 }
                 else {
                     $response_array = [
                         'code'   => ALREADY_FOLLOWING,
                         'msg'    => $this->lang->line( 'already_following' ),
                         'result' => []
                     ];
                 }
             }#form validation if end
             else {
                 $err            = $this->form_validation->error_array();
                 $arr            = array_values( $err );
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

             #settig error reposnse
             $response_array = [
                 'code'   => TRY_AGAIN_CODE,
                 'msg'    => $error,
                 'result' => []
             ];

             #sending Error response
             $this->response( $response_array );
         }

     }



     /**
      * @SWG\Get(path="/Managefollow",
      *   tags={"Follow-Following"},
      *   summary="View the lists",
      *   description="View the follow,following and user list",
      *   operationId="index_get",
      *   produces={"application/json"},
      *   @SWG\Parameter(
      *     name="Authorization",
      *     in="header",
      *     description="",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="accesstoken",
      *     in="query",
      *     description="Access token received during signup or login",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="page",
      *     in="query",
      *     description="page no.",
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="req_type",
      *     in="query",
      *     description="Req type 1 for getting followers list,2 for getting following list and 3 for getting users list",
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="searchlike",
      *     in="query",
      *     description="Send search string on this key",
      *     type="string"
      *   ),
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=201, description="Please try again"),
      *   @SWG\Response(response=202, description="No data found"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      * )
      */
     public function index_get() {
         try {

             #response arraay
             $response_array = [];

             $getDataArr = $this->input->get();

             $default_array = [
                 "page"       => 1,
                 "req_type"   => "",
                 "searchlike" => "",
                 "user_id"    => $GLOBALS['api_user_id'],
                 "limit"      => LIMIT
             ];
             $params        = defaultValue( $getDataArr, $default_array );


             $page             = $params['page'];
             $offset           = ($page - 1) * $params['limit'];
             $params['offset'] = $offset;
             $req_type         = $params['req_type'];


             if ( $req_type != 3 ) {
                 $userList = $this->Follow_model->getList( $params );
             }
             else {
                 $params['userlist_type'] = 2;
                 $userList                = $this->Common_model->getUserList( $params );
             }

             /*
              * fetching followers list
              */
             if ( $req_type == 1 ) {
                 $success_msg = $this->lang->line( 'followers_list_fetched' );
                 $error_msg   = $this->lang->line( 'no_followers' );
             }
             /*
              * fetching sent pending requests
              */
             else if ( $req_type == 2 ) {
                 $success_msg = $this->lang->line( 'followings_list_fetched' );
                 $error_msg   = $this->lang->line( 'no_followings' );
             }
             /*
              * fetching user lists
              */
             else if ( $req_type == 3 ) {
                 $success_msg = $this->lang->line( 'user_list_fetched' );
                 $error_msg   = $this->lang->line( 'no_users_found' );
             }



             if ( ($userList['count'] > ($page * $params['limit']) ) ) {
                 $page++;
             }
             else {
                 $page = 0;
             }


             if ( !empty( $userList['result'] ) ) {
                 $response_array = [
                     'code'       => SUCCESS_CODE,
                     'msg'        => $success_msg,
                     'total_rows' => $userList['count'],
                     'next_page'  => $page,
                     'result'     => $userList['result']
                 ];
             }
             else {
                 $response_array = [
                     'code'   => NO_DATA_FOUND,
                     'msg'    => $error_msg,
                     'result' => []
                 ];
             }

             $this->response( $response_array );
         }
         catch ( Exception $e ) {
             $error          = $e->getMessage();
             list($msg, $code) = explode( " || ", $error );
             $response_array = [
                 'code'   => $code,
                 'msg'    => $msg,
                 'result' => []
             ];

             $this->response( $response_array );
         }

     }



     /**
      * @SWG\Delete(path="/Managefollow",
      *   tags={"Follow-Following"},
      *   summary="Unfollow",
      *   description="Unfollow users",
      *   operationId="index_delete",
      *   consumes ={"multipart/form-data"},
      *   produces={"application/json"},
      *   @SWG\Parameter(
      *     name="Authorization",
      *     in="header",
      *     description="",
      *     required=true,
      *     type="string"
      *   ),
      *     @SWG\Parameter(
      *     name="accesstoken",
      *     in="query",
      *     description="Access token received during signup or login",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="user_id",
      *     in="formData",
      *     description="user Id of user whom we want to unfollow",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      * )
      */
     public function index_delete() {
         try {
             $deleteDataArr = $this->delete();

             #form validation rules array
             $config = array (
                 array (
                     'field' => 'user_id',
                     'label' => 'User id',
                     'rules' => 'required'
                 )
             );

             #form data array
             $set_data = array (
                 'user_id' => $this->delete( 'user_id' )
             );

             #setting form data
             $this->form_validation->set_data( $set_data );

             #setting form validation
             $this->form_validation->set_rules( $config );

             #Setting Error Messages for rules
             $this->form_validation->set_message( 'required', 'Please enter the %s' );


             #checking form validation
             if ( $this->form_validation->run() ) {

                 #where condition
                 $whereArr          = [];
                 $whereArr['where'] = ['sender_id' => $GLOBALS['api_user_id'], 'receiver_id' => $deleteDataArr['user_id']];

                 #DB transaction start
                 $this->db->trans_begin();

                 #delete follow data from Db
                 $isDeleteSuccess = $this->Common_model->delete_data( 'ai_follows', $whereArr );

                 #if deleted unsuccessfully, throw exception
                 if ( !$isDeleteSuccess ) {
                     throw new Exception( $this->lang->line( 'try_again' ) );
                 }

                 #checking DB transaction status
                 if ( $this->db->trans_status() ) {

                     #db transaction commit
                     $this->db->trans_commit();

                     #setting response
                     $response_array = [
                         'code'   => SUCCESS_CODE,
                         'msg'    => $this->lang->line( 'unfollow_success' ),
                         'result' => []
                     ];
                 }#if end
                 else {#IF transaction failed
                     #rolling back
                     $this->db->trans_rollback();

                     #setting Response Array
                     $response_array = [
                         'CODE'    => TRY_AGAIN_CODE,
                         'MESSAGE' => $this->lang->line( 'try_again' ),
                         'RESULT'  => []
                     ];
                 }#else transation end
             }#end if form validation
             else {#else form validation
                 $err            = $this->form_validation->error_array();
                 $arr            = array_values( $err );
                 $response_array = [
                     'code'   => PARAM_REQ,
                     'msg'    => $arr[0],
                     'result' => []
                 ];
             }

             #sending final response
             $this->response( $response_array );
         }#try end
         catch ( Exception $e ) {
             $this->db->trans_rollback();
             $error = $e->getMessage();

             #setting error response
             $response_array = [
                 'code'   => TRY_AGAIN_CODE,
                 'msg'    => $error,
                 'result' => []
             ];

             #send error response
             $this->response( $response_array );
         }#catch end

     }



 }
