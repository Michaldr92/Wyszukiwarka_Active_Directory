<?php


// MODEL - Użytkownicy 

class M_user extends CI_Model{
	
	private function get_computer_list($desc){ // Pobranie listy komputerów jakie posiada użytkownik
			
		$ret=array();  // Ustawienie $ret jako tablicy


		if ( isset($desc) && strlen($desc)>2 ) { // Sprawdź czy $desc jest ustawione i czy jest większe od 2
				
			// Łączenie po LDAP przez Active Directory 
			
			$ds=ldap_connect(LDAP_SERVER);
	
			if ($ds){
				$r=ldap_bind($ds, LDAP_USER, LDAP_PASS);
				$sr=ldap_search($ds, LDAP_COMPUTERS_DN, "description=*".$desc."*");
				$info = ldap_get_entries($ds, $sr);		
				$ile = count($info);
				if ($ile > 0){
					for ($i=0;$i<$ile;$i++){
						if (isset($info[$i]['cn'][0]) && $info[$i]['cn'][0]!='') $ret[$i] = $info[$i]['cn'][0];
					}
				}				
			}
		}		
			return $ret;			
	}
	
		
	private function spr_url($url){ // Sprawdzenie url - biblioteka dodatkowa
      $AgetHeaders = @get_headers($url);
        if (preg_match("|200|", $AgetHeaders[0])) {
         return true;
        } 
		else {
              return false;
          }
}

	private function getad($klucz) // Pobranie klucza AD
	{
		$out = '';
		
		$ile = $klucz['count'];
		if($ile > 1)
		{
			$out = "<ul>";
			for($i = 0; $i < $ile; $i++)
			{
				$out.="<li>".$klucz[$i]."</li>";
			}
			$out.="</ul>";
		}
		
		else{
			$out = $klucz[0];
		}
		
		return $out;
	}
	
	private function strefaoz($dane) // Tutaj funkcja ktora rozbija MS format na bardziej user-friendly
	{
		$utime = ($dane * 1);
		$var = (string) $utime;		
		$zmienna = substr($var,0, 4).'-'.substr($var,4,2).'-'.substr($var,6,2).' '.substr($var,8,2).':'.substr($var,10,2).':'.substr($var,12,2);
		return $zmienna;
		//return $utime;
	}
	
	function getUserAccountControlAttributes($inputCode) // UAC - Microsoft - Dane konta
	{
	
		$userAccountControlFlags = array(
		16777216 => "TRUSTED_TO_AUTH_FOR_DELEGATION",
		8388608 => "PASSWORD_EXPIRED",
		4194304 => "DONT_REQ_PREAUTH",
		2097152 => "USE_DES_KEY_ONLY",
		1048576 => "NOT_DELEGATED",
		524288 => "TRUSTED_FOR_DELEGATION",
		262144 => "SMARTCARD_REQUIRED",
		131072 => "MNS_LOGON_ACCOUNT",
		65536 => "DONT_EXPIRE_PASSWORD",
		8192 => "SERVER_TRUST_ACCOUNT",
		4096 => "WORKSTATION_TRUST_ACCOUNT",
		2048 => "INTERDOMAIN_TRUST_ACCOUNT",
		512 => "NORMAL_ACCOUNT",
		256 => "TEMP_DUPLICATE_ACCOUNT",
		128 => "ENCRYPTED_TEXT_PWD_ALLOWED",
		64 => "PASSWD_CANT_CHANGE",
		32 => "PASSWD_NOTREQD",
		16 => "LOCKOUT",
		8 => "HOMEDIR_REQUIRED",
		2 => "ACCOUNTDISABLE",
		1 => "SCRIPT");

		$attributes = NULL;
		while($inputCode > 0) {
			foreach($userAccountControlFlags as $flag => $flagName) {
				$temp = $inputCode-$flag;
				if($temp>0) {
					$attributes[$userAccountControlFlags[$flag]] = $flag;
					$inputCode = $temp;
				}
				if($temp==0) {
					if(isset($userAccountControlFlags[$inputCode])) {
						$attributes[$userAccountControlFlags[$inputCode]] = $inputCode;
					}
					$inputCode = $temp;
				}
			}
		}
		
		$out=array();
		foreach ($attributes as $klucz=>$value){
			array_push($out,$klucz);
		}
	
		return implode(", ",$out);
	
	}
	
	
	private function rozkodowanie($flags) // 
	{
			$flag_to_find = 530;
			$flags = array();
			for ($i=0; $i<=26; $i++){
			if ($flag_to_find & (1 << $i)){
			$zmienna = array_push($flags, 1 << $i);
				}
			}
			return $zmienna;
		 
		}
	private function toutf8($s)
	{
		
		$konwersja = iconv("Windows-1250","UTF-8",$s);
		
		return $konwersja;
	}

	private function get_group_name($wyrazenie)
	{
		$ret='';
		$tab = explode(",", $wyrazenie);
		$tab = explode("=",$tab[0]);
		
		if(isset($tab[1])) $ret=$tab[1];
		return $ret;
	}

	private function get_group_names($tab)
	{

		$ret = array();
		
		if(is_array($tab) && count($tab) > 0)
		{
			foreach($tab as $value)
			{
				$grupa = $this->get_group_name($value);
				if($grupa != "")
				{
					array_push($ret, $grupa);
				}
			}
		}
		return $ret;
	}

	private function konwersja($wintime) {
	  
	   $seconds_ad = $wintime/ (10000000);
	   //86400 -- seconds in 1 day
	   $unix = ((1970-1601) * 365 - 3 + round((1970-1601)/4) ) * 86400;
	 
	   $timestamp = $seconds_ad - $unix; 
	   $normalDate = date("Y-m-d H:i:s", $timestamp);
	 
		  return $normalDate;
	}
	
	public function getlist($nazwisko){
			
		$ret=array();
		$fields=array(
			'sn',
			'givenname',
			'samaccountname',
			'mail'
		);

		if ( isset($nazwisko) && strlen($nazwisko)>1 ) {
				
			$ds=ldap_connect(LDAP_SERVER);
			//ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

			if ($ds){
				$r=ldap_bind($ds, LDAP_USER, LDAP_PASS);
				$sr=ldap_search($ds, LDAP_DN, "sn=".$nazwisko."*");
				$info = ldap_get_entries($ds, $sr);
				//echo '<pre>';
				//print_r($info);
				//echo '</pre>';
				//die();			
				$c=(int)$info['count'];
				for ($i=0; $i < $c; $i++){
					foreach ($fields as $f){
						$ret[$i][$f]='';
						if (isset($info[$i][$f])) {						
							$ret[$i][$f]=$info[$i][$f][0];																
						}
					}		
				}		
			}
		}		
			return $ret;			
	}
		
	//-------------------------------
	
public function getinfo($netID)
	{
	$ret=array();

	if ( isset($netID) && strlen($netID)>2 ) {
			
		$ds=ldap_connect(LDAP_SERVER);
		if ($ds){
			$r=ldap_bind($ds, LDAP_USER, LDAP_PASS);
			$sr=ldap_search($ds, LDAP_DN, "samaccountname=".$netID);
			$info = ldap_get_entries($ds, $sr);

			$info=$info[0];
			$ret['mail']=$ret['nazwisko']=$ret['cn']=$ret['sn']=$ret['c']=$ret['l']=$ret['postalcode']=$ret['physicaldeliveryofficename']=$ret['telephonenumber']=$ret['facsimiletelephonenumber']=
			$ret['givenname']=$ret['distinguishedname']=$ret['instancetype']=$ret['whencreated']=$ret['whenchanged']=$ret['displayname']=$ret['usncreated']=
			$ret['usnchanged']=$ret['co']=$ret['department']=$ret['streetaddress']=$ret['wwwhomepage']=$ret['employeenumber']=
			$ret['name']=$ret['objectguid']=$ret['useraccountcontrol']=$ret['badpwdcount']=$ret['codepage']=$ret['countrycode']=$ret['badpasswordtime']=$ret['lastlogoff']=
			$ret['lastlogon']=$ret['pwdlastset']=$ret['primarygroupid']=$ret['objectsid']=$ret['accountexpires']=$ret['logoncount']=$ret['netid']=$ret['samaccounttype']=
			$ret['userprincipalname']=$ret['lockouttime']=$ret['proxyaddresses']=$ret['objectcategory']=$ret['dscorepropagationdata']=$ret['astlogontimestamp']=$ret['manager']=$ret['mobile']=$ret['thumbnailphoto']=$ret['title'] = "";
			$ret['memberof'] = array();
			
			if (isset($info['mail'][0])) $ret['mail']=$info['mail'][0];
			if (isset($info['cn'][0])) $ret['cn']=$info['cn'][0];
			if (isset($info['sn'][0])) $ret['sn']=$info['sn'][0];
			if (isset($info['c'][0])) $ret['c']=$info['c'][0];
			if (isset($info['l'][0])) $ret['l']=$this->toutf8($info['l'][0]);
			if (isset($info['title'][0])) $ret['title']=$info['title'][0];
			if (isset($info['postalcode'][0])) $ret['postalcode']=$info['postalcode'][0];
			if (isset($info['physicaldeliveryofficename'][0])) $ret['physicaldeliveryofficename']=$info['physicaldeliveryofficename'][0];
			if (isset($info['telephonenumber'][0])) $ret['telephonenumber']=$info['telephonenumber'][0];
			if (isset($info['facsimiletelephonenumber'][0])) $ret['facsimiletelephonenumber']=$info['facsimiletelephonenumber'][0];
			if (isset($info['givenname'][0])) $ret['givenname']=$info['givenname'][0];
			if (isset($info['distinguishedname'][0])) $ret['distinguishedname']=$info['distinguishedname'][0];
			if (isset($info['instancetype'][0])) $ret['instancetype']=$info['instancetype'][0];
			if (isset($info['whencreated'][0])) $ret['whencreated']=$this->strefaoz($info['whencreated'][0]);
			if (isset($info['whenchanged'][0])) $ret['whenchanged']=$info['whenchanged'][0];
			if (isset($info['displayname'][0])) $ret['displayname']=$info['displayname'][0];
			if (isset($info['usncreated'][0])) $ret['usncreated']=$info['usncreated'][0];
			if (isset($info['memberof'][0])) $ret['memberof']=$this->get_group_names($info['memberof']); // TABLICA
			if (isset($info['usnchanged'][0])) $ret['usnchanged']=$info['usnchanged'][0];
			if (isset($info['co'][0])) $ret['co']=$info['co'][0];
			if (isset($info['department'][0])) $ret['department']=$info['department'][0];
			if (isset($info['proxyaddresses'][0])) $ret['proxyaddresses']=$this->getad($info['proxyaddresses']);
			if (isset($info['streetaddress'][0])) $ret['streetaddress']=$this->toutf8($info['streetaddress'][0]);
			if (isset($info['wwwhomepage'][0])) $ret['wwwhomepage']=$info['wwwhomepage'][0];
			if (isset($info['employeenumber'][0])) $ret['employeenumber']=$info['employeenumber'][0];
			if (isset($info['name'][0])) $ret['name']=$info['name'][0];
			if (isset($info['objectguid'][0])) $ret['objectguid']=$info['objectguid'][0];
			if (isset($info['useraccountcontrol'][0])) $ret['useraccountcontrol']=$this->getUserAccountControlAttributes($info['useraccountcontrol'][0]);
			if (isset($info['badpwdcount'][0])) $ret['badpwdcount']=$info['badpwdcount'][0];
			if (isset($info['codepage'][0])) $ret['codepage']=$info['codepage'][0];
			if (isset($info['countrycode'][0])) $ret['countrycode']=$info['countrycode'][0];
			if (isset($info['badpasswordtime'][0])) $ret['badpasswordtime']=$this->konwersja($info['badpasswordtime'][0]);
			if (isset($info['lastlogoff'][0])) $ret['lastlogoff']=$this->konwersja($info['lastlogoff'][0]);
			if (isset($info['lastlogon'][0])) $ret['lastlogon']=$this->konwersja($info['lastlogon'][0]);
			if (isset($info['pwdlastset'][0])) $ret['pwdlastset']=$this->konwersja($info['pwdlastset'][0]);
			if (isset($info['primarygroupid'][0])) $ret['primarygroupid']=$info['primarygroupid'][0];
			if (isset($info['objectsid'][0])) $ret['objectsid']=$info['objectsid'][0];
			if (isset($info['accountexpires'][0])) $ret['accountexpires']=$this->konwersja($info['accountexpires'][0]);
			if (isset($info['logoncount'][0])) $ret['logoncount']=$info['logoncount'][0];
			if (isset($info['samaccountname'][0])) $ret['netid']=$info['samaccountname'][0];
			if (isset($info['samaccounttype'][0])) $ret['samaccounttype']=$info['samaccounttype'][0];
			if (isset($info['userprincipalname'][0])) $ret['userprincipalname']=$info['userprincipalname'][0];
			if (isset($info['lockouttime'][0])) $ret['lockouttime']=$info['lockouttime'][0];
			if (isset($info['objectcategory'][0])) $ret['objectcategory']=$info['objectcategory'][0];
			if (isset($info['dscorepropagationdata'][0])) $ret['dscorepropagationdata']=$info['dscorepropagationdata'][0];
			if (isset($info['astlogontimestamp'][0])) $ret['astlogontimestamp']=$info['astlogontimestamp'][0];
			if (isset($info['manager'][0])) $ret['manager']=$this->get_group_name($info['manager'][0]);
			if (isset($info['mobile'][0])) $ret['mobile']=$info['mobile'][0];
			if (isset($info['thumbnailphoto'][0])) $ret['thumbnailphoto']=$info['thumbnailphoto'][0];
			$ret['empire_photo']=EMPIRE_PHOTO_PREFIX.$ret['employeenumber'].'.jpg';
			$ret['empire_photo_exist']=$this->spr_url($ret['empire_photo']);
			$imienazwisko = $info['givenname'][0].' '.$info['sn'][0];
			$ret['computers']=$this->get_computer_list($imienazwisko);
			
		}
	}		
		return $ret;
}
}