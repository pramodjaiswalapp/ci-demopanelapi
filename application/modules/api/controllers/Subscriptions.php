<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );
 require APPPATH.'/libraries/REST_Controller.php';

 class Subscriptions extends REST_Controller {

     function __construct() {

         parent::__construct();
         $this->load->model( 'common_model' );
         $this->load->model( 'feeds_model' );
         $this->load->library( 'form_validation' );

     }



     /**
      * @SWG\Post(path="api/subscription",
      *   tags={"Subscription"},
      *   summary="subscription list",
      *   description="subscription list",
      *   operationId="subscription_get",
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
      *     name="Content-Type",
      *     in="header",
      *     description="",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="Accesstoken",
      *     in="header",
      *     description="Access token of user",
      *     required=true,
      *     type="string"
      *   )
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=201, description="Try Again"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      *
      */
     public function subscription_get() {

         $response_array = ['code' => 'TRY_AGAIN_CODE', 'msg' => $this->lang->line( 'try_again' ), 'result' => [], 'page' => ''];
         try {
             $getDataArr = $this->input->get();
             $page       = isset( $getDataArr['page'] ) ? $getDataArr['page'] : 1;

             $limit            = 5;
             $offset           = ($page - 1) * $limit;
             $params['limit']  = $limit;
             $params['offset'] = $offset;
             $user_id          = $GLOBALS['api_user_id'];
             unset( $GLOBALS['api_user_id'] );
             //Get all Subscriptions
             $subscriptions    = $this->get_subscriptions( $params );

             $where["where"]  = ["user_id" => $user_id, 'status' => ACTIVE];
             //Get User subscriptions CSV IDS
             $user_subscribed = $this->common_model->fetch_data( 'ai_user_subscriptions', 'GROUP_CONCAT(subscription_id) as sub_id', $where );
             $subscribed_ids  = (!empty( $user_subscribed )) ? explode( ',', $user_subscribed[0]['sub_id'] ) : [];
             //add flag in each subscripion if user has bought the subscription or not
             $response_data   = $this->check_user_subscribed( $subscriptions, $subscribed_ids );
             $next_page       = 0;
             if ( $subscriptions['count'] > ($page * $limit) ) {
                 $next_page = 1;
             }
             //check if db query returned data
             if ( !empty( $subscriptions['data'] ) ) {
                 $response_array = ['code' => SUCCESS_CODE, 'msg' => $this->lang->line( 'success' ), 'result' => $response_data['data'], 'page' => $next_page];
             }
             else {
                 $response_array = ['code' => NO_DATA_FOUND, 'msg' => $this->lang->line( 'no_data_found' ), 'result' => [], 'page' => ''];
             }
             $this->response( $response_array );
         }
         catch ( Exception $ex ) {

             #setting response
             $response_array = [
                 'code' => TRY_AGAIN_CODE,
                 'msg'  => $ex->getMessage()
             ];

             #sending response
             $this->response( $response_array );
         }#catch End

     }



     /**
      * @SWG\Post(path="api/user_subscription",
      *   tags={"Subscription"},
      *   summary="subscription list",
      *   description="subscription list",
      *   operationId="subscription_get",
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
      *     name="Content-Type",
      *     in="header",
      *     description="",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="Accesstoken",
      *     in="header",
      *     description="Access tokenof user",
      *     required=true,
      *     type="string"
      *   )
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=201, description="Try Again"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      *
      */
     public function user_subscription_get() {

         $response_array = ['code' => TRY_AGAIN_CODE, 'msg' => $this->lang->line( 'try_again' ), 'result' => [], 'page' => ''];

         try {
             $getDataArr = $this->input->get();

             $page              = isset( $getDataArr['page'] ) ? $getDataArr['page'] : 1;
             $params['user_id'] = $GLOBALS['api_user_id'];
             $limit             = 5;
             $offset            = ($page - 1) * $limit;
             $params['limit']   = $limit;
             $params['offset']  = $offset;
             unset( $GLOBALS['api_user_id'] );
             //Get all user Subscriptions
             $subscriptions     = $this->get_user_subscriptions( $params );

             $next_page = 0;
             if ( $subscriptions['count'] > ($page * $limit) ) { //IF 1 START
                 $next_page = 1;
             }
             // IF 1 END
             // Check if db query returned data
             if ( !empty( $subscriptions['data'] ) ) {
                 $response_array = ['code' => SUCCESS_CODE, 'msg' => $this->lang->line( 'success' ), 'result' => $subscriptions['data'], 'page' => $next_page];
             }
             else {
                 //ELSE START
                 $response_array = ['code' => NO_DATA_FOUND, 'msg' => $this->lang->line( 'no_data_found' ), 'result' => [], 'page' => ''];
             }//ELSE END
             $this->response( $response_array );
         }
         catch ( Exception $ex ) {

             #setting response
             $response_array = [
                 'code' => TRY_AGAIN_CODE,
                 'msg'  => $ex->getMessage()
             ];

             #sending response
             $this->response( $response_array );
         }#catch End

     }



     /**
      * @SWG\Post(path="api/buy",
      *   tags={"Subscription"},
      *   summary="buy a subscription",
      *   description="buy a subscription",
      *   operationId="buy_post",
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
      *     name="Content-Type",
      *     in="header",
      *     description="",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="Accesstoken",
      *     in="header",
      *     description="Access tokenof user",
      *     required=true,
      *     type="string"
      *   )
      *  @SWG\Parameter(
      *     name="subscription_id",
      *     in="query",
      *     description="Subscription ID",
      *     required=true,
      *     type="string"
      *   )
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=201, description="Try Again"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      *
      */
     public function buy_post() {

         $response_array = ['code' => TRY_AGAIN_CODE, 'msg' => $this->lang->line( 'try_again' ), 'result' => []];

         try {//TRY BLOCK START
             //Field validation START
             $config          = [['field' => 'subscription_id', 'label' => 'Subscription ID', 'rules' => 'required']];
             $postDataArr     = $this->input->post();
             $subscription_id = !empty( $postDataArr['subscription_id'] ) ? $postDataArr['subscription_id'] : '';
             $set_data        = ['subscription_id' => $subscription_id];

             $this->form_validation->set_data( $set_data );
             $this->form_validation->set_rules( $config );
             $this->form_validation->set_message( 'required', 'Please enter the %s' );
             //Field validation END

             if ( $this->form_validation->run() ) {//IF 1 START
                 $params['user_id']         = $GLOBALS['api_user_id'];
                 $params['subscription_id'] = $subscription_id;
                 unset( $GLOBALS['api_user_id'] );

                 $params['limit']  = 1;
                 $params['offset'] = 0;

                 //Get Subscrition details of the given subscription
                 $params['subscription_detail'] = $this->get_subscription( $params );

                 $this->db->trans_begin();
                 //Subscribe user with given subscription ID
                 $insert_id = $this->buy( $params['subscription_detail'], $params['user_id'] );

                 //check if db query returned data
                 if ( $this->db->trans_status() === TRUE && $insert_id ) {//IF 2 START
                     $this->db->trans_commit();
                     $response_array = ['code' => SUCCESS_CODE, 'msg' => $this->lang->line( 'success' ), 'result' => ["id" => $insert_id]];
                 }
                 else {//IF 2 ENDS
                     //ELSE START
                     $this->db->trans_rollback();
                     $response_array = ['code' => TRY_AGAIN_CODE, 'msg' => $this->lang->line( 'try_again' ), 'result' => []];
                 }//ELSE ENDS
                 $this->response( $response_array );
             } //IF 1 END
             else { //ELSE START
                 $err     = $this->form_validation->error_array();
                 $arr     = array_values( $err );
                 $err_msg = (isset( $arr[0] )) ? $arr[0] : $this->lang->line( 'missing_parameter' );
                 $this->response( array ('code' => PARAM_REQ, 'msg' => $err_msg, 'result' => []) );
             } //ELSE END
         }//TRY BLOCK END
         catch ( Exception $ex ) {

             #setting response
             $response_array = [
                 'code' => TRY_AGAIN_CODE,
                 'msg'  => $ex->getMessage()
             ];

             #sending response
             $this->response( $response_array );
         }#catch End

     }



     /**
      * @SWG\Post(path="api/cancel",
      *   tags={"Subscription"},
      *   summary="Revoke a subscription",
      *   description="Revoke a subscription",
      *   operationId="revoke_post",
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
      *     name="Content-Type",
      *     in="header",
      *     description="",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="Accesstoken",
      *     in="header",
      *     description="Access tokenof user",
      *     required=true,
      *     type="string"
      *   )
      *  @SWG\Parameter(
      *     name="subscription_id",
      *     in="query",
      *     description="Subscription ID",
      *     required=true,
      *     type="string"
      *   )
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=201, description="Try Again"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      *
      */
     public function revoke_post() {
         $response_array = ['code' => TRY_AGAIN_CODE, 'msg' => $this->lang->line( 'try_again' ), 'result' => []];

         try {//TRY BLOCK START
             //Field validation START
             $config          = [['field' => 'subscription_id', 'label' => 'Subscription ID', 'rules' => 'required']];
             $postDataArr     = $this->input->post();
             $subscription_id = !empty( $postDataArr['subscription_id'] ) ? $postDataArr['subscription_id'] : '';
             $set_data        = ['subscription_id' => $subscription_id];

             $this->form_validation->set_data( $set_data );
             $this->form_validation->set_rules( $config );
             $this->form_validation->set_message( 'required', 'Please enter the %s' );
             //Field validation END

             if ( $this->form_validation->run() ) {
                 //IF 1 START
                 $params['user_id']         = $GLOBALS['api_user_id'];
                 $params['subscription_id'] = $subscription_id;
                 unset( $GLOBALS['api_user_id'] );

                 $this->db->trans_begin();

                 //Revoke the given subscription
                 $this->revoke( $params );

                 //check if db query returned data
                 if ( $this->db->trans_status() === TRUE ) {//IF 2 START
                     $this->db->trans_commit();
                     $response_array = ['code' => SUCCESS_CODE, 'msg' => $this->lang->line( 'success' ), 'result' => []];
                 }//IF 2 ENDS
                 else {//ELSE START
                     $this->db->trans_rollback();
                     $response_array = ['code' => TRY_AGAIN_CODE, 'msg' => $this->lang->line( 'try_again' ), 'result' => []];
                 }//ELSE ENDS
                 $this->response( $response_array );
             } //IF 1 END
             else { //ELSE START
                 $err     = $this->form_validation->error_array();
                 $arr     = array_values( $err );
                 $err_msg = (isset( $arr[0] )) ? $arr[0] : $this->lang->line( 'missing_parameter' );
                 $this->response( array ('code' => PARAM_REQ, 'msg' => $err_msg, 'result' => []) );
             } //ELSE END
         }//TRY BLOCK END
         catch ( Exception $e ) {
//            $error = $e->getMessage();
             $this->response( $response_array );
         }

     }



     /**
      * @name get_subscriptions
      * @description fetch subscriptions list from table ai_subscriptions
      *
      * @param array
      * @return array
      */
     function get_subscriptions( $params ) {

         $result = $this->common_model->get_subscriptions( $params );
         return $result;

     }



     /**
      * @name get_subscription
      * @description fetch subscriptions list from table ai_subscriptions
      *
      * @param array
      * @return array
      */
     function get_subscription( $params ) {

         $result = $this->common_model->get_subscription( $params );
         return $result;

     }



     /**
      * @name get_user_subscriptions
      * @description fetch user's subscriptions list from table ai_subscriptions
      *
      * @param array
      * @return array
      */
     function get_user_subscriptions( $params ) {

         $result = $this->common_model->get_user_subscriptions( $params );
         return $result;

     }



     /**
      * @name buy
      * @description Subscribe User with given subscription
      *
      * @param array
      * @param string
      * @return string
      */
     function buy( $params, $user_id ) {
         $status = TRUE;
         if ( empty( $params['data'] ) ) {
             $status = FALSE;
         }

         if ( $status ) {
             $result = $params['data'][0];
             //get Valid from and Valid to dates on basis of Validity
             $dates  = $this->get_dates_on_type( $result['subs_recurring'] );

             $data['user_id']           = $user_id;
             $data['subscription_id']   = $result['subscription_id'];
             $data['subscription_name'] = $result['subscription_name'];
             $data['status']            = ACTIVE;
             $data['price']             = $result['price'];
             $data['start_date']        = $dates['from_date'];
             $data['end_date']          = $dates['to_date'];
             $data['create_date']       = date( 'Y-m-d H:i:s' );
             $data['update_date']       = date( 'Y-m-d H:i:s' );

             $table  = 'ai_user_subscriptions';
             $status = $this->common_model->insert_single( $table, $data );
         }
         return $status;

     }



     /**
      * @name revoke
      * @description Revoke/Cancel the given subscription User
      *
      * @param array
      * @return array
      */
     function revoke( $params ) {

         $data           = ["status" => BLOCKED];
         $where["where"] = ["user_id" => $params["user_id"], "subscription_id" => $params["subscription_id"]];
         $table          = 'ai_user_subscriptions';
         $status         = $this->common_model->update_single( $table, $data, $where );

         return $status;

     }



     /**
      * @name get_dates_on_type
      * @description get Valid from and Valid to dates on basis of Validity
      *
      * @param array
      * @return array
      */
     function get_dates_on_type( $validity ) {

         $dates     = [];
         $from_date = '';
         $to_date   = '';
         switch ( $validity ) {
             case RECURRING_DAY:
                 $from_date = date( 'Y-m-d H;i:s' );
                 $to_date   = date( 'Y-m-d  H;i:s', strtotime( '+1 day' ) );

                 break;
             case RECURRING_WEEK:
                 $from_date = date( 'Y-m-d H;i:s' );
                 $to_date   = date( 'Y-m-d H;i:s', strtotime( '+7 day' ) );

                 break;
             case RECURRING_MONTH:
                 $from_date = date( 'Y-m-d H;i:s' );
                 $to_date   = date( 'Y-m-d H;i:s', strtotime( '+30 day' ) );

                 break;
             case RECURRING_YEAR:
                 $from_date = date( 'Y-m-d H;i:s' );
                 $to_date   = date( 'Y-m-d H;i:s', strtotime( '+365 day' ) );

                 break;

             default:
                 break;
         }
         $dates['from_date'] = $from_date;
         $dates['to_date']   = $to_date;
         return $dates;

     }



     /**
      * @name check_user_subscribed
      * @description add flag in each subscripion if user has bought the subscription or not
      *
      * @param array
      * @param array
      * @return array
      */
     function check_user_subscribed( $subscription, $subscribed_ids ) {

         if ( !empty( $subscribed_ids ) && !empty( $subscription['data'] ) ) {

             foreach ( $subscription['data'] as $key => $value ) {
                 $subscription['data'][$key]['buy'] = (in_array( $value['subscription_id'], $subscribed_ids ) ) ? "1" : "0";
             }
         }
         return $subscription;

     }



 }
