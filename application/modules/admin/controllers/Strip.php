<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

 class Strip extends MY_Controller {

     /*
      * Published Key Of STRIP
      * Generated After login Strip Account
      */

     #private $publishable_key = "pk_test_dZRfM3sL3ASPkqkXT4eyKphh";


     /*
      * Secret Key "TOKEN", Generated Using card or account details
      * Provoided in API from app end
      * to be use
      */
     #private $scret_key = "sk_test_gFwLId8VnJPq74OULZ5K0BQy";

     function __construct() {
         parent::__construct();
         $this->load->helper( ['url', 'form', 'custom_cookie'] );
         $this->load->model( 'Common_model' );
         $this->load->library( 'session' );
         $this->lang->load( 'common', "english" );
         $this->load->library( 'form_validation' );
         $sessionData = validate_admin_cookie( 'rcc_appinventiv', 'admin' );

         if ( $sessionData ) {
             $this->session->set_userdata( 'admininfo', $sessionData );
         }

         $this->admininfo = $this->session->userdata( 'admininfo' );

         if ( $this->admininfo ) {
             redirect( base_url()."admin/Dashboard" );
         }

     }



     function index() {
         echo "Under process";

     }



     public function addCustomer() {

     }



 }
