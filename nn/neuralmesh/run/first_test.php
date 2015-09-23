<?php

include('../nm-admin/lib/proxy/nmesh.class.php');

$inputs = 1;
$outputs = 1;
$hidden_neurons_per_layer = 1;
$hidden_layers = 1;

$inputarray = array();

$nmesh = new nmesh($inputs, $outputs, $hidden_neurons_per_layer, $hidden_layers);
$nmesh->run($inputarray);
?>