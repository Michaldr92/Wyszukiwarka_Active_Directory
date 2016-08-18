
<?php

	echo '<br/>';
	echo '<ul><li><b>Grupa:'.' '.$info['group_name'].'</b></li></ul>';
	echo '<br/>';
	
	echo '<div id = "netid_tmp">';
		for($i = 0; $i < count($info['members']); $i++){
			echo $info['members'][$i][2].';<br/>';
		}
	echo '</div>';
	
	echo '<div id = "names_tmp">';
		for($i = 0; $i < count($info['members']); $i++){
			echo $info['members'][$i][0].' '.$info['members'][$i][1].'<br/>';
	}
	echo '</div>';
	

?>
