<?php

 class Version_Model extends CI_Model {

     public $finalrole = array ();

     public function __construct() {
         $this->load->database();

     }



     //--------------------------------------------------------------------------
     /**
      * @name userlist
      * @description Used to filter the users
      *
      * @param int $offset To set offset in MySql Query. E.g : select * from xxxx limit offset, limit
      * @param int $limit To set number of Rows at a time
      * @param array $params An array of parameters to filter out CMS content list
      * @return array $res An array of fetched result
      */
     public function versionlist( $where = '', $offset = null, $limit = null, $params = [] ) {

         $orderByMap = [
             "added" => "create_date",
             "name"  => "version_name",
             "title" => "versiob_title",
         ];

         $this->db->select( "SQL_CALC_FOUND_ROWS a.*", False );
         $this->db->from( 'app_version a' );
         if ( $where ) {
             $this->db->where( $where );
         }

         /* order by */
         if ( (isset( $params["field"] ) && !empty( $params["field"] ) && in_array( $params["field"], array_keys( $orderByMap ) ) ) &&
             (isset( $params["order"] ) && !empty( $params["order"] )) ) {
             $this->db->order_by( $orderByMap[$params["field"]], $params["order"] );
         }
         else {//default Order by on created date desc
             $this->db->order_by( "a.create_date", "DESC" );
         }
         /* order by end */

         /* setting LImit */
         if ( ( int ) $limit >= 0 && ( int ) $offset >= 0 ) {
             $this->db->limit( $limit, $offset );
         }

         /* setting search */
         if ( !empty( $params['searchlike'] ) ) {
             $this->db->group_start();
             $this->db->like( 'version_name', $params['searchlike'] );
             $this->db->or_like( 'versiob_title', $params['searchlike'] );
             $this->db->group_end();
         }

         $query         = $this->db->get();
         $res['result'] = $query->result_array();
         $res['total']  = $this->db->query( 'SELECT FOUND_ROWS() count;' )->row()->count;
         return $res;

     }



 }
