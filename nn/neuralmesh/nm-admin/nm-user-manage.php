<?php
require("lib/controller.class.php");
$app = new Controller;
switch($_GET['action']) {
	case "delete":
		if(!$app->model->get("usernetwork")->delink($_GET['u'],$_GET['n'])) {
			$app->model->get("users")->remove($_GET['u']);
		}
		break;
}
Model::direct("nm-users.php?n=".$_GET['n']);
?>