<?php
if(!isset($_GET['action']))
	exit;
$action = $_GET['action'];

if($action == 'get_scripts_list'){
	$files = array();
	$dh = opendir('js/progs/');
	if($handle = opendir('js/progs/')){
		while(false !== ($file = readdir($handle)))
			if($file != "." && $file != "..")
				$files[] = substr($file, 0, -3);
		closedir($handle);
	}
	echo json_encode($files);
} elseif($action == 'get_script')
	echo file_get_contents('js/progs/'.$_GET['script'].'.js');
?>