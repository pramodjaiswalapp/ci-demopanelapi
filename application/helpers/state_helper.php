<?php
 if ( !defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );
 if ( !function_exists( 'get_state_list' ) ) {

     function get_state_list() {

         $obj       = & get_instance();
         $obj->load->model( 'Common_model', 'Common' );
         $stateList = $obj->Common->fetch_data( 'states', '*' );
         return $stateList;

     }



 }


 if ( !function_exists( 'get_country_list' ) ) {

     function get_country_list() {

         $obj         = & get_instance();
         $obj->load->model( 'Common_model', 'Common' );
         $countryList = $obj->Common->fetch_data( 'countries', '*' );
         return $countryList;

     }



 }
?>
