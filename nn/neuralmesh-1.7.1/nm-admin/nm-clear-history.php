<?php  require("lib/controller.class.php");$app = new Controller;$app->model->get("epochs")->clearHistory($_GET['n'],$_SESSION['id']);Model::direct();?>