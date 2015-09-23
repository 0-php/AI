<?php
require("lib/controller.class.php");
$app = new Controller;

switch($_GET['action']) {
	case "remove":
		Model::loadProxy("train")->validate($_GET['s']);
		db::init()->query("pattern.remove",array("id"=>$_GET['s']));
		break;
		
	case "add":
		$app->model->val->run("trainingset",$_POST);
		db::init()->query("pattern.add",array("pattern"=>$_POST['input'],
										 "id"=>$_POST['id'],
										 "output"=>$_POST['output']));
		break;
		
	case "rename":
		$app->model->val->run("setrename",$_POST);
		db::init()->query("train.update",array("label"=>$_POST['label'],"id"=>$_POST['id']));
		break;
		
	case "delete":
		Model::loadProxy("train")->validate($_GET['s']);
		
		db::init()->query("train.remove",array("id"=>$_GET['s']));
		break;
		
	case "new":
		$app->model->val->run("newset",$_POST);
		db::init()->query("train.add",array("id"=>$_POST['n'],"label"=>$_POST['label']));
		break;
		
}
Model::direct($_SERVER['HTTP_REFERER']);
?>
