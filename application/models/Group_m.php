<?php

// MODEL - GRUPA


class Group_m extends CI_Model{
    
        
            public function get_group_members($gr){  // Funkcja pobiera grupy przypisanych użytkownikó
                
                $gr=str_replace('%20',' ',$gr);
                $ret=array();


            if ( isset($gr) && strlen($gr)>2 ) {  // Sprawdzenie czy $gr jest ustawione && czy $gr jest większe od 2
                    
                // KOMUNIKACJA PO LDAP z ACTIVE DIRECTORY 
                    
                $ds=ldap_connect(LDAP_SERVER); // Załadowanie do zmiennej połączenia
        
                if ($ds){ // Jeżeli ustanowiono połączenie ...
                
                    $r=ldap_bind($ds, LDAP_USER, LDAP_PASS); 
                    $sr=ldap_search($ds, LDAP_GROUPS_DN, "cn=*".$gr."*");
                    $info = ldap_get_entries($ds, $sr);        
                    //return $info;
                    
                    $ile = count($info);
                    
                    if ($ile > 0){
                        
                        // PETLA wyświetlające dane...
                        
                        for ($i=0;$i<$ile;$i++){
                            if (isset($info[$i]['member'][0]) && $info[$i]['cn'][0]!='') {
                                $mc=$info[$i]['member']['count'];
                                $group_name=$info[$i]['cn'][0];
                                $ret_tmp = array();
                                // PETLA FOR
                                for ($j=0;$j<$mc;$j++){
                                    $ret[$group_name][$j]=$info[$i]['member'][$j];
                                    
                                    
                                        $sr2=ldap_search($ds, LDAP_DN, "(|(distinguishedname=".$info[$i]['member'][$j]."))");
                                        $info2 = ldap_get_entries($ds, $sr2);    
                                        
                                        if(count($info2) > 1){
                                            $netid_tmp = $info2[0]['samaccountname'][0];
                                            $name_tmp = $info2[0]['givenname'][0];
                                            if(isset($info2[0]['sn'][0])){
                                                $surname_tmp = $info2[0]['sn'][0];
                                            }else{
                                                $surname_tmp ='';
                                            }
                                            array_push($ret_tmp, array($name_tmp, $surname_tmp, $netid_tmp));
                                        }
                                }                            
                                
                            }
                            
                        }
                    }
                    
                }
            }        
                return array('group_name'=>$group_name,"members"=>$ret_tmp);    // Zwracanie tablicy $group_name i $ret_tmp        
        }

            
    }
?>