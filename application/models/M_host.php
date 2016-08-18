<?php

//echo check_host("KRX-D0xx.xxxxxxx.xxx");
// MODEL - Dane o hostach


class M_host extends CI_Model{ 
	

	function check_host($hostname){ // Sprawdź czy dany host jest włączony
		
		$status='OFFLINE'; // OFFLINE
		try {
			$fp = fSockOpen($hostname.'.'.DOMAIN_NAME,135,$errno,$errstr,1);
		}
		catch (Exception $e){}
			if($fp) {
				$status='ONLINE'; // ONLINE
				fclose($fp);	
			} 

		return $status;
	}
}


?>