<div id = "info">
<ul>
<li><b>Imię: <?=$info['givenname'] ?></b></li>
<li><b>Nazwisko: <?=$info['sn'] ?></b></li>
<li><b>netID: <?=$info['netid'] ?></b></li>
<li>E-mail: <?=$info['mail'] ?></li>
<li>Stanowisko: <?=$info['title'] ?></li>
<li>Dział: <?=$info['department'] ?></li>
<li>Numer pracownika: <?=$info['employeenumber'] ?></li>
<li>Numer telefonu 1: <?=$info['telephonenumber'] ?></li>
<li>Numer telefonu 2: <?=$info['facsimiletelephonenumber'] ?></li>
<li>Przełożony: <?=$info['manager'] ?></li>
<li>Kraj: <?=$info['c'] ?></li>
<li>Miasto: <?=$info['l'] ?></li>
<li>Ulica: <?=$info['streetaddress']?></li>
<li>Kod pocztowy: <?=$info['postalcode'] ?></li>
<li>Obiekt AD: <?=$info['distinguishedname']?></li>
<li>Data utworzenia: <?=$info['whencreated'] ?></li>
<li>Aktualne miejsce: <?=$info['co'] ?></li>
<li>Adres proxy: <?=$info['proxyaddresses'] ?></li>
<li>Strona domowa: <?=$info['wwwhomepage'] ?></li>
<li>Stan konta: <?=$info['useraccountcontrol']?></li>
<li>Ostatnie nieudane próby logowania: <?=$info['badpwdcount'] ?></li>
<li>Ostatnie nieudana próba logowania: <?=$info['badpasswordtime'] ?></li>
<li>Ostatnie logowanie: <?=$info['lastlogon'] ?></li>
<li>Ostatnia zmiana hasła: <?=$info['pwdlastset'] ?></li>
<li>Data wygaśnięcia konta: <?=$info['accountexpires'] ?></li>
<li>Ilość logowań: <?=$info['logoncount'] ?></li>
<li>Komputery: <ul><?php 
foreach($info['computers'] as $value)
{ 
echo '<li><span class="computer_name">'.$value.'</span><span class="computer_status"></span></li>';
}
?></li></ul>
</div>

<div id = "group">
<ul>
<li><b>Grupy: </b><ul>
<?php 
foreach($info['memberof'] as $value)
{ 
	echo '<li><a href= "'.base_url().'Group/get_members/'.$value.'">'.$value.'</a></li>';
}
?>
</li></ul>
</ul>
</div>	

<div id ="photo">
<?php
if(isset($info['thumbnailphoto'][0]))
{
	echo '<fieldset><legend>Active Directory</legend><img  src="data:image/jpeg;base64,'.base64_encode($info['thumbnailphoto']).'" /></fieldset>';
}
else{
	echo '<fieldset><legend>Active Directory</legend><img src="'.base_url().'assets/img/brak.gif"/></fieldset>';
}

if($info['empire_photo_exist'])
{
	echo '<fieldset><legend>Empire</legend><img  src="'.$info['empire_photo'].'"/></fieldset>';
}
else{
	echo '<fieldset><legend>Empire</legend><img src="'.base_url().'assets/img/brak.gif"/></fieldset>';
}
?>
</div>

<script>
var base_url = "<?=base_url()?>";

$(function() {
	
	$('.computer_name').each(function(){
		var sc = $(this);
		//var computername= sc.text();
		getComputerStatus(sc);
	}
	)
});

function getComputerStatus(sc){
	var computername= sc.text();
	//alert(name);
	$.ajax({
	dataType: "json",
	url: base_url+'C_host/check_host/'+computername,
	//data: data,
	success: function(data)
	{
		var parent = sc.parent();
		$('.computer_status', parent).text(' '+data.status);
		if(data.status == 'OFFLINE')
		{
			$('.computer_status', parent).css("color", "red");
		}
		if(data.status == 'ONLINE')
		{
			$('.computer_status', parent).css("color", "green");
		}
	}
});

}


</script>