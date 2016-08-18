<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Group extends CI_Controller{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Group_m'); // Załadowanie modelu group
	}

	
	public function get_members($gr)
	{
		$info = $this->Group_m->get_group_members($gr); // Pobierz grupy użytkowników
		
		$this->load->view('header', array('info'=>array())); // Załadowanie głównego widoku
		$this->load->view('wyszukiwarka', array('nazwisko'=>'')); // Załadowanie widoku -> wyszukiwarka
		$this->load->view('showgroup', array('info'=>$info)); // Załadowanie widoku -> grupy
		$this->load->view('footer'); // Załadowanie widoku -> stopka
	}
	
	

}

?>