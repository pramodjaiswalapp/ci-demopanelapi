<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );
 require APPPATH.'/libraries/REST_Controller.php';

 class Managereviews extends REST_Controller {

     function __construct() {
         parent::__construct();
         $this->load->model( 'Review_model' );

     }



     /**
      * @SWG\Post(path="/Managereviews",
      *   tags={"Reviews"},
      *   summary="Give review for a post",
      *   description="Give review for a post",
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
      *     in="header",
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
      *     name="review",
      *     in="formData",
      *     description="Review",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="rating",
      *     in="formData",
      *     description="Rating",
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

             #response Array
             $response_array = [];

             $postDataArr = $this->post();

             #setting form validation rule array
             $config = array (
                 array (
                     'field' => 'post_id',
                     'label' => 'Post Id',
                     'rules' => 'required'
                 ),
                 array (
                     'field' => 'rating',
                     'label' => 'Rating',
                     'rules' => 'required'
                 ),
             );

             #setting Rule
             $this->form_validation->set_rules( $config );

             # Setting Error Messages for rules
             $this->form_validation->set_message( 'required', $this->lang->line( "msg_sfx" ).'%s' );


             #running form validation
             if ( $this->form_validation->run() ) {


                 if ( MULTIPLE_REVIEW_ALLOWED ) {
                     /*
                      * Request Array
                      */
                     $reviewInsertArr = [
                         "user_id" => $GLOBALS['api_user_id'],
                         "post_id" => $postDataArr['post_id'],
                         "review"  => isset( $postDataArr['review'] ) ? $postDataArr['review'] : "",
                         "rating"  => $postDataArr['rating'],
                         "created" => datetime(),
                         "updated" => datetime()
                     ];

                     $isRequestSuccess = $this->Common_model->insert_single( 'ai_reviews', $reviewInsertArr );

                     if ( $isRequestSuccess ) {
                         $whereArr['where'] = ['post_id' => $postDataArr['post_id']];
                         $userdetail        = $this->Common_model->fetch_data( 'ai_post', array ('user_id'), $whereArr, true );

                         if ( $userdetail['user_id'] != $GLOBALS['api_user_id'] ) {

                             $msg = "".$postDataArr['name']." has reviewed your post";

                             #Create Android Payload
                             $androidPayload = [
                                 "message" => $msg,
                                 "user_id" => $userdetail['user_id'],
                                 "type"    => REVIEW_PUSH,
                                 "time"    => time()
                             ];


                             #Create Ios Payload
                             $iosPayload = [
                                 "alert" => array ('title' => $msg, 'user_id' => $userdetail['user_id']),
                                 "badge" => 0,
                                 "type"  => REVIEW_PUSH,
                                 "sound" => "beep.mp3"
                             ];


                             #data array to send push
                             $pushData = [
                                 "receiver_id"    => $userdetail['user_id'],
                                 "androidPayload" => $androidPayload,
                                 "iosPayload"     => $iosPayload
                             ];

                             #loading common function library
                             $this->load->library( 'commonfn' );

                             #sending push notiofication
                             $this->commonfn->sendPush( $pushData );
                         }

                         $reviewData = [
                             "review"    => $reviewInsertArr['review'],
                             "rating"    => $reviewInsertArr['rating'],
                             "review_id" => $isRequestSuccess
                         ];

                         $response_array = [
                             'code'   => SUCCESS_CODE,
                             'msg'    => $this->lang->line( 'review_success' ),
                             'result' => $reviewData
                         ];

                         $this->response( $response_array );
                     }
                     else {
                         $response_array = [
                             'code'   => TRY_AGAIN_CODE,
                             'msg'    => $this->lang->line( 'try_again' ),
                             'result' => []
                         ];
                         $this->response( $response_array );
                     }
                 }
                 else {
                     $whereArr                = [];
                     $whereArr['customWhere'] = "((user_id=".$GLOBALS['api_user_id']." AND post_id=".$postDataArr['post_id']."))";
                     $isReviewExist           = $this->Common_model->fetch_data( 'ai_reviews', array ('id'), $whereArr, true );
                     if ( !empty( $isReviewExist ) ) {
                         $response_array = [
                             'code'   => REVIEW_ALREADY_EXISTS,
                             'msg'    => $this->lang->line( 'review_exist' ),
                             'result' => []
                         ];
                     }
                 }
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
         }
         catch ( Exception $e ) {
             $error = $e->getMessage();
             list($msg, $code) = explode( " || ", $error );
             $this->response( array ('code' => $code, 'msg' => $msg, 'result' => []) );
         }

     }



     /**
      * @SWG\Get(path="/Managereviews",
      *   tags={"Reviews"},
      *   summary="View the reviews of a post",
      *   description="View the reviews of a post",
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
      *     in="header",
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

             $getDataArr = $this->input->get();

             #setting form validation rule array
             $config = array (
                 array (
                     'field' => 'post_id',
                     'label' => 'Post Id',
                     'rules' => 'required|numeric'
                 ),
             );

             #setting form data array
             $set_data = array (
                 'post_id' => $this->input->get( 'post_id' ),
             );

             #setting form data
             $this->form_validation->set_data( $set_data );

             #setting Rule
             $this->form_validation->set_rules( $config );

             # Setting Error Messages for rules
             $this->form_validation->set_message( 'required', 'Please enter the %s' );

             #checking form validation
             if ( $this->form_validation->run() ) {


                 #setting default value array
                 $default_array = [
                     "page"    => 1,
                     "post_id" => $getDataArr['post_id'],
                     "user_id" => $GLOBALS['api_user_id'],
                     "limit"   => LIMIT
                 ];
                 $params        = defaultValue( $getDataArr, $default_array );


                 $limit             = $params['limit'];
                 $page              = $params['page'];
                 $params['post_id'] = $getDataArr['post_id'];
                 $params['user_id'] = $GLOBALS['api_user_id'];
                 $params['offset']  = ($page - 1) * $params['limit'];

                 #getting Reviews list for post
                 $reviewsList = $this->Review_model->getReviews( $params );


                 #Checking total count is more than current page count
                 if ( ($reviewsList['count'] > ($page * $limit) ) ) {
                     $page++;
                 }
                 else {
                     $page = 0;
                 }


                 if ( !empty( $reviewsList['result'] ) ) {

                     #setting response
                     $response_array = [
                         'code'       => SUCCESS_CODE,
                         'msg'        => $this->lang->line( 'reviews_list_fetched' ),
                         'next_page'  => $page,
                         'total_rows' => $reviewsList['count'],
                         'result'     => $reviewsList['result']
                     ];
                 }
                 else {

                     #setting response
                     $response_array = [
                         'code'   => NO_DATA_FOUND,
                         'msg'    => $this->lang->line( 'no_reviews_found' ),
                         'result' => []
                     ];
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

             #sending finale response
             $this->response( $response_array );
         }#try end
         catch ( Exception $e ) {
             $error = $e->getMessage();

             #setting response
             $response_array = [
                 'code'   => TRY_AGAIN_CODE,
                 'msg'    => $error,
                 'result' => []
             ];
             $this->response( $response_array );
         }#catch End

     }



     /**
      * @SWG\Put(path="/Managereviews",
      *   tags={"Reviews"},
      *   summary="Update existing review",
      *   description="Update existing review",
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
      *   ),
      *   @SWG\Parameter(
      *     name="review_id",
      *     in="formData",
      *     description="Review Id of review which we want to edit",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="rating",
      *     in="formData",
      *     description="New Rating",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="review",
      *     in="formData",
      *     description="Review text",
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
             #response array
             $response_array = [];

             $putDataArr = $this->put();

             $config = [];

             #setting form validation rule array
             $config = array (
                 array (
                     'field' => 'rating',
                     'label' => 'Rating',
                     'rules' => 'required'
                 ),
                 array (
                     'field' => 'review_id',
                     'label' => 'Review Id',
                     'rules' => 'required'
                 )
             );

             #form data configuration
             $set_data = array (
                 'review_id' => $this->put( 'review_id' ),
                 'rating'    => $this->put( 'rating' )
             );

             #setting form data
             $this->form_validation->set_data( $set_data );

             #setting Rule
             $this->form_validation->set_rules( $config );

             # Setting Error Messages for rules
             $this->form_validation->set_message( 'required', 'Please enter the %s' );


             #checking form validation
             if ( $this->form_validation->run() ) {

                 #db transaction start
                 $this->db->trans_begin();

                 #data array to update
                 $reviewUpdateArr = [
                     "user_id" => $GLOBALS['api_user_id'],
                     "review"  => isset( $putDataArr['review'] ) ? $putDataArr['review'] : "",
                     "rating"  => $putDataArr['rating'],
                     "updated" => datetime()
                 ];

                 #where condition array
                 $whereArr          = [];
                 $whereArr['where'] = ['id' => $putDataArr['review_id']];

                 #updating review details
                 $isRequestSuccess = $this->Common_model->update_single( 'ai_reviews', $reviewUpdateArr, $whereArr );


                 if ( $isRequestSuccess ) {

                     #checking DB transaction
                     if ( $this->db->trans_status() ) {
                         #commiting DB transaction
                         $this->db->trans_commit();

                         $reviewData = [
                             "review" => $reviewUpdateArr['review'],
                             "rating" => $reviewUpdateArr['rating']
                         ];

                         $response_array = [
                             'code'   => SUCCESS_CODE,
                             'msg'    => $this->lang->line( 'review_update_success' ),
                             'result' => $reviewData
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
                 $err = $this->form_validation->error_array();
                 $arr = array_values( $err );

                 $response_array = [
                     'code'   => PARAM_REQ,
                     'msg'    => $arr[0],
                     'result' => []
                 ];
             }
             $this->response( $response_array );
         }#try end
         catch ( Exception $e ) {
             $error = $e->getMessage();

             #rolling back
             $this->db->trans_rollback();
             #setting error reponse array
             $response_array = [
                 'code'   => TRY_AGAIN_CODE,
                 'msg'    => $error,
                 'result' => []
             ];

             #setting error response
             $this->response( $response_array );
         }#catch End

     }



     /**
      * @SWG\Delete(path="/Delete Review",
      *   tags={"Reviews"},
      *   summary="Delete Review",
      *   description="Managereviews",
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
      *     in="header",
      *     description="Access token received during signup or login",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="review_id",
      *     in="formData",
      *     description="Review Id of review which we want to delete",
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

             #response array
             $response_array = [];

             #setting form validation rule array
             $config = array (
                 array (
                     'field' => 'review_id',
                     'label' => 'Review id',
                     'rules' => 'required'
                 )
             );

             #form data array
             $set_data = array (
                 'review_id' => $this->delete( 'review_id' )
             );

             #setting form data
             $this->form_validation->set_data( $set_data );

             #setting Rule
             $this->form_validation->set_rules( $config );

             # Setting Error Messages for rules
             $this->form_validation->set_message( 'required', 'Please enter the %s' );

             #checking form validation
             if ( $this->form_validation->run() ) {


                 #db transaction start to delete
                 $this->db->trans_begin();

                 #where condition array
                 $whereArr          = [];
                 $whereArr['where'] = ['id' => $deleteDataArr['review_id']];
                 $isDeleteSuccess   = $this->Common_model->delete_data( 'ai_reviews', $whereArr );

                 if ( $isDeleteSuccess ) {

                     #checking DB transaction
                     if ( $this->db->trans_status() ) {
                         #commiting DB transaction
                         $this->db->trans_commit();
                         $response_array = [
                             'code'   => SUCCESS_CODE,
                             'msg'    => $this->lang->line( 'delete_success' ),
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
                 $err = $this->form_validation->error_array();
                 $arr = array_values( $err );

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

             $error          = $e->getMessage();
             $response_array = [
                 'code'   => TRY_AGAIN_CODE,
                 'msg'    => $error,
                 'result' => []
             ];
             $this->response( $response_array );
         }#catch End

     }



 }
