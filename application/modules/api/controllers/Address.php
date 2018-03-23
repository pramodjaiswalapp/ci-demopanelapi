<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );
 require APPPATH.'/libraries/REST_Controller.php';

 class Address extends REST_Controller {

     function __construct() {
         parent::__construct();
         $this->load->model( 'Common_model' );

     }



     /**
      * @SWG\Get(path="/address",
      *   tags={"Address"},
      *   summary="Get address information",
      *   description="Get country,state and city list",
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
      *     name="country_code",
      *     in="query",
      *     description="hit with country code to get list of state belongs to it",
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="state_code",
      *     in="query",
      *     description="hit it with country code to get list of city belongs to it",
      *     type="string"
      *   ),
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=201, description="Please try again"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      * )
      */
     public function index_get() {

         # Don't send any state or country code for getting the list of country
         try {

             #Getting Values from GET
             $getDataArr = $this->get();

             #setting Default values to array KEYS
             $default      = array (
                 "limit"        => LIMIT,
                 "country_code" => "",
                 "state_code"   => "",
                 "searchlike"   => "",
                 "page"         => 1
             );
             $defaultValue = defaultValue( $getDataArr, $default );


             $listType     = '';
             $listData     = '';
             $country_code = $defaultValue['country_code'];
             $state_code   = $defaultValue['state_code'];
             $searchlike   = $defaultValue['searchlike'];
             $page         = $defaultValue['page'];
             $limit        = $defaultValue['limit'];
             $offset       = ($page - 1) * $limit;
             $whereArr     = [];

             #IF country code and state code is not set
             #then only countries list will sent
             if ( empty( $country_code ) && empty( $state_code ) ) {
                 $listType = 'countryList';
                 $table    = 'country_list';
             }
             else if ( !empty( $country_code ) && empty( $state_code ) ) {#country code set and state code is not set then state list will sent
                 $listType          = 'state_list';
                 $whereArr['where'] = array ('country_code' => $country_code);
                 $table             = 'state_list';
             }
             else if ( !empty( $country_code ) && !empty( $state_code ) ) {
                 $whereArr['limit'] = array ($limit, $offset);
                 if ( !empty( $searchlike ) ) {
                     $whereArr['like'] = array ('name' => $searchlike);
                 }

                 $listType          = 'cityList';
                 $whereArr['where'] = array ('country_code' => $country_code, 'state_code' => $state_code);
                 $table             = 'city_list';
             }
             else {
                 $whereArr['limit'] = array ($limit, $offset);
                 if ( !empty( $searchlike ) ) {
                     $whereArr['like'] = array ('name' => $searchlike);
                 }

                 $listType          = 'cityList';
                 $whereArr['where'] = array ('country_code' => $country_code, 'state_code' => $state_code);
                 $table             = 'city_list';
             }

             $whereArr['order_by'] = ['name' => 'asc'];
             $whereArr['group_by'] = ['id'];
             $listData             = $this->Common_model->fetch_data( $table, array ('SQL_CALC_FOUND_ROWS *'), $whereArr );
             $totalrows            = $this->Common_model->totalrows;

             if ( ($totalrows > ($page * $limit) ) ) {
                 $page++;
             }
             else {
                 $page = 0;
             }

             #If Result list is not empty
             if ( !empty( $listData ) ) {
                 $response_array = [
                     'code'      => SUCCESS_CODE,
                     'msg'       => $this->lang->line( 'list_fetched' ),
                     'next_page' => $page,
                     'totalrows' => $totalrows,
                     'result'    => $listData
                 ];
             }#if End
             else {
                 $response_array = [
                     'code'   => TRY_AGAIN_CODE,
                     'msg'    => $this->lang->line( 'list_fetched' ),
                     'result' => []
                 ];
             }#else End
             #sending response
             $this->response( $response_array );
         }
         catch ( Exception $e ) {
             $response_array = [
                 'code'   => EMAIl_SEND_FAILED,
                 'msg'    => $e->getMessage(),
                 'result' => []
             ];
             $this->response( $response_array );
         }

     }



 }
