<?php
//$config['protocol'] = 'smtp';
$config['protocol'] = 'sendmail';
$config['smtp_host'] = 'tls://mail.applaurels.com'; 
$config['charset'] = 'utf-8';  
$config['mailtype'] = 'html';
$config['from'] = 'noreply@applaurels.com';
$config['from_name'] = 'Project Name';
$config['reply_to'] = 'noreply@applaurels.com';
$config['reply_to_name'] = 'Project Name';
$config['mailpath'] ="/usr/sbin/sendmail";

