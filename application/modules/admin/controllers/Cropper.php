<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

 class Cropper extends MY_Controller {

     private $admininfo = NULL;
     private $onS3      = false;

     function __construct() {
         parent::__construct();
         $this->admininfo = $this->session->userdata( 'admininfo' );
         $this->load->model( 'Cms_Model' );
         $this->lang->load( 'common', "english" );

     }



     /**
      * @funciton index
      * @description Function to load image cropper
      */
     public function index() {
         $data["csrfName"]  = $this->security->get_csrf_token_name();
         $data["csrfToken"] = $this->security->get_csrf_hash();
         $data['admininfo'] = $this->admininfo;
         load_views( "cropper/cropper", $data );

     }



     /**
      * @function saveImage
      * @description to save image on server OR on S3 server
      *
      */
     public function saveImage() {
         if ( $this->onS3 ) {
             $this->save_on_s3();
         }
         else {
             $this->save_on_own_server();
         }

     }



     /**
      * @function save_on_s3
      * @description function to save image on Amazon S3 server
      */
     private function save_on_s3() {

     }



     /**
      * @function save_on_own_server
      * @description to save image on our own server as on demand
      */
     private function save_on_own_server() {

     }



 }