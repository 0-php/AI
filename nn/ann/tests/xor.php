<?php

require_once '../Loader.php';
 
use ANN\Network;
use ANN\Values;
 
try
{
  $objNetwork = Network::loadFromFile('xor.dat');
}
catch(Exception $e)
{
  print 'Creating a new one...';
 
  $objNetwork = new Network;
 
  $objValues = new Values;
 
  $objValues->train()
            ->input(0,0)->output(0)
            ->input(0,1)->output(1)
            ->input(1,0)->output(1)
            ->input(1,1)->output(0);
 
  $objValues->saveToFile('values_xor.dat');
 
  unset($objValues);
}
 
try
{
  $objValues = Values::loadFromFile('values_xor.dat');
}
catch(Exception $e)
{
  die('Loading of values failed');
}
 
$objNetwork->setValues($objValues); // to be called as of version 2.0.6
 
$boolTrained = $objNetwork->train();
 
print ($boolTrained)
        ? 'Network trained'
        : 'Network not trained completely. Please re-run the script';
 
$objNetwork->saveToFile('xor.dat');
 
$objNetwork->printNetwork();

?>