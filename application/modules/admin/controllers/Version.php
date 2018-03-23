<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

 class Version extends MY_Controller {

     function __construct() {
         parent::__construct();
         $this->admininfo = $this->session->userdata( 'admininfo' );
         $this->load->model( 'Version_Model' );
         $this->lang->load( 'common', "english" );

     }



     /**
      * @name index
      * @description This method is used to list all the customers.
      */
     public function index() {

         try {//TRY START
             $this->load->library( 'commonfn' );

             $data['admininfo'] = $this->admininfo;

             $get = $this->input->get();

             $default      = array (
                 "limit"      => 10,
                 "page"       => 1,
                 "searchlike" => "",
                 "field"      => "",
                 "order"      => ""
             );
             $defaultValue = defaultValue( $get, $default );


             $data['page']   = $defaultValue['page'];
             $data['offset'] = ($defaultValue['page'] - 1) * $defaultValue['limit'];

             $data['versions'] = $this->Version_Model->versionlist( '', $data['offset'], $defaultValue['limit'], $defaultValue );
             $totalrows        = $data['versions']['total'];

             #IF user is on other than First page, having only one element
             #IF last row is deleted by user
             #than page will redirected to previous page
             if ( !$data['versions']['result'] && $defaultValue['page'] > 1 ) {
                 $defaultValue['page'] = ( string ) ($defaultValue['page'] - 1);
                 redirect( base_url()."admin/version?data=".queryStringBuilder( $defaultValue ) );
             }


             /* Sorting Query */
             $getQuery          = http_build_query( array_filter( ["limit" => $defaultValue['limit'], "page" => $defaultValue['page']] ) );
             $data['get_query'] = "&".$getQuery;

             $data["order_by_title"] = $data["order_by_name"]  = $data["order_by_date"]  = "sorting";

             //Default Order by
             $data["order_by"] = "asc";

             if ( !empty( $defaultValue['order'] ) ) {//IF 1 START
                 $data["order_by"] = $defaultValue["order"] == "desc" ? "asc" : "desc";
                 if ( !empty( $defaultValue["field"] ) ) {
                     switch ( trim( $defaultValue["field"] ) ) {
                         case "added":
                             $data["order_by_date"]  = $defaultValue["order"] == "desc" ? "sort-descending" : "sort-ascending";
                             break;
                         case "name":
                             $data["order_by_name"]  = $defaultValue["order"] == "desc" ? "sort-descending" : "sort-ascending";
                             break;
                         case "title":
                             $data["order_by_title"] = $defaultValue["order"] == "desc" ? "sort-descending" : "sort-ascending";
                             break;
                     }
                 }
             }//IF 1 END

             /* paggination */
             $pageurl            = 'admin/version';
             $links              = $this->commonfn->pagination( $pageurl, $totalrows, $defaultValue['limit'] );
             $data["link"]       = $links;
             $data['searchlike'] = $defaultValue['searchlike'];

             /* CSRF token */
             $data["csrfName"]   = $this->security->get_csrf_token_name();
             $data["csrfToken"]  = $this->security->get_csrf_hash();
             $data["totalrows"]  = $totalrows;
             $data['page']       = $defaultValue['page'];
             $data['limit']      = $defaultValue['limit'];
             $data['controller'] = $this->router->fetch_class();
             $data['method']     = $this->router->fetch_method();
             $data['module']     = $this->router->fetch_module();

             if ( !$GLOBALS['permission'] ) {
                 setDefaultPermission();
             }

             $data['permission'] = $GLOBALS['permission'];

             load_views( "version/index", $data );
         }//TRY END
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }

     }



     /**
      * @name add
      * @description This method is used to add a new app version to the admin.
      */
     public function add() {
         //TRY START
         try {
             $data              = array ();
             $adminInfo         = $this->admininfo;
             $data['admininfo'] = $adminInfo;

             $post_array = $this->input->post();
             if ( count( $post_array ) ) {//IF 1
                 $this->load->library( 'form_validation' );
                 $this->form_validation->set_rules( 'name', $this->lang->line( 'version_name' ), 'trim|required' );
                 $this->form_validation->set_rules( 'title', $this->lang->line( 'version_title' ), 'trim|required' );
                 $this->form_validation->set_rules( 'desc', $this->lang->line( 'description' ), 'trim|required' );
                 $this->form_validation->set_rules( 'platform', $this->lang->line( 'platform' ), 'trim|required' );
                 $this->form_validation->set_rules( 'update_type', $this->lang->line( 'update_type' ), 'trim|required' );
                 $this->form_validation->set_rules( 'current_version', $this->lang->line( 'current_version' ), 'trim|required' );

                 if ( $this->form_validation->run() ) {
                     $saveData = array (
                         'version_name'   => $post_array['name'],
                         'versiob_title'  => $post_array['title'],
                         'version_desc'   => $post_array['desc'],
                         'platform'       => $post_array['platform'],
                         'update_type'    => $post_array['update_type'],
                         'is_cur_version' => $post_array['current_version'],
                         'create_date'    => DEFAULT_DB_DATE_TIME_FORMAT
                     );

                     // call to insert data into db
                     $res = $this->saveVersionData( $saveData );

                     $alertMsg = array ();

                     if ( $res ) {//IF 3
                         $alertMsg['text'] = $this->lang->line( 'version_added' );
                         $alertMsg['type'] = $this->lang->line( 'success' );
                         $this->session->set_flashdata( 'alertMsg', $alertMsg );
                     }//IF 3 END
                     else {
                         $alertMsg['text'] = $this->lang->line( 'try_again' );
                         $alertMsg['type'] = $this->lang->line( 'error' );
                         $this->session->set_flashdata( 'alertMsg', $alertMsg );
                     }//ELSE END
                     redirect( 'admin/version/' );
                 }//ELSE END
             }//IF 1 END
             load_views( "version/add", $data );
         }//TRY END
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }//CATCH END

     }



     /**
      * @name edit
      * @description This method is used to add a new Edit/Update version.
      */
     public function edit() {

         try {//TRY START
             $adminInfo = $this->admininfo;
             $get       = $this->input->get();

             $data               = array ();
             $data['admininfo']  = $adminInfo;
             $data['version_id'] = $versionId          = (isset( $get['id'] ) && !empty( $get['id'] )) ? $get['id'] : show404();
             $data['version']    = $this->Common_model->fetch_data( 'app_version', '*', ['where' => ['vid' => $versionId]], true );

             if ( empty( $data['version'] ) && $data['version'] == array () ) {//IF 2
                 show404();
             }//IF 2 END
             //Form posted
             $post_array = $this->input->post();
             if ( count( $post_array ) ) {//IF 3
                 //Loading Form validation Library
                 $this->load->library( 'form_validation' );

                 $this->form_validation->set_rules( 'name', $this->lang->line( 'version_name' ), 'trim|required' );
                 $this->form_validation->set_rules( 'title', $this->lang->line( 'version_title' ), 'trim|required' );
                 $this->form_validation->set_rules( 'desc', $this->lang->line( 'description' ), 'trim|required' );
                 $this->form_validation->set_rules( 'platform', $this->lang->line( 'platform' ), 'trim|required' );
                 $this->form_validation->set_rules( 'update_type', $this->lang->line( 'update_type' ), 'trim|required' );
                 $this->form_validation->set_rules( 'current_version', $this->lang->line( 'current_version' ), 'trim|required' );

                 // IF Form validation
                 if ( $this->form_validation->run() ) {

                     $saveData = array (
                         'version_name'   => $post_array['name'],
                         'versiob_title'  => $post_array['title'],
                         'version_desc'   => $post_array['desc'],
                         'platform'       => $post_array['platform'],
                         'update_type'    => $post_array['update_type'],
                         'is_cur_version' => $post_array['current_version'],
                         'create_date'    => DEFAULT_DB_DATE_TIME_FORMAT
                     );

                     // call to insert data into db
                     $res = $this->saveVersionData( $saveData, $versionId );

                     if ( $res ) {//INNER IF 2 START
                         $alertMsg['text'] = $this->lang->line( 'version_updated' );
                         $alertMsg['type'] = $this->lang->line( 'success' );
                         $this->session->set_flashdata( 'alertMsg', $alertMsg );
                     }
                     else {
                         $alertMsg['text'] = $this->lang->line( 'try_again' );
                         $alertMsg['type'] = $this->lang->line( 'error' );
                         $this->session->set_flashdata( 'alertMsg', $alertMsg );
                     }//INNER IF 2 END

                     redirect( 'admin/version/' );
                 }//INNDER IF 1 END
             }//IF 3 END
             load_views( "version/edit", $data );
         }//TRY END
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }//CATCH END

     }



     /**
      * @function saveVersionData
      * @description to save/update version information in DB
      *
      * @param type $data
      * @param type $updateId
      * @return boolean
      */
     private function saveVersionData( $data, $updateId = false ) {

         //TRY START
         try {
             $this->db->trans_start();
             if ( $updateId ) {//IF 1
                 $this->Common_model->update_single( 'app_version', $data, ['where' => ['vid' => $updateId]] );
             }//IF 1 END
             else {
                 $updateId = $this->Common_model->insert_single( 'app_version', $data );
             }

             if ( isset( $data['is_cur_version'] ) && YES == $data['is_cur_version'] ) {//IF 2
                 $this->Common_model->update_single( 'app_version', ['is_cur_version' => NO], ['where' => ['vid !=' => $updateId, 'platform' => $data['platform']]] );
             }//IF 2 END

             if ( TRUE === $this->db->trans_status() ) {//IF 3
                 $this->db->trans_commit();
                 return true;
             }//IF 3 END
             else {
                 $this->db->trans_rollback();
                 return false;
             }
         }//TRY END
         catch ( Exception $e ) {
             $this->db->trans_rollback();
             showException( $e->getMessage() );
             exit;
         }//CATCH END

     }



 }
