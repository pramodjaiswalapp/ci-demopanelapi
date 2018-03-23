<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

 class Logout extends MX_Controller {

     public function __construct() {
         $this->load->model( "Common_model" );
         $this->load->helper( ['url', 'cookie'] );
         $this->load->library( "session" );

     }



     /**
      * @function:logout
      * @param:N/A;
      * @description:Logout the user
      */
     public function index() {
         delete_cookie( "rcc_appinventiv" );
         $this->session->sess_destroy();
         redirect( base_url().'admin/Admin' );

     }



 }