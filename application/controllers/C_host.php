<?php
defined('BASEPATH') OR exit('No direct script access allowed');


//KONTROLER 


class C_host extends CI_Controller {
	
	public function __construct()
       {
            parent::__construct();
			$this->load->model('M_host');		
       }	
	

	public function check_host($hostname)
		{

			$response = array('status'=>$this->M_host->check_host($hostname)); // Sprawdzenie czy dany HOST jest ON/OFF line			
			$this->load->view('hostinfo_json', array('response'=>$response)); // Wczytanie widoku hostu

		}
	
}


?>