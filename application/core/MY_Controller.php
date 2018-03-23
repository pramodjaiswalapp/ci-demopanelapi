<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

 class MY_Controller extends MX_Controller {

     public function __construct() {
         parent::__construct();
         $this->load->library( "session" );

     }



     /**
      * @funciton loadAdmin
      * @description function to load admin view pages
      *
      * @param string $page view page name to load
      * @param array $data data array to render data on view page
      */
     protected function loadAdmin( $page, $data ) {
         $this->load->view( "admin/adminPanel/template/login_header", $data );
         $this->load->view( "admin/adminPanel/{$page}", $data );
         $this->load->view( "admin/adminPanel/template/footer", $data );

     }



     /**
      * @function loadAdminDashboard
      * @description function to load admin dashboard
      *
      * @param type $page
      * @param type $data
      */
     protected function loadAdminDashboard( $page, $data ) {
         $this->load->view( "admin/adminPanel/template/header_1", $data );
         $this->load->view( "admin/adminPanel/template/leftmenu_1", $data );
         $this->load->view( "admin/adminPanel/template/navbar", $data );
         $this->load->view( "admin/adminPanel/{$page}", $data );
         $this->load->view( "admin/adminPanel/template/ending_footer", $data );

     }



     /**
      * @function loadvendorDashboard
      * @description function to load vendor Dashboard
      *
      * @param type $page
      * @param type $data
      */
     protected function loadvendorDashboard( $page, $data ) {
         $this->load->view( "admin/adminPanel/template/header_1", $data );
         $this->load->view( "admin/adminPanel/template/leftmenu_1", $data );
         $this->load->view( "admin/adminPanel/template/navbar", $data );
         $this->load->view( "admin/Vendor_Management/{$page}", $data );
         $this->load->view( "admin/adminPanel/template/ending_footer", $data );

     }



 }
