<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['account_suffix']		= '@xxxxxxx.xxx'; // Serwer
$config['base_dn']				= 'DC=xxxxxxx.xxx'; // Serwer
$config['domain_controllers']	= array ("XX.XX.XX.XX"); // IP  Active Directory
$config['ad_username']			= 'XXXXXXXXXXX'; // Login AD
$config['ad_password']			= 'XXXXXXXXXXX'; // HASŁO AD
$config['real_primarygroup']	= true;
$config['use_ssl']				= false;
$config['use_tls'] 				= false;
$config['recursive_groups']		= true;


/* End of file adldap.php */
/* Location: ./system/application/config/adldap.php */