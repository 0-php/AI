<?php
/**
 * Main admin page
 * @author Louis Stowasser
 */
require("lib/controller.class.php");
$app = new Controller;

$app->assign("nlist",$app->model->get("network")->listNetworks());
$data = $app->model->get("network")->getStats($_SESSION['id']);
$app->map($data);
$app->assign("auth",md5($_SESSION['name']));
$app->display("main"); 
?>
