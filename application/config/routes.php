<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

 /*
   | -------------------------------------------------------------------------
   | URI ROUTING
   | -------------------------------------------------------------------------
   | This file lets you re-map URI requests to specific controller functions.
   |
   | Typically there is a one-to-one relationship between a URL string
   | and its corresponding controller class/method. The segments in a
   | URL normally follow this pattern:
   |
   |	example.com/class/method/id/
   |
   | In some instances, however, you may want to remap this relationship
   | so that a different class/function is called than the one
   | corresponding to the URL.
   |
   | Please see the user guide for complete details:
   |
   |	https://codeigniter.com/user_guide/general/routing.html
   |
   | -------------------------------------------------------------------------
   | RESERVED ROUTES
   | -------------------------------------------------------------------------
   |
   | There are three reserved routes:
   |
   |	$route['default_controller'] = 'welcome';
   |
   | This route indicates which controller class should be loaded if the
   | URI contains no data. In the above example, the "welcome" class
   | would be loaded.
   |
   |	$route['404_override'] = 'errors/page_missing';
   |
   | This route will tell the Router which controller/method to use if those
   | provided in the URL cannot be matched to a valid route.
   |
   |	$route['translate_uri_dashes'] = FALSE;
   |
   | This is not exactly a route, but allows you to automatically route
   | controller and method names that contain dashes. '-' isn't a valid
   | class or method name character, so it requires translation.
   | When you set this option to TRUE, it will replace ALL dashes in the
   | controller and method URI segments.
   |
   | Examples:	my-controller/index	-> my_controller/index
   |		my-controller/my-method	-> my_controller/my_method
  */
 $route['Notfound'] = 'admin/Notfound';

 if ( isset( $_SERVER["REQUEST_URI"] ) && preg_match( '/.*\/(api)\/.*/', $_SERVER["REQUEST_URI"] ) == TRUE ) {
     //$route['404_override'] = 'api/Page404';
 }
 else if ( isset( $_SERVER["REQUEST_URI"] ) && preg_match( '/.*\/admin\/.*/', $_SERVER["REQUEST_URI"] ) == TRUE ) {
     $route['404_override'] = 'Notfound';
 }
 else if ( isset( $_SERVER["REQUEST_URI"] ) && preg_match( '/.*\/req\/.*/', $_SERVER["REQUEST_URI"] ) == TRUE ) {
     $route['404_override'] = 'Notfound';
 }
 else if ( isset( $_SERVER["REQUEST_URI"] ) && preg_match( '/.*\/web\/.*/', $_SERVER["REQUEST_URI"] ) == TRUE ) {
     $route['404_override'] = 'admin/Notfound';
 }
 else {
     //$route['404_override'] = 'Notfound';
 }

 $route['default_controller']   = 'admin/Admin';
 $route['translate_uri_dashes'] = FALSE;

 /* Route for Admin */

 $route["admin"]                 = 'admin/Admin';
 $route["admin/forget"]          = 'admin/Admin/forget';
 $route["admin/editMerchant"]    = 'admin/Vendor_Management/merchant_edit_profile';
 $route["admin/viewMerchant"]    = 'admin/Vendor_Management/merchant_view_profile';
 $route["admin/users"]           = 'admin/User/index';
 $route["admin/profile"]         = 'admin/Admin_Profile/admin_profile';
 $route["admin/change-password"] = 'admin/Admin_Profile/admin_change_password';
 $route["admin/edit-profile"]    = 'admin/Admin_Profile/edit_profile';
 $route["admin/users/detail"]    = 'admin/User/detail';


 /* Add merchant Ajax prodilepicture */

 $route['req/upload/profile-picture']      = 'admin/AjaxUtil/profilePictureUpload';
 $route['req/check-email-exists']          = 'admin/AjaxUtil/emailExistsAjax';
 $route['req/check-mobile-exists']         = 'admin/AjaxUtil/mobileExistsAjax';
 $route['req/block-user']                  = 'admin/AjaxUtil/changestatus';
 $route['req/delete-user']                 = 'admin/AjaxUtil/deleteuser';
 $route['req/check-edit-email-exists']     = 'admin/AjaxUtil/editemailExistsAjax';
 $route['req/check-edit-mobile-exists']    = 'admin/AjaxUtil/editmobileExistsAjax';
 $route['req/check-edit-passmatch-exists'] = 'admin/AjaxUtil/oldpasswordExistsAjax';
 $route['req/getstatesbycountry']          = 'admin/AjaxUtil/getStatesByCountry';
 $route['req/change-user-status']          = 'admin/AjaxUtil/changeUserStatus';
 $route['req/manage-sidebar']              = 'admin/AjaxUtil/manageSideBar';

 /* Api Routes */

 $route['api/manage-friend']  = 'api/managefriends';
 $route['api/managefeed']     = 'api/Managefeeds';
 $route['api/feeds']          = 'api/Managefeeds/feeds';
 $route['api/deletefeed']     = 'api/Managefeeds/delete_feed';
 $route['api/editfeed']       = 'api/Managefeeds/edit_feed';
 $route['api/reportfeed']     = 'api/Managefeeds/report_feed';
 $route['api/likefeed']       = 'api/Managefeeds/like';
 $route['api/sharefeed']      = 'api/Managefeeds/share';
 $route['api/managecomments'] = 'api/Managefeeds/manage_comment';
 $route['api/hashtags']       = 'api/Managefeeds/hashtags';
 $route['api/savelocation']   = 'api/Managefeeds/save_location';
 $route['api/event']          = 'api/Events';


 /*   Web login */
 $route["web"]                    = 'web/Login';
 $route["req/ajax_post_login"]    = 'web/SocialLogin/ajax_post_login';
 $route["req/ajax_post_linkedin"] = 'web/SocialLogin/ajax_post_linkedin';
 $route["req/ajax_post_google"]   = 'web/SocialLogin/ajax_post_google';
 $route["web/logout"]             = 'web/Dashboard/logout';
 $route["web/instagram"]          = 'web/SocialLogin/instagram';
 $route["web/twitter"]            = 'web/SocialLogin/twitterauth';
 /*
  * Post Managment
  */
#$route['admin/posts/(:any)'] = "admin/posts/index/$1";

 $route['access-denied']        = "admin/Notfound/show403";
 $route['web/redirect']         = "web/SocialLogin/twitterauth";
 $route['web/closeLoginWindow'] = "web/Twitter_login/dashboard_redirection";
 $route['web/instaRedirect']    = "web/SocialLogin/redirect_to_instagram";


//rcc subscription
 $route['api/subscription']      = 'api/Subscriptions/subscription';
 $route['api/user_subscription'] = 'api/Subscriptions/user_subscription';
 $route['api/buy']               = 'api/Subscriptions/buy';
 $route['api/revoke']            = 'api/Subscriptions/revoke';
 