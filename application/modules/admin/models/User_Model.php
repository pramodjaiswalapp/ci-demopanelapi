<?php

 class User_Model extends CI_Model {

     public $finalrole = array ();

     public function __construct() {
         parent::__construct();
         $this->load->database();

     }



     /**
      * @name userlist
      * @description Used to filter the users
      *
      * @param int $offset To set offset in MySql Query. E.g : select * from xxxx limit offset, limit
      * @param int $limit To set number of Rows at a time
      * @param array $params An array of parameters to filter out CMS content list
      * @return array $res An array of fetched result
      */
     public function userlist( $params ) {

         $sortMap = [
             "name"       => "name",
             "registered" => "u.user_id"
         ];

         $this->db->select( "SQL_CALC_FOUND_ROWS u.*", False );
         $this->db->from( 'ai_user as u' );

         if ( !empty( $params['searchlike'] ) ) {
             $this->db->group_start();
             $this->db->like( 'concat_ws(" ",first_name,middle_name,last_name)', $params['searchlike'] );
             $this->db->or_like( 'email', $params['searchlike'] );
             $this->db->or_like( 'phone', $params['searchlike'] );
             $this->db->group_end();
         }
         if ( (isset( $params["sortfield"] ) && !empty( $params["sortfield"] ) && in_array( $params["sortfield"], array_keys( $sortMap ) ) ) &&
             (isset( $params["sortby"] ) && !empty( $params["sortby"] )) ) {
             if ( $params["sortfield"] == "name" ) {
                 $this->db->order_by( "u.first_name", $params["sortby"] );
                 $this->db->order_by( "u.middle_name", $params["sortby"] );
                 $this->db->order_by( "u.last_name", $params["sortby"] );
             }
             else {
                 $this->db->order_by( $sortMap[$params["sortfield"]], $params["sortby"] );
             }
         }
         else {
             $this->db->order_by( "u.registered_date", "DESC" );
         }

         if ( !empty( $params['status'] ) ) {
             $this->db->where( 'status', $params['status'] );
         }
         else {
             $this->db->where( 'status != 3' );
         }

         if ( !empty( $params['country'] ) ) {
             $this->db->where( 'country_id', $params['country'] );
         }
         if ( !empty( $params['startDate'] ) && !empty( $params['endDate'] ) ) {
             $startDate = date( 'Y-m-d', strtotime( $params['startDate'] ) );
             $endDate   = date( 'Y-m-d', strtotime( $params['endDate'] ) );
             $this->db->where( "DATE(registered_date) >= '".$startDate."' AND DATE(registered_date) <= '".$endDate."' " );
         }

         $this->db->limit( $params['limit'], $params['offset'] );

         $query         = $this->db->get();
         $res['result'] = $query->result_array();
         $res['total']  = $this->db->query( 'SELECT FOUND_ROWS() count' )->row()->count;

         return $res;

     }



 }
