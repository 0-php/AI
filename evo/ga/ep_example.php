<?php
require_once('../classes/ep.class.php');

function fitness(){

}

$ep = new ep();
$ep->fitnessFunction = 'fitness';
$ep->population = 10;
$ep->debug();
?>