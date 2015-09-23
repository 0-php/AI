<?php
/**
 * Form processing page to manage the network structure
 * @author Louis Stowasser
 */
 
require("lib/controller.class.php");
$app = new Controller;
$app->model->val->run("default",$_GET);

$app->inc("nmesh");
$data = $app->model->get("network")->get($_GET['n']);
$nn = $app->model->get("network")->nn;

switch($_GET['action']) {
	
	case model::$INPUT:
		if($_GET['submit'] == "Add") {
			$nn->add_inputs($_GET['quantity']);
		} else if($_GET['submit'] == "Remove") {
			$nn->remove_inputs($_GET['quantity']);
		}
		break;
		
	case model::$OUTPUT:
		if($_GET['submit'] == "Add") {
			$nn->add_outputs($_GET['quantity']);
		} else if($_GET['submit'] == "Remove") {
			$nn->remove_outputs($_GET['quantity']);
		}
		break;
		
	case model::$NEURON:
		if($_GET['submit'] == "Add") {
			$nn->add_neuron($_GET['quantity']);
		} else if($_GET['submit'] == "Remove") {
			$nn->remove_neuron($_GET['quantity']);
		}
		break;
		
	case model::$LAYER:
		if($_GET['submit'] == "Add") {
			$nn->add_layer();
		} else if($_GET['submit'] == "Remove") {
			$nn->remove_layer();
		}
		break;
		
	case model::$NETWORK:
		$app->model->get("network")->delete($_GET['n']);
		Model::direct("nm-main.php");
		exit;
		break;
}
$app->model->get("network")->buildTree($_GET['n'],$nn);
$app->model->get("network")->save($nn,$_GET['n']);
Model::direct("nm-network.php?n=".$_GET['n']);
?>
