<?php

 class Post_model extends CI_Model {

     public $finalrole = array ();

     public function __construct() {
         $this->load->database();

     }



     /**
      * Fetch filtered/all Post lists
      *
      * @name postlist
      * @description Used to filter the Posts List
      *
      * @param int $offset To set offset in MySql Query. E.g : select * from xxxx limit offset, limit
      * @param int $limit To set number of Rows at a time
      * @param array $params An array of parameters to filter out CMS content list
      * @return array $res An array of fetched result
      */
     public function postlist( $offset, $limit, $params ) {
         $this->db->select( "SQL_CALC_FOUND_ROWS post.*", False );
         $this->db->from( 'ai_post as post' );

         $this->db->where( "post.user_id", $params['user_id'] );
         $this->db->limit( $limit, $offset );

         $query         = $this->db->get();
         #echo $this->db->last_query();
         $res['result'] = $query->result_array();
         $res['total']  = $this->db->query( 'SELECT FOUND_ROWS() count;' )->row()->count;
         return $res;

     }



     function getPostGalerryMedia( $post ) {

         $query = $this->db->select( "url" )
             ->from( "ai_media" )
             ->where( "post_id", $post['post_id'] )
             ->get();

         if ( $query->num_rows() ) {
             return $query->result_array();
         }
         else {
             return NULL;
         }

     }



 }
