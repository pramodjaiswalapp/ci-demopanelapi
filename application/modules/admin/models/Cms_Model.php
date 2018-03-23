<?php

 class Cms_Model extends CI_Model {

     public $finalrole = array ();

     public function __construct() {
         $this->load->database();

     }



     /**
      * @name pagelist
      * @description Used to list out all Page content
      *
      * @param int $offset To set offset in MySql Query. E.g : select * from xxxx limit offset, limit
      * @param int $limit To set number of Rows at a time
      * @param array $params An array of parameters to filter out CMS content list
      * @return array $res An array of fetched result
      */
     public function pagelist( $limit, $offset, $params ) {

         try {
             $orderByMap = [
                 "added"   => "created_date",
                 "name"    => "name",
                 "content" => "content",
             ];

             $this->db->select( "SQL_CALC_FOUND_ROWS u.*", False );
             $this->db->from( 'page_master as u' );
             $this->db->limit( $limit, $offset );

             if ( !empty( $params['searchlike'] ) ) {//IF WHERE CONDITIONS
                 $this->db->group_start();
                 $this->db->like( 'name', $params['searchlike'] );
                 $this->db->group_end();
             }// IF WHRE CONDITIONS

             /* order by */
             if ( (isset( $params["field"] ) && !empty( $params["field"] ) && in_array( $params["field"], array_keys( $orderByMap ) ) ) &&
                 (isset( $params["order"] ) && !empty( $params["order"] )) ) {
                 $this->db->order_by( $orderByMap[$params["field"]], $params["order"] );
             }
             else {//default Order by on created date desc
                 $this->db->order_by( "created_date", "DESC" );
             }
             /* order by end */


             if ( !$query = $this->db->get() ) {
                 $error = $this->db->error();
                 throw new Exception( $error['message'] );
             }
             $res['result'] = $query->result_array();
             $res['total']  = $this->db->query( 'SELECT FOUND_ROWS() count;' )->row()->count;

             return $res;
         }
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }

     }



 }
