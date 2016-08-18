<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class User extends CI_Controller {
	
	  public function __construct()
       {
            parent::__construct();
			$this->load->model('m_user'); // załadowanie modelu użytkowników		
       }
	
	// 
	public function index()
	{
		$nazwisko = $this->input->post('nazwisko'); // pobranie nazwiska z formularza
		$this->load->view('header'); // załadowanie widoku głównego
		$this->load->view('wyszukiwarka', array('nazwisko'=>$nazwisko)); // załadowanie wyszukiwarki + nazwiska
		
		if(strlen($nazwisko) > 1){ // Sprawdzaj czy nazwisko ma więcej niż 1 znak
			$lista = $this->m_user->getlist($nazwisko); // pobierz listy nazwisk
			$ile = count($lista); // Lista - ilość
		
			if($ile == 0){ // Jeżeli brak nazwisk to wyświetl brak danych
				$this->load->view('brakdanych'); 
			}elseif ($ile == 1){ // Jeżeli jedno nazwisko, przejdź od razu do wyświetlenia danych
				$netid = $lista[0]['samaccountname'];
				$info=$this->m_user->getinfo($netid);
				$this->load->view('showinfo', array('info'=>$info));
				
			}elseif ($ile > 1){ // Jeżeli więcej niż 1 nazwisko to pokaż listę nazwisk
				$this->load->view('showlist', array('lista'=>$lista));
			}
		
		}else{ // Brak danych
			$this->load->view('brakdanych');
		}
	
		$this->load->view('footer'); // Załadowanie widoku stopki
	}
		
	public function getlist($nazwisko)
	{		
		$this->load->view('showlist', array('lista'=>$this->m_user->getlist($nazwisko))); // Pokaż liste nazwisk
	}
	
	public function getinfo($netID) // Pobierz informacje z argunementem NETID
	{
		$info = $this->m_user->getinfo($netID);
		$nazwisko = $info['sn']; // Imię Nazwisko
		$this->load->view('header', array('info'=>$this->m_user->getinfo($netID))); // Główny Widok
		$this->load->view('wyszukiwarka', array('nazwisko'=>$nazwisko)); // Widok wyszukiwarki
		$this->load->view('showinfo', array('info'=>$info)); // Widok informacji
		$this->load->view('footer'); // Widok stopki
	}
}