<?php
 /*
  * To change this license header, choose License Headers in Project Properties.
  * To change this template file, choose Tools | Templates
  * and open the template in the editor.
  */

 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

 $config['permission'] = array (
     "user"         => array (
         "user_view"   => [
             "text"      => "View User List",
             "class"     => "User",
             "method"    => "index",
             "in_column" => false
         ],
         "user_detail" => [
             "text"      => "View User Detail",
             "class"     => "User",
             "method"    => "detail",
             "in_column" => false
         ],
         "user_block"  => [
             "text"      => "Block User",
             "class"     => "AjaxUtil",
             "method"    => "changeUserStatus",
             "in_column" => true
         ],
         "user_delete" => [
             "text"      => "Delete User",
             "class"     => "AjaxUtil",
             "method"    => "changeUserStatus",
             "in_column" => true
         ]
     ), //Content managment Permission
     "cms"          => array (
         "cms_view"   => [
             "text"      => "View Content",
             "class"     => "cms",
             "method"    => "index",
             "in_column" => false
         ],
         "cms_edit"   => [
             "text"      => "Edit Content",
             "class"     => "cms",
             "method"    => "edit",
             "in_column" => true
         ],
         "cms_add"    => [
             "text"      => "Add Content",
             "class"     => "cms",
             "method"    => "add",
             "in_column" => false
         ],
         "cms_delete" => [
             "text"      => "Delete Content",
             "class"     => "cms",
             "method"    => "delete",
             "in_column" => true
         ]
     ),
     #SUB ADMIN Checks
     "subadmin"     => [
         "admin_view"   => [
             "text"      => "Subadmin List",
             "class"     => "subadmin",
             "method"    => "index",
             "in_column" => false
         ],
         "admin_edit"   => [
             "text"      => "Subadmin List",
             "class"     => "subadmin",
             "method"    => "edit",
             "in_column" => false
         ],
         "admin_add"    => [
             "text"      => "Add new Subadmin",
             "class"     => "subadmin",
             "method"    => "add",
             "in_column" => false
         ],
         "admin_block"  => [
             "text"      => "Block Subadmin",
             "class"     => "AjaxUtil",
             "method"    => "changeUserStatus",
             "in_column" => false
         ],
         "admin_delete" => [
             "text"      => "Delete Subadmin",
             "class"     => "AjaxUtil",
             "method"    => "changeUserStatus",
             "in_column" => false
         ]
     ],
     # Version Control user Permission
     "version"      => [
         "version_view"   => [
             "text"      => "View Version List",
             "class"     => "version",
             "method"    => "index",
             "in_column" => false
         ],
         "version_add"    => [
             "text"      => "Add New Version",
             "class"     => "version",
             "method"    => "add",
             "in_column" => false
         ],
         "version_edit"   => [
             "text"      => "Edit Version",
             "class"     => "version",
             "method"    => "edit",
             "in_column" => true
         ],
         "version_delete" => [
             "text"      => "Delete Version",
             "class"     => "AjaxUtil",
             "method"    => "changeUserStatus",
             "in_column" => true
         ]
     ],
     # Notification User Permission
     "notification" => [
         "notification_list"       => [
             "text"      => "List Notification",
             "class"     => "notification",
             "method"    => "index",
             "in_column" => false
         ],
         "notification_add"        => [
             "text"      => "Add Notification",
             "class"     => "notification",
             "method"    => "add",
             "in_column" => false
         ],
         "notification_resend"     => [
             "text"      => "Resend Notification",
             "class"     => "notification",
             "method"    => "resendNotification",
             "in_column" => true
         ],
         "notification_editresend" => [
             "text"      => "Edit and Resend Notification",
             "class"     => "notification",
             "method"    => "edit",
             "in_column" => true
         ],
         "notification_delete"     => [
             "text"      => "Delete Notification",
             "class"     => "AjaxUtil",
             "method"    => "changeUserStatus",
             "in_column" => true
         ]
     ]
 );
