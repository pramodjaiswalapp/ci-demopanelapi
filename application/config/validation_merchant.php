<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$config['add_merchant'] = array(
      array(
                'field' => 'Merchant_Name',
                'label' => 'Merchant_Name',
                'rules' => 'trim|required|min_length[3]|max_length[50]'
        ),
        array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'required|regex_match[/^[\w\W]{6,}$/]',
                'errors' => array(
                        'required' => 'You must provide a %s.',
                        'regex_match' => "%s:Minimum 6 character"
                ),
	),
        array(
                'field' => 'confirm-password',
                'label' => 'confirm-password',
                'rules' => 'required|min_length[6]|matches[password]'
        ),
        array(
                'field' => 'email',
                'label' => 'Email',
                'rules' => "trim|required|regex_match[/^[a-zA-Z0-9!#$%&'*+\/=?^_\`{|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+\/=?^_\`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/]"
        ),
	 array(
                'field' => 'mobile-number',
                'label' => 'mobile-number',
                'rules' => 'trim|required|min_length[10]',
        ),
    	 array(
                'field' => 'address',
                'label' => 'address',
                'rules' => 'trim|required',
        )
);

 
?>