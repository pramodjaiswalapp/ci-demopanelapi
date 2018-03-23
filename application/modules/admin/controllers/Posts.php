<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

 class Posts extends MY_Controller {

     private $data = array ();

     function __construct() {
         parent::__construct();
         $this->load->model( 'Post_model' );
         $this->load->library( 'session' );

         $this->admininfo = $this->session->userdata( 'admininfo' );
         $this->lang->load( 'common', "english" );
         $this->csrftoken = $this->security->get_csrf_hash();

     }



     /**
      * @name index
      * @description This method is used to list all the customers.
      */
     public function index() {

         $data = array ();

         $adminInfo = $this->admininfo;
         $role_id   = $adminInfo['role_id'];

         //If logged user is sub admin check for his permission
         $defaultPermission['viewp']   = 1;
         $defaultPermission['blockp']  = 1;
         $defaultPermission['deletep'] = 1;

         if ( $role_id != 1 ) {
             $whereArr          = [];
             $whereArr['where'] = array ('admin_id' => $adminInfo['admin_id'], 'access_permission' => 1, 'status' => 1);
             $access_detail     = $this->Common_model->fetch_data( 'sub_admin', ['viewp', 'blockp', 'deletep'], $whereArr, true );
         }

         $data['accesspermission'] = ($role_id == 2) ? $access_detail : $defaultPermission;
         $this->load->library( 'commonfn' );



         $get = $this->input->get();
         $get = is_array( $get ) ? $get : array ();


         $default = array (
             "limit"      => 10,
             "page"       => 1,
             "startDate"  => "",
             "endDate"    => "",
             "searchlike" => "",
             "status"     => "",
             "country"    => "",
             "export"     => "",
             "field"      => "",
             "order"      => ""
         );

         $defaultValue = defaultValue( $get, $default );

         $limit      = (isset( $get['limit'] ) && !empty( $get['limit'] )) ? $get['limit'] : 10;
         $page       = (isset( $get['page'] ) && !empty( $get['page'] )) ? $get['page'] : 1;
         $startDate  = isset( $get['startDate'] ) ? $get['startDate'] : '';
         $endDate    = isset( $get['endDate'] ) ? $get['endDate'] : '';
         $searchlike = (isset( $get['searchlike'] ) && !empty( $get['searchlike'] )) ? (trim( $get['searchlike'] )) : "";
         $status     = (isset( $get['status'] ) && !empty( $get['status'] )) ? (trim( $get['status'] )) : "";
         $isExport   = (isset( $get['export'] ) && !empty( $get['export'] )) ? $get['export'] : "";

         $params               = [];
         $params['user_id']    = $get['id'];
         $params['searchlike'] = $defaultValue['searchlike'];
         $params["sortfield"]  = $defaultValue['field'];
         $params["sortby"]     = $defaultValue['order'];
         $params["export"]     = $defaultValue['export'];


         //If Request if Excel Export then restrict to 65000 limit
         if ( $isExport ) {
             $params['limit']  = 65000;
             $params['offset'] = 0;
         }
         else {
             $offset           = ($defaultValue['page'] - 1) * $defaultValue['limit'];
             $params['limit']  = $defaultValue['limit'];
             $params['offset'] = $offset;
         }


         $userInfo = $this->Post_model->postlist( $offset, $defaultValue['limit'], $params );

         //Export to Csv
         if ( $isExport ) {
             $this->exportUser( $userInfo['result'] );
         }

         $totalrows        = $userInfo['total'];
         $data['postlist'] = $userInfo['result'];


         // Manage Pagination
         $pageurl                = 'admin/users';
         $this->data["link"]     = $this->commonfn->pagination( $pageurl, $totalrows, $limit );
         $this->data["order_by"] = "asc";
         if ( !empty( $params['sortby'] ) ) {
             $this->data["order_by"] = $params["sortby"] == "desc" ? "asc" : "desc";
         }

         //unset sortfields
         unset( $params["sortby"] );


         // build query to append it to sort url
         $getQuery = http_build_query( array_filter( $params ) );

         $data['filterVal'] = !empty( $this->get ) ? $this->get : ( object ) array ();
         $data['get_query'] = !empty( $getQuery ) ? "&".$getQuery : "";

         // CSRF token
         $data["csrfName"]  = $this->security->get_csrf_token_name();
         $data["csrfToken"] = $this->security->get_csrf_hash();

         $data['searchlike'] = $searchlike;
         $data['page']       = $page;
         $data['startDate']  = $startDate;
         $data['endDate']    = $endDate;
         $data['status']     = $status;
         $data['limit']      = $limit;
         $data['totalrows']  = $totalrows;
         $data['admininfo']  = $adminInfo;

         load_views( "postManagement/index", $data );

     }



     /**
      * @name postDetails
      * @description This method is used to Show post details.
      */
     public function postDetails() {
         $this->data['admininfo'] = $this->admininfo;
         load_views( "postManagement/postDetails", $this->data );

     }



     /**
      *
      */
     public function getPost() {

         if ( $this->input->is_ajax_request() ) {
             $post = $this->input->post();

             $data['responce']  = $this->Post_model->getPostGalerryMedia( $post );
             $data["csrfName"]  = $this->security->get_csrf_token_name();
             $data["csrfToken"] = $this->security->get_csrf_hash();
             echo json_encode( $data, true );
         }
         else {
             show404();
         }

     }



 }
