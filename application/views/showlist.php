<ul>
<?php

foreach ($lista as $value)
{
	echo '
	<pre>
	<li>
		<a class = "osoby" href ='.base_url().'User/getinfo/'.$value['samaccountname'].'>'.$value['sn'].'  '.$value['givenname'].'  '.$value['samaccountname'].'  '.$value['mail'].'</a>
	</li>
	</pre>';
}

?>
</ul>
