<?php
$dirname = 'networks/';

$dh = opendir($dirname);
while(false !== ($file = readdir($dh))){
	if($file != '.' && $file != '..')
		echo "<a href='".$dirname.$file."'>".$file."</a><br>";
}

?>