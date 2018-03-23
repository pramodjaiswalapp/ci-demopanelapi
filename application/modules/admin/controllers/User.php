<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

 class User extends MY_Controller {

     private $admininfo = "";
     private $data      = array ();

     function __construct() {

         parent::__construct();

         $this->admininfo         = $this->session->userdata( 'admininfo' );
         $this->data['admininfo'] = $this->admininfo;
         $this->load->model( 'User_Model' );

     }



     /**
      * @name index
      * @description This method is used to list all the Users.
      */
     public function index() {

         try {
             $get = $this->input->get();
             $this->load->library( 'commonfn' );

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

             $defaultValue              = defaultValue( $get, $default );
             $defaultValue["sortfield"] = trim( $defaultValue["field"] );
             $defaultValue["sortby"]    = trim( $defaultValue["order"] );

             //If Request if Excel Export then restrict to 65000 limit
             if ( $defaultValue['export'] ) {//IF 2 START
                 $defaultValue['limit']  = EXPORT_LIMIT;
                 $defaultValue['offset'] = 0;
             }//IF 2 END
             else {//ELSE 2 START
                 $offset                 = ($defaultValue['page'] - 1) * $defaultValue['limit'];
                 $defaultValue['offset'] = $offset;
             }//ELSE 2 END


             $userInfo = $this->User_Model->userlist( $defaultValue );

             /*
              * Export to Csv
              */
             if ( $defaultValue['export'] ) {//IF 3 START
                 $this->exportUser( $userInfo['result'] );
             }//IF 3 END

             $totalrows        = $userInfo['total'];
             $data['userlist'] = $userInfo['result'];

             // Manage Pagination
             $pageurl               = 'admin/users';
             $data["link"]          = $this->commonfn->pagination( $pageurl, $totalrows, $defaultValue['limit'] );
             $data["order_by"]      = "asc";
             $data["order_by_date"] = $data["order_by_name"] = "sorting";

             if ( !empty( $defaultValue['sortby'] ) ) {//IF 4 START
                 $data["order_by"] = $defaultValue["sortby"] == "desc" ? "asc" : "desc";

                 if ( !empty( $defaultValue["sortfield"] ) ) {//if
                     switch ( $defaultValue["sortfield"] ) {
                         case "name":
                             $data["order_by_name"] = $defaultValue["sortby"] == "desc" ? "sort-descending" : "sort-ascending";
                             break;
                         case "registered":
                             $data["order_by_date"] = $defaultValue["sortby"] == "desc" ? "sort-descending" : "sort-ascending";
                             break;
                     }//switch end
                 }//if end
             }//IF 4 END

             unset( $defaultValue["sortby"] ); //unset sortfields

             $getQuery = http_build_query( array_filter( ["limit" => $defaultValue['limit'], "page" => $defaultValue['page']] ) ); // build query to append it to sort url

             $data['filterVal']  = $defaultValue; #??
             $data['get_query']  = "&".$getQuery;
             $data['searchlike'] = $defaultValue['searchlike'];
             $data['page']       = $defaultValue['page'];
             $data['startDate']  = $defaultValue['startDate'];
             $data['endDate']    = $defaultValue['endDate'];
             $data['status']     = $defaultValue['status'];
             $data['limit']      = $defaultValue['limit'];
             $data['totalrows']  = $totalrows;
             $data['admininfo']  = $this->admininfo;

             $data['controller'] = $this->router->fetch_class();
             $data['method']     = $this->router->fetch_method();
             $data['module']     = $this->router->fetch_module();

             #IF user is on other than First page, having only one element
             #IF last row is deleted by user
             #than page will redirected to previous page
             if ( !$userInfo['result'] && $defaultValue['page'] > 1 ) {
                 $defaultValue['page'] = ( string ) ($defaultValue['page'] - 1);
                 redirect( base_url()."admin/users?data=".queryStringBuilder( $defaultValue ) );
             }

             if ( !$GLOBALS['permission'] ) {
                 setDefaultPermission();
             }

             $data['permission'] = $GLOBALS['permission'];

             load_views( "users/index", $data );
         }
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }

     }



     /**
      *
      * @function detail
      * @description To fetch user details and display it on web
      *
      * @return int 0
      */
     public function detail() {
         try {
             $get = $this->input->get();

             $userId            = (isset( $get['id'] ) && !empty( $get['id'] )) ? $get['id'] : show404( $this->lang->line( 'no_user' ), "/admin/users/" );
             $data              = array ();
             $data['admininfo'] = $this->data['admininfo'];
             $data['user_id']   = $userId;

             //get user profile data
             $data['profile'] = $this->Common_model->fetch_data( 'ai_user', array (), ['where' => ['user_id' => $userId]], true );

             //User Subscription START
             $pageurl = 'users/detail';
             $this->load->library( 'commonfn' );

             $data['searchlike'] = "";
             $params['limit']    = $data['limit']      = 10;
             $data['page']       = 1;
             $data['order']      = "";

             $params['offset']  = ($data['page'] - 1) * $data['limit'];
             $params["user_id"] = $userId;

             //get user Subscription data
             $respdata = $this->Common_model->get_user_subscriptions( $params );


             $data['link']         = $this->commonfn->pagination( $pageurl, $respdata['count'], $data['limit'] );
             $data['data']         = $respdata['data'];
             $data['pagecount']    = $respdata['count'];
             $data['status_array'] = [1 => 'Active', 2 => 'Blocked', 3 => 'Deleted'];

             //User Subscription END

             if ( empty( $data['profile'] ) ) {//IF START
                 show404( $this->lang->line( 'no_user' ), "/admin/users/" );
                 return 0;
             }//IF END

             load_views( "users/user-detail", $data );
         }
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }

     }



     /**
      * @function exportUser
      * @description To export user search list
      *
      * @param type $userData
      */
     public function exportUser( $userData ) {
         try {
             $fileName = 'userlist'.date( 'd-m-Y-g-i-h' ).'.xls';

             // The function header by sending raw excel
             header( "Content-type: application/vnd-ms-excel" );

             // Defines the name of the export file
             header( "Content-Disposition: attachment; filename=".$fileName );

             $format = '<table border="1">'
                 .'<tr>'
                 .'<th width="25%">S.no</th>'
                 .'<th>Name</th>'
                 .'<th>Email</th>'
                 .'<th>Registration Date</th>'
                 .'</tr>';

             $coun = 1;
             foreach ( $userData AS $res ) {

                 $date = date_create( $res['registered_date'] );
                 $Date = date_format( $date, 'd/m/Y' );
                 $Time = date_format( $date, 'g:i A' );

                 $fld_1 = $coun;
                 $fld_2 = (isset( $res['first_name'] ) && ($res['first_name'] != '')) ? $res['first_name'] : '';
                 $fld_3 = (isset( $res['email'] ) && ($res['email'] != '')) ? $res['email'] : '';
                 $fld_4 = $Date.' '.$Time;

                 $format .= '<tr>
                        <td>'.$fld_1.'</td>
                        <td>'.$fld_2.'</td>
                        <td>'.$fld_3.'</td>
                        <td>'.$fld_4.'</td>
                      </tr>';
                 $coun++;
             } //end foreach

             echo $format;
             die;
         }
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }

     }



 }
