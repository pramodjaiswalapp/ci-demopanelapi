<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

 /*
   | -------------------------------------------------------------------------
   | Hooks
   | -------------------------------------------------------------------------
   | This file lets you define "hooks" to extend CI without hacking the core
   | files.  Please see the user guide for info:
   |
   |	https://codeigniter.com/user_guide/general/hooks.html
   |
  */


 /**
  * @description Hook to check admin saved cookie each and evert time when any class initiate
  * @param array params An array to pass values to Function
  */
 $hook['post_controller_constructor'][] = array (
     'class'    => '',
     'function' => 'validate_cookie',
     'filename' => 'custom_hook.php',
     'filepath' => 'hooks',
     'params'   => array ('rcc_appinventiv', 'admin')
 );


 /**
  * @description  checking access permission
  */
 $hook['post_controller_constructor'][] = array (
     'class'    => '',
     'function' => 'checkAccessPermission',
     'filename' => 'custom_hook.php',
     'filepath' => 'hooks',
     'params'   => '');

 /**
  * @description hook to decrypt and set values to post
  */
 $hook['post_controller_constructor'][] = array (
     'class'    => '',
     'function' => 'get_parameters',
     'filename' => 'custom_hook.php',
     'filepath' => 'hooks',
     'params'   => ''
 );

 /**
  * @description check api authentication
  *///
 $hook['post_controller_constructor'][] = array (
     'class'    => '',
     'function' => 'user_authentication',
     'filename' => 'custom_hook.php',
     'filepath' => 'hooks',
     'params'   => ''
 );
