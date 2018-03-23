<?php

 class Subscription_model extends CI_Model {

     //check whether the email is exist in the database or not
     function is_email_available( $email ) {
         $this->db->where( 'email', $email );
         $query = $this->db->get( "oe_admins" );
         if ( $query->num_rows() > 0 ) {
             return true;
         }
         else {
             return false;
         }

     }



     /**
      * @function getSubscriptionsData
      * @description fetch the data according to search the data of subscriptions according to admin search
      *
      * @param int $offset To set offset in MySql Query. E.g : select * from xxxx limit offset, limit
      * @param int $limit To set number of Rows at a time
      * @param array $params An array of parameters to filter out CMS content list
      * @return array $res An array of fetched result
      */
     public function getSubscriptionsData( $limit, $offset, $params ) {

         $this->db->select( 'SQL_CALC_FOUND_ROWS *', false );
         $this->db->from( 'ai_subscriptions as a' );

         if ( !empty( $params['searchlike'] ) ) {
             $this->db->group_start();
             $this->db->like( 'a.subscription_name', $params['searchlike'], 'after' );
             $this->db->group_end();
         }

         // $this->db->where('status', ACTIVE);

         $this->db->limit( $limit, $offset );
         $query = $this->db->get();

         $respdata              = array ();
         $respdata['totalrows'] = $this->db->query( 'SELECT FOUND_ROWS() count;' )->row()->count;
         $respdata['records']   = $query->result_array();
         return $respdata;

     }



     /**
      * @function delete_data
      * @description delete the records
      *
      * @param int $userId id to delete a particular record
      */
     public function delete_data( $userId ) {
         $this->db->where( 'userId', $userId );
         $this->db->delete( 'oe_admins' );

     }



     /**
      * @function update
      * @description update the records
      *
      * @param string $table table name to perform update action
      * @param type $data
      * @param type $where
      * @return boolean
      */
     public function update( $table, $data, $where ) {
         $this->db->where( $where );
         $this->db->update( $table, $data );
         if ( $this->db->affected_rows() > 0 ) {
             return true;
         }
         else {
             return false;
         }

     }



 }

?>
