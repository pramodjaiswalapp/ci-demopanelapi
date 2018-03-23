<?php

 class Dashboard_Model extends CI_Model {

     public function __construct() {
         parent::__construct();
         $this->load->database();

     }



     /**
      * @function get_yearly_data
      * @description To fetch number of users' registration from last 10 years
      *
      * @param array $data Required data to fetch details
      * @return array user registration no
      */
     public function get_yearly_data() {
         $start_year = date( 'Y' );
         $end_year   = $start_year - 10;
         $this->db->select( ' year(registered_date) y, count(user_id) total ' )
             ->group_by( 'year(registered_date)' )
             ->where( 'year(registered_date)<=', $start_year, false )
             ->where( 'year(registered_date)>=', $end_year, false );
         $query      = $this->db->get( "ai_user" );
         $result     = $query->result_array();
         if ( $query->num_rows() ) {
             return $result;
         }
         else {
             return null;
         }

     }



     /**
      *
      * @param type $year
      */
     function get_monthly_data( $year ) {
         $this->db->select( ' month(registered_date) y, count(user_id) total ' )
             ->group_by( 'month(registered_date)' )
             ->where( 'year(registered_date)', $year, false );
         $query  = $this->db->get( "ai_user" );
         $result = $query->result_array();
         if ( $query->num_rows() ) {
             return $result;
         }
         else {
             return null;
         }

     }



     /**
      *
      * @param type $year
      * @param type $month
      */
     function get_weekly_data( $year, $month ) {
         $this->db->select( 'count(registered_date) total,concat(DATE_FORMAT(DATE_ADD(registered_date, INTERVAL(1-DAYOFWEEK(registered_date)) DAY),"%d,%b"),"-", DATE_FORMAT( DATE_ADD(registered_date, INTERVAL(7-DAYOFWEEK(registered_date)) DAY),"%d,%b")) y ' )
             ->group_by( "week(registered_date)" )
             ->where( "year(registered_date)", $year )
             ->where( "month(registered_date)", $month );

         $query  = $this->db->get( "ai_user" );
         $result = $query->result_array();
         if ( $query->num_rows() ) {
             return $result;
         }
         else {
             return null;
         }

     }



 }
