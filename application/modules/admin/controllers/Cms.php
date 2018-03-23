<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

 class Cms extends MY_Controller {

     private $admininfo = NULL;

     function __construct() {
         parent::__construct();
         $this->admininfo = $this->session->userdata( 'admininfo' );
         $this->load->model( 'Cms_Model' );
         $this->lang->load( 'common', "english" );

     }



     /**
      * @name index
      * @description This method is used to list all the CMS Content.
      */
     public function index() {

         try {//TRY START
             $this->load->library( 'commonfn' );

             $get = $this->input->get();

             $default = array (
                 "limit"      => 10,
                 "page"       => 1,
                 "searchlike" => "",
                 "field"      => "",
                 "order"      => ""
             );

             $defaultValue               = defaultValue( $get, $default );
             $defaultValue['searchlike'] = trim( $defaultValue['searchlike'] );

             $offset = ($defaultValue['page'] - 1) * $defaultValue['limit'];

             // Content List fetching
             $respData = $this->Cms_Model->pagelist( $defaultValue['limit'], $offset, $defaultValue );

             //Manage pagination
             $pageurl            = 'admin/cms';
             $data["link"]       = $this->commonfn->pagination( $pageurl, $respData['total'], $defaultValue['limit'] );
             $data['page']       = $defaultValue['page'];
             $data['limit']      = $defaultValue['limit'];
             $data['searchlike'] = $defaultValue['searchlike'];
             $data['cmsData']    = $respData['result'];
             $data['totalrows']  = $respData['total'];

             #IF user is on other than First page, having only one element
             #IF last row is deleted by user
             #than page will redirected to previous page
             if ( !$respData['result'] && $defaultValue['page'] > 1 ) {
                 $defaultValue['page'] = ( string ) ($defaultValue['page'] - 1);
                 redirect( base_url()."admin/cms?data=".queryStringBuilder( $defaultValue ) );
             }


             $getQuery          = http_build_query( array_filter( ["limit" => $defaultValue['limit'], "page" => $defaultValue['page']] ) );
             $data['get_query'] = "&".$getQuery;

             $data["order_by_date"] = "sorting";

             //Default Order by
             $data["order_by"] = "asc";

             if ( !empty( $defaultValue['order'] ) ) {//IF 1 START
                 $data["order_by"] = $defaultValue["order"] == "desc" ? "asc" : "desc";
                 if ( !empty( $defaultValue["field"] ) ) {
                     switch ( trim( $defaultValue["field"] ) ) {
                         case "added":
                             $data["order_by_date"] = $defaultValue["order"] == "desc" ? "sort-descending" : "sort-ascending";
                             break;
                     }
                 }
             }//IF 1 END


             /* CSRF token */
             $data["csrfName"]  = $this->security->get_csrf_token_name();
             $data["csrfToken"] = $this->security->get_csrf_hash();
             $data['admininfo'] = $this->admininfo;

             if ( !$GLOBALS['permission'] ) {
                 setDefaultPermission();
             }

             $data['permission'] = $GLOBALS['permission'];

             $controller = $this->router->fetch_class();
             $method     = $this->router->fetch_method();
             $module     = $this->router->fetch_module();

             $data['pageUrl'] = base_url().$module.'/'.strtolower( $controller ).'/'.$method;


             load_views( "cms/index", $data );
         }//TRY END
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }//CATCH END

     }



     /**
      * @name add
      * @description This method is used to add a new page to the cms.
      */
     public function add() {

         try {//TRY START
             $postedData        = $this->input->post();
             $data['admininfo'] = $this->admininfo;

             if ( count( $postedData ) ) {//IF 1
                 $this->form_validation->set_rules( 'title', $this->lang->line( 'title' ), 'required|trim' );
                 $this->form_validation->set_rules( 'page_desc', $this->lang->line( 'page_desc' ), 'required' );
                 $this->form_validation->set_rules( 'status', $this->lang->line( 'status' ), 'required|trim' );

                 if ( $this->form_validation->run() ) {//if 2
                     $savedata['name']         = $postedData['title'];
                     $savedata['content']      = $postedData['page_desc'];
                     $savedata['status']       = $postedData['status'];
                     $savedata['created_date'] = DEFAULT_DB_DATE_TIME_FORMAT;

                     // calling to insert data method.
                     $res = $this->saveCmsData( $savedata );

                     $alertMsg = [];
                     if ( $res ) {//IF 3
                         $alertMsg['text'] = $this->lang->line( 'page_added' );
                         $alertMsg['type'] = $this->lang->line( 'success' );
                         $this->session->set_flashdata( 'alertMsg', $alertMsg );
                     }//IF 3 END
                     else {
                         $alertMsg['text'] = $this->lang->line( 'try_again' );
                         $alertMsg['type'] = $this->lang->line( 'error' );
                         $this->session->set_flashdata( 'alertMsg', $alertMsg );
                     }//ELSE END

                     redirect( '/admin/cms' );
                 }//if END
             }//IF 1 END
             else {
                 // CSRF token
                 $data["csrfName"]  = $this->security->get_csrf_token_name();
                 $data["csrfToken"] = $this->security->get_csrf_hash();
             }//ELSE END

             load_views( "cms/add", $data );
         }//TRY END
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }

     }



     /**
      * @name saveCmsData
      * @descrition To insert/update CMS page data description.
      *
      * @param type $this->data
      * @return boolean
      */
     private function saveCmsData( $data, $updateId = false ) {

         try {//TRY START
             $this->db->trans_start(); //transaction Start

             if ( $updateId ) {//IF 1
                 $this->Common_model->update_single( 'page_master', $data, ['where' => ['id' => $updateId]] );
             }//IF 1 END
             else {
                 $this->Common_model->insert_single( 'page_master', $data );
             }//ELSE END


             if ( TRUE === $this->db->trans_status() ) { //IF 2
                 //Commiting Trasaction
                 $this->db->trans_commit();
                 return true;
             }//IF 2 END
             else {
                 //Trasaction Rollback
                 $this->db->trans_rollback();
                 return false;
             }//ELSE END
         }//TRY END
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }//CATCH END

     }



     /**
      * @name edit
      * @description This method is used to edit the cms page.
      *
      * @access public
      */
     public function edit() {

         try {//TRY START
             $get               = $this->input->get();
             $data['admininfo'] = $this->admininfo;

             $pageId          = (isset( $get['id'] ) && !empty( $get['id'] )) ? $get['id'] : show404();
             $data['page_id'] = $pageId;
             $data['pages']   = $this->Common_model->fetch_data( 'page_master', '*', ['where' => ['id' => $pageId]], true );

             //IF no data return then Display 404 page
             if ( empty( $data['pages'] ) && array () == $data['pages'] ) {//IF 1
                 show404();
             }//IF 1 END

             $postedData = $this->input->post();

             if ( count( $postedData ) ) {//IF 2
                 $this->form_validation->set_rules( 'title', $this->lang->line( 'title' ), 'required|trim' );
                 $this->form_validation->set_rules( 'page_desc', $this->lang->line( 'page_desc' ), 'required' );
                 $this->form_validation->set_rules( 'status', $this->lang->line( 'status' ), 'required|trim' );

                 if ( $this->form_validation->run() ) {//IF START
                     $savedata['name']         = $postedData['title'];
                     $savedata['content']      = $postedData['page_desc'];
                     $savedata['status']       = $postedData['status'];
                     $savedata['created_date'] = DEFAULT_DB_DATE_TIME_FORMAT;

                     // calling to update data method.
                     $res = $this->saveCmsData( $savedata, $pageId );

                     if ( $res ) {//IF 4
                         $alertMsg['text'] = $this->lang->line( 'page_updated' );
                         $alertMsg['type'] = $this->lang->line( 'success' );
                         $this->session->set_flashdata( 'alertMsg', $alertMsg );
                     }//IF 4 END
                     else {
                         $alertMsg['text'] = $this->lang->line( 'try_again' );
                         $alertMsg['type'] = $this->lang->line( 'error' );
                         $this->session->set_flashdata( 'alertMsg', $alertMsg );
                     }//ELSE END
                     redirect( '/admin/cms' );
                 }//IF END
             }// IF 2 END
             else {
                 // CSRF token
                 $data["csrfName"]  = $this->security->get_csrf_token_name();
                 $data["csrfToken"] = $this->security->get_csrf_hash();
             }

             load_views( "cms/edit", $data );
         }//TRY END
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }//CATCH END

     }



 }
