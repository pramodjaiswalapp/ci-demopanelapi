<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );
 require APPPATH.'/libraries/REST_Controller.php';

 class Events extends REST_Controller {

     function __construct() {

         parent::__construct();
         $this->load->model( 'common_model' );

     }



     /**
      * @SWG\Post(path="api/Events",
      *   tags={"Events"},
      *   summary="Save events",
      *   description="Save events",
      *   operationId="events_post",
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
      *     name="api-key",
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
      *     name="access_token",
      *     in="header",
      *     description="Access tokenof user",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="title",
      *     in="query",
      *     description="event title",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="description",
      *     in="query",
      *     description="Event Description",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="start_date",
      *     in="query",
      *     description="Event start date",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="end_date",
      *     in="query",
      *     description="Event end date",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="location",
      *     in="query",
      *     description="Event location",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="type",
      *     in="query",
      *     description="Event type",
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
     public function index_post() {
         try {
             #getting data from Post Array
             $postDataArr    = $this->post();
             #response array
             $response_array = [];
             $default        = array (
                 "title"      => "",
                 "event_type" => "",
             );
             #Setting Default Value
             $defaultValue   = defaultValue( $postDataArr['event_array'][0], $default );

             $title      = $defaultValue['title'];
             $event_type = $defaultValue['event_type'];
             #setting form validation rule

             $set_data = array (
                 'title'      => $title,
                 'event_type' => $event_type
             );
             $config   = [
                 ['field' => 'title', 'label' => 'Title', 'rules' => 'required'],
                 ['field' => 'event_type', 'label' => 'Event Type', 'rules' => 'required']
             ];

             #Setting Rules, Data and error Messages for rules
             $this->form_validation->set_data( $set_data );
             $this->form_validation->set_rules( $config );
             $this->form_validation->set_message( 'required', 'Please enter the %s' );

             if ( $this->form_validation->run() ) {

                 // $postDataArr['event_array'] = json_decode( $postDataArr['event_array'], true );
                 #DB transaction start
                 $this->db->trans_begin();

                 #Save user events - Table - ai_events
                 $insert_status = $this->save_events( $postDataArr['event_array'], $GLOBALS['api_user_id'] );

                 #if error in Saving
                 if ( !$insert_status ) {

                     #rolling back
                     $this->db->trans_rollback();
                     throw new Exception( $this->lang->line( 'try_again' ) );
                 }


                 #Check if all DB queries executed successfully
                 if ( $this->db->trans_status() === TRUE ) {

                     $this->db->trans_commit();

                     #setting Response
                     $response_array = [
                         'code'   => SUCCESS_CODE,
                         'msg'    => $this->lang->line( 'event_data_saved' ),
                         'result' => []
                     ];
                 }#if end
                 else {#IF transaction failed
                     #rolling back
                     $this->db->trans_rollback();

                     #setting Response Array
                     $response_array = [
                         'CODE'   => TRY_AGAIN_CODE,
                         'msg'    => $this->lang->line( 'try_again' ),
                         'result' => []
                     ];
                 }
             }#form validation if end
             else {#if form validation goes wrong
                 $err = $this->form_validation->error_array();
                 $arr = array_values( $err );

                 #setting Response
                 $response_array = [
                     'code'   => PARAM_REQ,
                     'msg'    => $arr,
                     'result' => []
                 ];
             }

             #sending Response
             $this->response( $response_array );
         }#TRY END
         catch ( Exception $exc ) {

             $this->db->trans_rollback();
             $error = $exc->getMessage();

             #setting Response
             $response_array = [
                 'code'   => TRY_AGAIN_CODE,
                 'msg'    => $error,
                 'result' => []
             ];

             #sending Response
             $this->response( $response_array );
         }#CATCH END

     }



     /**
      * @SWG\Post(path="api/event",
      *   tags={"Events"},
      *   summary="get events list",
      *   description="get events list",
      *   operationId="index_get",
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
      *     name="api-key",
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
      *     name="access_token",
      *     in="header",
      *     description="Access tokenof user",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid")
      * )
      */
     /*
      */
     public function index_get() {
         try {
             $getDataArr = $this->input->get();

             $default      = array (
                 "page"      => 1,
                 "to_date"   => "",
                 "from_date" => "",
                 "limit"     => CHAT_LIMIT
             );
             #Setting Default Value
             $defaultValue = defaultValue( $getDataArr, $default );

             $page                = $defaultValue['page'];
             $offset              = ($page - 1) * $defaultValue['limit'];
             $params['limit']     = $defaultValue['limit'];
             $params['offset']    = $offset;
             $params['from_date'] = $defaultValue['from_date'];
             $params['to_date']   = $defaultValue['to_date'];


             #Get all Events
             $events_data = $this->get_events( $params );



             if ( !empty( $events_data['data'] ) ) {#if Start
                 #setting response
                 $response_array = [
                     'code'   => SUCCESS_CODE,
                     'msg'    => 'success',
                     'result' => $events_data['data'],
                     'page'   => $page
                 ];
             }#IF END
             else {#ELSE START
                 #setting response
                 $response_array = [
                     'code'   => NO_DATA_FOUND,
                     'msg'    => 'NO_DATA_FOUND',
                     'result' => [],
                     'page'   => ''
                 ];
             }#Else End
             #sending Response
             $this->response( $response_array );
         }#try END
         catch ( Exception $e ) {#Catch Start
             $error = $e->getMessage();

             #setting response
             $response_array = [
                 'code'   => TRY_AGAIN_CODE,
                 'msg'    => $error,
                 'result' => [],
                 'page'   => ''
             ];
             $this->response( $response_array );
         }#Catch End

     }



     /**
      * @name save_events
      * @description Save user location in table ai_user_location
      *
      * @param array
      * @param string
      *
      * @return string
      */
     function save_events( $data, $user_id ) {
         $status = 0;
         $table  = 'ai_events';

         $insert_events = [];
         if ( count( $data ) > 0 ) {

             foreach ( $data as $value ) {
                 $insert_events[] = [
                     'title'       => $value['title'],
                     'description' => $value['description'],
                     'start_date'  => $value['start_date'],
                     'end_date'    => $value['end_date'],
                     'location'    => $value['location'],
                     'event_type'  => $value['event_type'],
                     'user_id'     => $user_id
                 ];
             }
         }
         if ( count( $insert_events ) > 0 ) {
             $status = $this->common_model->insert_multiple( $table, $insert_events );
         }
         return $status;

     }



     /**
      * @function get_events
      * @description Fetch all events
      *
      * @param array $params
      * @return type
      */
     function get_events( $params ) {

         $result = $this->common_model->get_events( $params );
         return $result;

     }



     /**
      * @function valiate_input
      * @desription Validate Event Input Fields
      *
      * @param array $postDataArr
      * @return boolean
      */
     function valiate_input( $postDataArr ) {

         #Setting Form validation configuration
         $config = [
             ['field' => 'title', 'label' => 'Title', 'rules' => 'required'],
             ['field' => 'start_date', 'label' => 'Start Date', 'rules' => 'required'],
             ['field' => 'end_date', 'label' => 'End Date', 'rules' => 'required'],
             ['field' => 'event_type', 'label' => 'Event Type', 'rules' => 'required']
         ];


         $validation = true;
         if ( !empty( $postDataArr ) ) {

             foreach ( $postDataArr as $data ) {

                 $default      = array (
                     "title"      => "",
                     "start_date" => "",
                     "end_date"   => "",
                     "event_type" => ""
                 );
                 #Setting Default Value
                 $defaultValue = defaultValue( $data, $default );

                 $set_data = [
                     'title'      => $defaultValue['title'],
                     'start_date' => $defaultValue['start_date'],
                     'end_date'   => $defaultValue['end_date'],
                     'event_type' => $defaultValue['event_type']
                 ];

                 // Set Data , Rules and Error messages for API request parameter validation
                 $this->form_validation->set_data( $set_data );
                 $this->form_validation->set_rules( $config );
                 $this->form_validation->set_message( 'required', 'Please enter the %s' );
                 $status = $this->form_validation->run();
                 if ( !$status ) {
                     $validation = $status;
                 }
             }
         }
         return $validation;

     }



 }
