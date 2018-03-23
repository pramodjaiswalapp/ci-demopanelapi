<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

 class Notfound extends MY_Controller {

     function __construct() {
         parent::__construct();
         $this->load->helper( ['url'] );

     }



     /**
      * @function index
      * @description to call 404 page
      */
     public function index() {
         show404( "We can't seem to find the page you'r looking for." );

     }



     /**
      * @function index
      * @description to call 403 page
      */
     public function show403() {
         show403( "We can't seem to find the page you'r looking for." );

     }



 }
