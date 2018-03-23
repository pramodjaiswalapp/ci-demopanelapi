<?php

defined('BASEPATH') OR exit('No direct script access allowed');
$config['edit_admin_profile'] = array(
    array(
        'field' => 'Admin_Name',
        'label' => 'Admin_Name',
        'rules' => 'trim|required|min_length[3]|max_length[50]'
    )
);
?>