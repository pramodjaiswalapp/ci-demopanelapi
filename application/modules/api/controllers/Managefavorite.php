<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );
 require APPPATH.'/libraries/REST_Controller.php';

 class Managefavorite extends REST_Controller {

     function __construct() {
         parent::__construct();
         $this->load->model( 'Favorite_model' );

     }



     /**
      * @SWG\Post(path="/managefavorite",
      *   tags={"Favorite"},
      *   summary="Make a post favorite",
      *   description="Make a post favorite",
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
      *     description="User Id",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      *   @SWG\Response(response=507, description="Already favorite")
      * )
      */
     public function index_post() {
         try {

             #response array
             $response_array = [];

             #getting data from post array
             $postDataArr = $this->post();

             #form validation cofiguration
             $config = array (
                 array (
                     'field' => 'user_id',
                     'label' => 'User Id',
                     'rules' => 'required|numeric'
                 )
             );

             #setting form validation rules
             $this->form_validation->set_rules( $config );

             #Setting Error Messages for rules
             $this->form_validation->set_message( 'required', $this->lang->line( "msg_sfx" ).'%s' );

             #checking form is valid or not
             if ( $this->form_validation->run() ) {

                 $name    = $GLOBALS['login_user']['userinfo']['name'];
                 $user_id = $GLOBALS['api_user_id'];

                 #where condition
                 $whereArr          = [];
                 $whereArr['where'] = ['user_id' => $user_id, 'favorited_userid' => $postDataArr['user_id']];

                 #featching favroute data
                 $isRequestExist = $this->Common_model->fetch_data( 'ai_favorite', ['id'], $whereArr, true );

                 #if $isRequestExist having no value
                 if ( empty( $isRequestExist ) ) {

                     #db transaction start
                     $this->db->trans_begin();

                     #data to save in DB
                     $favoriteInsertArr = [
                         "user_id"          => $user_id,
                         "favorited_userid" => $postDataArr['user_id'],
                         "created_at"       => datetime()
                     ];

                     #saving data in DB
                     $isRequestSuccess = $this->Common_model->insert_single( 'ai_favorite', $favoriteInsertArr );

                     #if data not saved, throw Exceptions
                     if ( !$isRequestSuccess ) {
                         throw new Exception( $this->lang->line( 'try_again' ) );
                     }

                     #checking transaction
                     if ( $this->db->trans_status() ) {

                         #Commiting DB transaction
                         $this->db->trans_commit();

                         #Android payload
                         $androidPayload = [
                             "message" => "You profile has been set to favorite by ".$name,
                             "user_id" => $postDataArr['user_id'],
                             "type"    => FAVORITE_PUSH,
                             "time"    => time()
                         ];

                         #iOS Payload
                         $iosPayload = [
                             "alert" => array ('title' => "You profile has been set to favorite by ".$name, 'user_id' => $postDataArr['user_id']),
                             "badge" => 0,
                             "type"  => FAVORITE_PUSH,
                             "sound" => 'beep.mp3'
                         ];


                         #Push notification data
                         $pushData = [
                             "receiver_id"    => $postDataArr['user_id'],
                             "androidPayload" => $androidPayload,
                             "iosPayload"     => $iosPayload
                         ];

                         #loading Common Function library
                         $this->load->library( 'commonfn' );

                         #sending Push notification
                         $this->commonfn->sendPush( $pushData );


                         #setting response
                         $response_array = [
                             'code'   => SUCCESS_CODE,
                             'msg'    => $this->lang->line( 'favorite_success' ),
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
                 }#$isRequestExist if END
                 else {

                     #setting response
                     $response_array = [
                         'code'   => ALREADY_FAVORITE,
                         'msg'    => $this->lang->line( 'already_favorite' ),
                         'result' => []
                     ];
                 }#else END
             }#form validation if END
             else {#if form validation Error fails
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



     /**
      * @SWG\Get(path="/managefavorite",
      *   tags={"Favorite"},
      *   summary="View the Favorite of a post",
      *   description="View the Favorite of a post",
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
      *     name="user_id",
      *     in="query",
      *     description="User Id",
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="page",
      *     in="query",
      *     description="page no.",
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="searchlike",
      *     in="query",
      *     description="Search key parameter",
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="req_type",
      *     in="query",
      *     description="request type 1 for getting list of users",
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

             #getting data from get
             $getDataArr    = $this->input->get();
             $default_array = [
                 "page"       => 1,
                 "req_type"   => "",
                 "searchlike" => "",
                 "limit"      => LIMIT
             ];

             $params = defaultValue( $getDataArr, $default_array );

             $page              = $params['page'];
             $params['user_id'] = $GLOBALS['api_user_id'];
             $params['offset']  = ($page - 1) * $params['limit'];

             #request type 1 for getting list of users, else fav list
             if ( 1 == $params['req_type'] ) {
                 $params['userlist_type'] = 3;
                 $usersList               = $this->Common_model->getUserList( $params );
                 $msg                     = $this->lang->line( 'user_list_fetched' );
             }#if end
             else {
                 $usersList = $this->Favorite_model->getFavorites( $params );
                 $msg       = $this->lang->line( 'favorite_list_fetched' );
             }#else end


             if ( ($usersList['count'] > ($page * $params['limit']) ) ) {#checking if total are more than curent page count
                 $page++;
             }
             else {
                 $page = 0;
             }


             if ( !empty( $usersList['result'] ) ) {

                 #setting response
                 $response_array = [
                     'code'       => SUCCESS_CODE,
                     'msg'        => $msg,
                     'next_page'  => $page,
                     'total_rows' => $usersList['count'],
                     'result'     => $usersList['result']
                 ];
             }
             else {

                 #setting response
                 $response_array = [
                     'code'   => NO_DATA_FOUND,
                     'msg'    => $this->lang->line( 'no_users_found' ),
                     'result' => []
                 ];
             }

             #sending final response
             $this->response( $response_array );
         }
         catch ( Exception $e ) {
             $error = $e->getMessage();
             list($msg, $code) = explode( " || ", $error );

             #setting error response
             $response_array = [
                 'code'   => $code,
                 'msg'    => $msg,
                 'result' => []
             ];

             #sending error response
             $this->response( $response_array );
         }

     }



     /**
      * @SWG\Delete(path="/managefavorite",
      *   tags={"Comments"},
      *   summary="Unfavorite the post",
      *   description="Unfavorite the post",
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
      *     description="Comment Id of Comment which we want to delete",
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

             #getting data
             $deleteDataArr = $this->delete();

             #setting form validation
             $config = array (
                 array (
                     'field' => 'user_id',
                     'label' => 'User Id',
                     'rules' => 'required'
                 )
             );

             $set_data = array (
                 'user_id' => $this->delete( 'user_id' )
             );

             #setting form data
             $this->form_validation->set_data( $set_data );

             #setting validation rule
             $this->form_validation->set_rules( $config );


             #Setting Error Messages for rules
             $this->form_validation->set_message( 'required', 'Please enter the %s' );

             #running form validation
             if ( $this->form_validation->run() ) {

                 #db transaction start
                 $this->db->trans_begin();
                 $whereArr          = [];
                 $whereArr['where'] = ['favorited_userid' => $deleteDataArr['user_id'], 'user_id' => $GLOBALS['api_user_id']];

                 #deleting favorite
                 $isDeleteSuccess = $this->Common_model->delete_data( 'ai_favorite', $whereArr );

                 #if not deleted successfuly
                 if ( !$isDeleteSuccess ) {
                     throw new Exception( $this->lang->line( 'try_again' ) );
                 }


                 #checking transaction status
                 if ( $this->db->trans_status() ) {

                     #commiting DB transaction
                     $this->db->trans_commit();

                     #setting response
                     $response_array = [
                         'code'   => SUCCESS_CODE,
                         'msg'    => $this->lang->line( 'unfavorite_success' ),
                         'result' => []
                     ];

                     #sending response
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
             }#form validaion IF end
             else {
                 $err = $this->form_validation->error_array();
                 $arr = array_values( $err );

                 $response_array = [
                     'code'   => PARAM_REQ,
                     'msg'    => $arr[0],
                     'result' => []
                 ];
             }#else end
             #sending final Response
             $this->response( $response_array );
         }#try end
         catch ( Exception $e ) {#catch Start
             $this->db->trans_rollback();
             $error = $e->getMessage();

             $response_array = [
                 'code'   => TRY_AGAIN_CODE,
                 'msg'    => $error,
                 'result' => []
             ];

             $this->response( $response_array );
         }#catch End

     }



 }
