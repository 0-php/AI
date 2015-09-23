<?php
//include('../classes/system.class.php');
include('D:\web\www\lh\lib/php/classes/threads.class.php');

$params = Threads::getParams();
echo $params['chromosome'];

$chromosome = $params['chromosome'];

echo "<pre>"; print_r($params); echo "</pre>";

//$system = new System($chromosome);
?>