<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );
 require APPPATH.'/libraries/REST_Controller.php';

 class Managecomments extends REST_Controller {

     function __construct() {
         parent::__construct();
         $this->load->model( 'Comment_model' );

     }



     /**
      * @SWG\Post(path="/managecomments",
      *   tags={"Comments"},
      *   summary="Give comment for a post",
      *   description="Give comment for a post",
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
      *     name="post_id",
      *     in="formData",
      *     description="Post Id",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="comment",
      *     in="formData",
      *     description="Comment",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      *   @SWG\Response(response=505, description="Review already exists")
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
                     'field' => 'post_id',
                     'label' => 'Post Id',
                     'rules' => 'required|numeric'
                 ),
                 array (
                     'field' => 'comment',
                     'label' => 'Comment',
                     'rules' => 'required'
                 ),
             );


             #setting form validation rules
             $this->form_validation->set_rules( $config );


             #Setting Error Messages for rules
             $this->form_validation->set_message( 'required', $this->lang->line( "msg_sfx" ).'%s' );


             #checking form is valid or not
             if ( $this->form_validation->run() ) {

                 $accesstoken = $GLOBALS['login_user']['Accesstoken'];
                 $respArr     = $this->Common_model->getUserInfo( $accesstoken, ['u.user_id', 'status', 'CONCAT(first_name," ",last_name) as name'] );
                 $user_info   = [];


                 if ( SUCCESS_CODE === $respArr['code'] ) {

                     $user_info = $respArr['userinfo'];


                     #comment data to save in DB
                     $commentInsertArr            = [];
                     $commentInsertArr['user_id'] = $user_info['user_id'];
                     $commentInsertArr['post_id'] = $postDataArr['post_id'];
                     $commentInsertArr['comment'] = $postDataArr['comment'];
                     $commentInsertArr['created'] = datetime();
                     $commentInsertArr['updated'] = datetime();

                     #DB transaction start
                     $this->db->trans_begin();

                     #comment saved in DB
                     $isRequestSuccess = $this->Common_model->insert_single( 'ai_comments', $commentInsertArr );

                     #if comment not saved successfuly
                     # exception will throw
                     if ( !$isRequestSuccess ) {
                         throw new Exception( $this->lang->line( 'try_again' ) );
                     }


                     #if DB transaction run successfuly
                     if ( $this->db->trans_status() ) {

                         #DB transaction commited
                         $this->db->trans_commit();


                         $whereArr          = [];
                         $whereArr['where'] = ['post_id' => $postDataArr['post_id']];
                         $userdetail        = $this->Common_model->fetch_data( 'ai_post', array ('user_id'), $whereArr, true );


                         #commenting user is not a post owner, Send push notification
                         if ( $userdetail['user_id'] != $user_info['user_id'] ) {


                             #Create Android Payload to send push notfication
                             $msg                       = "".$user_info['name']." commented on your post";
                             $androidPayload            = [];
                             $androidPayload['message'] = $msg;
                             $androidPayload['user_id'] = $userdetail['user_id'];
                             $androidPayload['type']    = COMMENT_PUSH;
                             $androidPayload['time']    = time();


                             #Create Ios Payload to send iOS push notification
                             $iosPayload          = [];
                             $iosPayload['alert'] = array ('title' => $msg, 'user_id' => $userdetail['user_id']);
                             $iosPayload['badge'] = 0;
                             $iosPayload['type']  = COMMENT_PUSH;
                             $iosPayload['sound'] = 'beep.mp3';


                             #Combind data to send push notification
                             $pushData                   = [];
                             $pushData['receiver_id']    = $userdetail['user_id']; #post owner user id to receive notification
                             $pushData['androidPayload'] = $androidPayload; #Android payload
                             $pushData['iosPayload']     = $iosPayload; #iOS payload
                             #
                             #
                             #loading common function library
                             $this->load->library( 'commonfn' );


                             #sending notificatoin
                             $this->commonfn->sendPush( $pushData );
                         }

                         $commentData               = [];
                         $commentData['comment']    = $commentInsertArr['comment'];
                         $commentData['comment_id'] = $isRequestSuccess;

                         #setting response
                         $response_array = [
                             'code'   => SUCCESS_CODE,
                             'msg'    => $this->lang->line( 'comment_posted' ),
                             'result' => $commentData
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
                     #Response is not success if session expired or invalid access token
                     $response_array = $respArr;
                 }
             }#form validation if End
             else {#else = form validation failed
                 $err = $this->form_validation->error_array();
                 $arr = array_values( $err );

                 #setting response
                 $response_array = [
                     'code'   => PARAM_REQ,
                     'msg'    => $arr[0],
                     'result' => []
                 ];
             }#else End
             #sending response
             $this->response( $response_array );
         }#try end
         catch ( Exception $e ) {

             #if Error occured DB transaction will rolled back
             $this->db->trans_rollback();

             $error = $e->getMessage();
             $this->response( array ('code' => TRY_AGAIN_CODE, 'msg' => $error, 'result' => []) );
         }#catch End

     }



     /**
      * @SWG\Get(path="/managecomments",
      *   tags={"Comments"},
      *   summary="View the comments of a post",
      *   description="View the comments of a post",
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
      *     name="post_id",
      *     in="query",
      *     description="Post Id",
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="page",
      *     in="query",
      *     description="page no.",
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

             #getting data from GET
             $getDataArr = $this->input->get();

             #Req type 1 pending request,2 sent pending request and empty for getting friends list
             $config = array (
                 array (
                     'field' => 'post_id',
                     'label' => 'Post Id',
                     'rules' => 'required'
                 ),
             );

             #form data array
             $set_data = array (
                 'post_id' => $this->input->get( 'post_id' ),
             );

             #Setting data to form
             $this->form_validation->set_data( $set_data );

             #setting form rules
             $this->form_validation->set_rules( $config );

             #Setting Error Messages for rules
             $this->form_validation->set_message( 'required', 'Please enter the %s' );

             #checking form validation
             if ( $this->form_validation->run() ) {

                 #user id from Global array
                 $user_id = $GLOBALS['api_user_id'];

                 #setting default array
                 $default_array = [
                     "page"    => 1,
                     "post_id" => $getDataArr['post_id'],
                     "user_id" => $user_id,
                     "limit"   => LIMIT
                 ];

                 #setting default values
                 $params = defaultValue( $getDataArr, $default_array );


                 $page             = $params['page'];
                 $offset           = ($page - 1) * $params['limit'];
                 $params['offset'] = $offset;
                 $commentsList     = $this->Comment_model->getComments( $params );

                 #fetching number of pages, if total count greater than current page count
                 if ( ($commentsList['count'] > ($page * $params['limit']) ) ) {
                     $page++;
                 }
                 else {
                     $page = 0;
                 }


                 if ( !empty( $commentsList['result'] ) ) {

                     #setting response
                     $response_array = [
                         'code'       => SUCCESS_CODE,
                         'msg'        => $this->lang->line( 'comments_list_fetched' ),
                         'next_page'  => $page,
                         'total_rows' => $commentsList['count'],
                         'result'     => $commentsList['result']
                     ];
                 }#if end
                 else {

                     #setting response
                     $response_array = [
                         'code'   => NO_DATA_FOUND,
                         'msg'    => $this->lang->line( 'no_comments_found' ),
                         'result' => []
                     ];
                 }#else end
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
         }

     }



     /**
      * @SWG\Put(path="/managecomments",
      *   tags={"Comments"},
      *   summary="Update existing comment",
      *   description="Update existing comment",
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
      *     name="comment_id",
      *     in="formData",
      *     description="Review Id of review which we want to edit",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="comment",
      *     in="formData",
      *     description="New Comment",
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
             #getting data from PUT
             $putDataArr = $this->put();

             #form validation configuration
             $config = array (
                 array (
                     'field' => 'comment_id',
                     'label' => 'Comment Id',
                     'rules' => 'required|numeric'
                 ),
                 array (
                     'field' => 'comment',
                     'label' => 'Comment',
                     'rules' => 'required'
                 ),
             );

             #setting data array for form
             $set_data = array (
                 'comment_id' => $this->put( 'comment_id' ),
                 'comment'    => $this->put( 'comment' )
             );

             #setting form data
             $this->form_validation->set_data( $set_data );

             #setting form validation rules
             $this->form_validation->set_rules( $config );

             #Setting Error Messages for rules
             $this->form_validation->set_message( 'required', 'Please enter the %s' );


             #checking form validation
             if ( $this->form_validation->run() ) {

                 #user id from Global array
                 $user_id                     = $GLOBALS['api_user_id'];
                 $commentUpdateArr            = [];
                 $commentUpdateArr['user_id'] = $user_id;
                 $commentUpdateArr['comment'] = isset( $putDataArr['comment'] ) ? $putDataArr['comment'] : "";
                 $commentUpdateArr['updated'] = datetime();
                 $whereArr                    = [];
                 $whereArr['where']           = ['id' => $putDataArr['comment_id']];

                 #DB transaction start
                 $this->db->trans_begin();
                 $isRequestSuccess = $this->Common_model->update_single( 'ai_comments', $commentUpdateArr, $whereArr );

                 #if not update successfully
                 if ( !$isRequestSuccess ) {
                     throw new Exception( $this->lang->line( 'try_again' ) );
                 }

                 #DB transaction successfull
                 if ( $this->db->trans_status() ) {

                     #db transaction commit
                     $this->db->trans_commit();

                     $commentData            = [];
                     $commentData['comment'] = $commentUpdateArr['comment'];

                     $response_array = [
                         'code'   => SUCCESS_CODE,
                         'msg'    => $this->lang->line( 'comment_update_success' ),
                         'result' => $commentData
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
             }#form validation if end
             else {
                 $err = $this->form_validation->error_array();
                 $arr = array_values( $err );

                 #setting Response Array
                 $response_array = [
                     'code'   => PARAM_REQ,
                     'msg'    => $arr[0],
                     'result' => []
                 ];
             }#else ENd
             #sending final response
             $this->response( $response_array );
         }#try end
         catch ( Exception $ex ) {

             #if exception, roll back
             $this->db->trans_rollback();

             #setting error response
             $response_array = [
                 'CODE'    => TRY_AGAIN_CODE,
                 'MESSAGE' => $ex->getMessage(),
                 'RESULT'  => []
             ];

             #sending error response
             $this->response( $response_array );
         }#catch END

     }



     /**
      * @SWG\Delete(path="/managecomments",
      *   tags={"Comments"},
      *   summary="Delete comment",
      *   description="Delete comment",
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
      *     name="comment_id",
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
             $deleteDataArr = $this->delete();

             #setting form validation configuration
             $config = array (
                 array (
                     'field' => 'comment_id',
                     'label' => 'Comment Id',
                     'rules' => 'required'
                 )
             );

             #from data array
             $set_data = array (
                 'comment_id' => $this->delete( 'comment_id' )
             );

             #setting form data
             $this->form_validation->set_data( $set_data );

             #setting form validation rule configuration
             $this->form_validation->set_rules( $config );

             #Setting Error Messages for rules
             $this->form_validation->set_message( 'required', 'Please enter the %s' );

             #checking form validation
             if ( $this->form_validation->run() ) {

                 #DB transaction start
                 $this->db->trans_begin();

                 #where condition array
                 $whereArr          = [];
                 $whereArr['where'] = ['id' => $deleteDataArr['comment_id']];

                 $isDeleteSuccess = $this->Common_model->delete_data( 'ai_comments', $whereArr );

                 #if not updated successfully, throw exception
                 if ( !$isDeleteSuccess ) {
                     throw new Exception( $this->lang->line( 'try_again' ) );
                 }

                 #checking db transaction
                 if ( $this->db->trans_status() ) {

                     #commiting DB transaction
                     $this->db->trans_commit();

                     #setting response
                     $response_array = [
                         'code'   => SUCCESS_CODE,
                         'msg'    => $this->lang->line( 'comment_delete_success' ),
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
             }#if end form validation
             else {
                 $err = $this->form_validation->error_array();
                 $arr = array_values( $err );

                 #setting response
                 $response_array = [
                     'code'   => PARAM_REQ,
                     'msg'    => $arr[0],
                     'result' => []
                 ];
             }#else end
             #sending final reponse
             $this->response( $response_array );
         }#try end
         catch ( Exception $e ) {

             #if exception, rollback transaction
             $this->db->trans_rollback();

             $error = $e->getMessage();

             #sending response
             $response_array = [
                 'code'   => TRY_AGAIN_CODE,
                 'msg'    => $error,
                 'result' => []
             ];
             #sendig response
             $this->response( $response_array );
         }#catch End

     }



 }
