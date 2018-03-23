<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );
 require APPPATH.'/libraries/REST_Controller.php';

 class Managefriends extends REST_Controller {

     function __construct() {
         parent::__construct();
         $this->load->library( 'commonfn' );

     }



     /**
      * @SWG\Post(path="/Managefriends",
      *   tags={"Friends"},
      *   summary="Send friend request",
      *   description="Send friend request",
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
      *     name="receiver_id",
      *     in="formData",
      *     description="Receiver Id of user who will receive request",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Response(response=200, description="Request sent"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      *   @SWG\Response(response=500, description="Request already sent by user"),
      *   @SWG\Response(response=501, description="Request already received by user"),
      * )
      */
     public function index_post() {

         #try Start
         try {
             #response array
             $response_array = [];

             $postDataArr = $this->post();

             #setting form validation rule array
             $config = array (
                 array (
                     'field' => 'receiver_id',
                     'label' => 'Receiver Id',
                     'rules' => 'required'
                 ),
             );

             #setting Rule
             $this->form_validation->set_rules( $config );

             # Setting Error Messages for rules
             $this->form_validation->set_message( 'required', 'Please enter the %s' );

             #checking form validation
             if ( $this->form_validation->run() ) {

                 $user_id = $GLOBALS['api_user_id'];
                 $name    = $GLOBALS['login_user']['userinfo']['name'];

                 $whereArr                = [];
                 $whereArr['customWhere'] = "((sender_id=".$user_id." AND receiver_id=".$postDataArr['receiver_id'].") OR (sender_id=".$postDataArr['receiver_id']." AND receiver_id=".$user_id."))";
                 $isRequestExist          = $this->Common_model->fetch_data( 'ai_friend_request', array ('sender_id', 'receiver_id'), $whereArr, true );

                 if ( !empty( $isRequestExist ) ) {
                     if ( $isRequestExist['sender_id'] == $user_id ) {

                         $response_array = [
                             'code'   => REQUEST_ALREADY_SENT,
                             'msg'    => $this->lang->line( 'req_already_sent' ),
                             'result' => []
                         ];
                     }
                     else {

                         $response_array = [
                             'code'   => REQUEST_ALREADY_RECEIVED,
                             'msg'    => $this->lang->line( 'req_already_recieved' ),
                             'result' => []
                         ];
                     }
                 }
                 else {

                     #db transaction start
                     $this->db->trans_begin();

                     #Request Array to insert
                     $reqInsertArr = [
                         "sender_id"   => $user_id,
                         "receiver_id" => $postDataArr['receiver_id'],
                         "created"     => datetime(),
                         "updated"     => datetime()
                     ];

                     #saving friend request inDB
                     $isRequestSent = $this->Common_model->insert_single( 'ai_friend_request', $reqInsertArr );

                     if ( $isRequestSent ) {

                         #Create Android Payload
                         $androidPayload = [
                             "message" => "You have received friend request from ".$name,
                             "user_id" => $postDataArr['receiver_id'],
                             "type"    => REQUEST_PUSH,
                             "time"    => time()
                         ];

                         #Create Ios Payload
                         $iosPayload = [
                             "alert" => array ('title' => "You have received friend request from ".$name, 'user_id' => $postDataArr['receiver_id']),
                             "badge" => 0,
                             "type"  => REQUEST_PUSH,
                             "sound" => PUSH_SOUND
                         ];

                         #push data array
                         $pushData = [
                             "receiver_id"    => $postDataArr['receiver_id'],
                             "androidPayload" => $androidPayload,
                             "iosPayload"     => $iosPayload
                         ];


                         #loading common function
                         $this->load->library( 'commonfn' );

                         #sending push notification
                         $this->commonfn->sendPush( $pushData );


                         #checking DB transaction
                         if ( $this->db->trans_status() ) {
                             #commiting DB transaction
                             $this->db->trans_commit();
                             $this->response( array ('code' => SUCCESS_CODE, 'msg' => $this->lang->line( 'request_sent' ), 'result' => []) );
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
                 $this->response( $response_array );
             }
             else {
                 $err = $this->form_validation->error_array();
                 $arr = array_values( $err );

                 $response_array = [
                     'code'   => PARAM_REQ,
                     'msg'    => $arr[0],
                     'result' => []
                 ];
                 $this->response( $response_array );
             }
         }#try end
         catch ( Exception $e ) {

             $this->db->trans_rollback();

             $error          = $e->getMessage();
             $response_array = [
                 'code'   => TRY_AGAIN_CODE,
                 'msg'    => $error,
                 'result' => []
             ];
             $this->response( $response_array );
         }#catch End

     }



     /**
      * @SWG\Get(path="/Managefriends",
      *   tags={"Friends"},
      *   summary="Manage Request and list",
      *   description="Manage friend list,pending request,sent pending request,all users list",
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
      *     name="req_type",
      *     in="query",
      *     description="Req type 1 pending request,2 sent pending request,3 get all users list and empty for getting friends list",
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
             #response array
             $response_array = [];

             $getDataArr = $this->input->get();

             #seting default values
             $default_array = [
                 "page"       => 1,
                 "req_type"   => "",
                 "searchlike" => "",
                 "limit"      => FRIEND_LIMIT
             ];
             $params        = defaultValue( $getDataArr, $default_array );


             $page              = $params['page'];
             $params['user_id'] = $GLOBALS['api_user_id'];
             $offset            = ($page - 1) * $params['limit'];
             $params['offset']  = $offset;
             $req_type          = $params['req_type'];


             if ( $req_type != 3 ) {
                 $pendingRequests = $this->Common_model->getRequest( $params );
             }
             else {
                 $pendingRequests = $this->Common_model->getUserList( $params );
             }

             /*
              * fetching recieved pending requests
              */
             if ( $req_type == 1 ) {
                 $success_msg = $this->lang->line( 'pending_request_fetched' );
                 $error_msg   = $this->lang->line( 'no_request_found' );
             }
             /*
              * fetching sent pending requests
              */
             else if ( $req_type == 2 ) {
                 $success_msg = $this->lang->line( 'sent_pending_request_fetched' );
                 $error_msg   = $this->lang->line( 'no_request_found' );
             }
             /*
              * fetching user lists
              */
             else if ( $req_type == 3 ) {
                 $success_msg = $this->lang->line( 'user_list_fetched' );
                 $error_msg   = $this->lang->line( 'no_users_found' );
             }
             /*
              * fetching users list
              */
             else {
                 $error_msg   = $this->lang->line( 'no_friends_found' );
                 $success_msg = $this->lang->line( 'friends_list_fetched' );
             }


             #checking total count is more than current page count
             if ( ($pendingRequests['count'] > ($page * $params['limit']) ) ) {
                 $page++;
             }
             else {
                 $page = 0;
             }

             if ( !empty( $pendingRequests['result'] ) ) {
                 $response_array = [
                     'code'       => SUCCESS_CODE,
                     'msg'        => $success_msg,
                     'total_rows' => $pendingRequests['count'],
                     'next_page'  => $page,
                     'result'     => $pendingRequests['result']
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
             $error = $e->getMessage();
             list($msg, $code) = explode( " || ", $error );
             $this->response( array ('code' => $code, 'msg' => $msg, 'result' => []) );
         }

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
      *     in="query",
      *     description="Access token received during signup or login",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="sender_id",
      *     in="formData",
      *     description="Sender Id of user who will has send the request",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="status",
      *     in="formData",
      *     description="New status of request 1",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      * )
      */
     public function index_put() {
         try {
             $putDataArr = $this->put();

             #setting form validation rule array
             $config = array (
                 array (
                     'field' => 'sender_id',
                     'label' => 'Sender Id',
                     'rules' => 'required'
                 )
             );

             #setting form data array
             $set_data = array (
                 'sender_id' => $this->put( 'sender_id' )
             );

             #setting Form data
             $this->form_validation->set_data( $set_data );

             #setting Rule
             $this->form_validation->set_rules( $config );

             # Setting Error Messages for rules
             $this->form_validation->set_message( 'required', 'Please enter the %s' );

             #checking form validation
             if ( $this->form_validation->run() ) {

                 $name                    = $GLOBALS['login_user']['userinfo']['name'];
                 $whereArr                = [];
                 $whereArr['customWhere'] = "(sender_id = ".$putDataArr['sender_id']." AND receiver_id = ".$GLOBALS['api_user_id'].")";
                 $updateArr               = [];
                 $updateArr['status']     = 1;

                 #db transaction start
                 $this->db->trans_begin();

                 $isRequestSuccess = $this->Common_model->update_single( 'ai_friend_request', $updateArr, $whereArr );

                 if ( $isRequestSuccess ) {
                     /*
                      * Send Request Push
                      */

                     #Create Android Payload
                     $androidPayload = [
                         "message" => $name." has accepted your friend request",
                         "user_id" => $putDataArr['sender_id'],
                         "type"    => REQUEST_ACCEPT_PUSH,
                         "time"    => time()
                     ];



                     #Create Ios Payload
                     $iosPayload = [
                         "alert" => array ('title' => $name." has accepted your friend request", 'user_id' => $putDataArr['sender_id']),
                         "badge" => 0,
                         "type"  => REQUEST_ACCEPT_PUSH,
                         "sound" => PUSH_SOUND
                     ];


                     #push data array
                     $pushData = [
                         "receiver_id"    => $putDataArr['sender_id'],
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

                         #setting response
                         $response_array = [
                             'code'   => SUCCESS_CODE,
                             'msg'    => $this->lang->line( 'req_accepted' ),
                             'result' => []
                         ];
                     }
                 }
                 else {
                     throw new Exception( $this->lang->line( 'try_again' ) );
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

             #sending final response
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
         }#catch End

     }



     /**
      * @SWG\Delete(path="/Managefriends",
      *   tags={"Friends"},
      *   summary="Manage friend request",
      *   description="Reject received or sent request",
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
      *     description="Sender Id of user who send request",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="req type",
      *     in="formData",
      *     description="Req type 1 if wants to reject the request 2 if wants to cancel sent pending request",
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
             #response array
             $response_array = [];

             $deleteDataArr = $this->delete();

             #form validation cofiguration
             $config = array (
                 array (
                     'field' => 'req_type',
                     'label' => 'Req type',
                     'rules' => 'required'
                 ),
                 array (
                     'field' => 'user_id',
                     'label' => 'User Id',
                     'rules' => 'required'
                 )
             );

             $set_data = array (
                 'user_id'  => $this->delete( 'user_id' ),
                 'req_type' => $this->delete( 'req_type' ),
             );

             $this->form_validation->set_data( $set_data );

             #setting form validation rules
             $this->form_validation->set_rules( $config );

             #Setting Error Messages for rules
             $this->form_validation->set_message( 'required', $this->lang->line( "msg_sfx" ).'%s' );

             #checking form is valid or not
             if ( $this->form_validation->run() ) {

                 /*
                  * Req type 1 pending request,2 sent pending request,3 get all users list and empty for getting friends list
                  */
                 $req_type = isset( $deleteDataArr['req_type'] ) ? $deleteDataArr['req_type'] : "";

                 $whereArr = [];
                 if ( $req_type == 1 ) {
                     $whereArr['customWhere'] = "(sender_id = ".$deleteDataArr['user_id']." AND receiver_id = ".$GLOBALS['api_user_id'].")";
                 }
                 else if ( $req_type == 2 ) {
                     $whereArr['customWhere'] = "(sender_id = ".$GLOBALS['api_user_id']." AND receiver_id = ".$deleteDataArr['user_id'].")";
                 }
                 else if ( $req_type == 3 ) {
                     $whereArr['customWhere'] = "((sender_id=".$GLOBALS['api_user_id']." AND receiver_id=".$deleteDataArr['user_id'].") OR (sender_id=".$deleteDataArr['user_id']." AND receiver_id=".$deleteDataArr['user_id']."))";
                 }

                 #db transaction start
                 $this->db->trans_begin();
                 $isRequestSuccess = $this->Common_model->delete_data( 'ai_friend_request', $whereArr );


                 if ( !$isRequestSuccess ) {
                     throw new Exception( $this->lang->line( 'try_again' ) );
                 }

                 #checking transaction
                 if ( $this->db->trans_status() ) {

                     #Commiting DB transaction
                     $this->db->trans_commit();

                     $response_array = [
                         'code'   => SUCCESS_CODE,
                         'msg'    => $this->lang->line( 'req_rejected' ),
                         'result' => []
                     ];

                     $this->response( $response_array );
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
                 $err            = $this->form_validation->error_array();
                 $arr            = array_values( $err );
                 $response_array = [
                     'code'   => PARAM_REQ,
                     'msg'    => $arr[0],
                     'result' => []
                 ];
             }

             $this->response( $response_array );
         }#try end
         catch ( Exception $e ) {
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
         }#catch END

     }



 }
