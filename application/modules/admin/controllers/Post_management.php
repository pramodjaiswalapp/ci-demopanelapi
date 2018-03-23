<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

 class Post_management extends MY_Controller {

     private $data = array ();

     function __construct() {
         parent::__construct();
         $this->load->helper( ['url', 'custom_cookie', 'form', 'encrypt_openssl'] );
         $this->load->model( 'Common_model' );
         $this->load->model( 'Admin_Model' );
         $this->load->model( 'Post_model' );
         $this->load->library( 'session' );

         $sessionData = validate_admin_cookie( 'rcc_appinventiv', 'admin' );
         if ( $sessionData ) {
             $this->session->set_userdata( 'admininfo', $sessionData );
         }
         $this->admininfo = $this->session->userdata( 'admininfo' );
         $this->lang->load( 'common', "english" );

         $this->admininfo = $this->session->userdata( 'admininfo' );

         if ( empty( $this->admininfo ) ) {
             redirect( base_url().'admin' );
         }
         $this->data              = [];
         $this->data['admininfo'] = $this->admininfo;

     }



     /**
      * @name index
      * @description This method is used to list all the customers.
      */
     public function index() {
         $role_id                      = $this->admininfo['role_id'];
         /*
          * If logged user is sub admin check for his permission
          */
         $defaultPermission['addp']    = 1;
         $defaultPermission['editp']   = 1;
         $defaultPermission['deletep'] = 1;
         if ( $role_id != 1 ) {
             $whereArr          = [];
             $whereArr['where'] = array ('admin_id' => $this->admininfo['admin_id'], 'access_permission' => 2, 'status' => 1);
             $access_detail     = $this->Common_model->fetch_data( 'sub_admin', ['addp', 'editp', 'deletep'], $whereArr, true );
         }
         $this->data['accesspermission'] = ($role_id == 2) ? $access_detail : $defaultPermission;
         $this->data['admininfo']        = $this->admininfo;

         /* Fetch List of users */
         $get = $this->input->get();
         $get = is_array( $get ) ? $get : array ();

         $page                = (isset( $get['per_page'] ) && !empty( $get['per_page'] )) ? $get['per_page'] : 1;
         $this->data['limit'] = $limit               = (isset( $get['pagecount'] ) && !empty( $get['pagecount'] )) ? $get['pagecount'] : 10;
         $searchlike          = (isset( $get['searchlike'] ) && !empty( $get['searchlike'] )) ? (trim( $get['searchlike'] )) : "";

         $params               = [];
         $params['searchlike'] = $searchlike;
         $this->data['page']   = $page;
         $offset               = ($page - 1) * $limit;
         $this->data['offset'] = $offset;

         $this->data['versions']   = $this->Post_model->postlist( '', $offset, $limit, $params );
         $totalrows                = $this->data['versions']['total'];
         /* paggination */
         $pageurl                  = 'post-management/index';
         $per_page                 = 1;
         $this->data["link"]       = $this->Admin_Model->paginaton_link_custom( $totalrows, $pageurl, $limit, $per_page );
         $this->data['searchlike'] = $searchlike;
         $this->data["totalrows"]  = $totalrows;

         load_views( "post-management/index", $this->data );

     }



     /**
      * @name postDetails
      * @description This method is used to Show post details.
      */
     public function postDetails() {
         $role_id                      = $this->admininfo['role_id'];
         /*
          * If logged user is sub admin check for his permission
          */
         $defaultPermission['addp']    = 1;
         $defaultPermission['editp']   = 1;
         $defaultPermission['deletep'] = 1;
         if ( $role_id != 1 ) {
             $whereArr          = [];
             $whereArr['where'] = array ('admin_id' => $this->admininfo['admin_id'], 'access_permission' => 2, 'status' => 1);
             $access_detail     = $this->Common_model->fetch_data( 'sub_admin', ['addp', 'editp', 'deletep'], $whereArr, true );
         }
         $this->data['accesspermission'] = ($role_id == 2) ? $access_detail : $defaultPermission;
         $this->data['admininfo']        = $this->admininfo;
         load_views( "post-management/postDetails", $this->data );

     }



 }
