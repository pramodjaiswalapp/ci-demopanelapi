<?php

 class Follow_model extends CI_Model {

     public function __construct() {
         parent::__construct();
         /**
          * load database
          */
         $this->load->database();

     }



     /**
      * get review list
      * @param array
      * @return array
      */
     public function getList( $params ) {
         /*
          * req type 1 get followers and 2 get following
          */
         $this->db->select( 'SQL_CALC_FOUND_ROWS user_id,CONCAT(first_name," ",last_name) as name', false );
         $this->db->from( 'ai_follows as f' );
         if ( $params['req_type'] == 1 ) {
             $this->db->join( 'ai_user as u', '(f.receiver_id='.$params['user_id'].' AND f.sender_id=u.user_id)', 'left' );
         }
         else if ( $params['req_type'] == 2 ) {
             $this->db->join( 'ai_user as u', '(f.receiver_id=u.user_id AND f.sender_id='.$params['user_id'].')', 'left' );
         }
         $this->db->where( 'u.user_id != '.$params['user_id'].'' );

         if ( !empty( $params['searchlike'] ) ) {
             $this->db->like( 'u.first_name', $params['searchlike'] );
         }
         $this->db->limit( $params['limit'], $params['offset'] );
         $query            = $this->db->get();
         $resArr           = [];
         $resArr['result'] = $query->result_array();
         $resArr['count']  = $this->db->query( 'SELECT FOUND_ROWS() count;' )->row()->count;
         return $resArr;

     }



 }
