<?php
///////////////////////////////////////////////////
//MLP neural network in PHP
//Original source code by Phil Brierley
//www.philbrierley.com
//Translated into PHP - dspink Sep 2005
//Modified by Phil July 2009
//This code may be freely used and modified at will
////////////////////////////////////////////////


//Tanh hidden neurons
//Linear output neuron

//To include an input bias create an
//extra input in the training data
//and set to 1


//////////////////////////////// User settings //////////////////
$numEpochs = 500; 
$numHidden = 3;
$LR_IH = 0.7;
$LR_HO = 0.07;

//////////////////////////////// Data dependent settings //////////////////
$numInputs = 3;
$numPatterns = 4;

////////////////////////////////////////////////////////////////////////////////

$patNum;
$errThisPat;
$outPred;
$RMSerror;

$trainInputs = array();
$trainOutput = array();


// the outputs of the hidden neurons

$hiddenVal = array();

// the weights
$weightsIH = array();
$weightsHO = array();


main();


//==============================================================
//********** THIS IS THE MAIN PROGRAM **************************
//==============================================================

function main()
{
 global $numEpochs;
 global $numPatterns;
 global $patNum;
 global $RMSerror;

 // initiate the weights
  initWeights();

 // load in the data
  initData();

 // train the network
    for($j = 0;$j <= $numEpochs;$j++)
    {

        for($i = 0;$i<$numPatterns;$i++)
        {

            //select a pattern at random
	    //srand();	
            $patNum = rand(0,$numPatterns-1);		 	   	

            //calculate the current network output
            //and error for this pattern
            calcNet();

            //change network weights
            WeightChangesHO();
            WeightChangesIH();
        }

        //display the overall network error
        //after each epoch
        calcOverallError();

	if (gmp_mod($j,50) == 0) 
	print "epoch = ".$j."  RMS Error = ".$RMSerror."</br>";

    }

    //training has finished
    //display the results
    displayResults();

 }

//============================================================
//********** END OF THE MAIN PROGRAM **************************
//=============================================================






//***********************************
function calcNet()
{
 global $numHidden;
 global $hiddenVal;
 global $weightsIH;
 global $weightsHO;
 global $trainInputs;
 global $trainOutput;
 global $numInputs;
 global $patNum;
 global $errThisPat;
 global $outPred;


 //calculate the outputs of the hidden neurons
 //the hidden neurons are tanh

 for($i = 0;$i<$numHidden;$i++)
 {
  $hiddenVal[$i] = 0.0;

  for($j = 0;$j<$numInputs;$j++)
   {
    $hiddenVal[$i] = $hiddenVal[$i] + ($trainInputs[$patNum][$j] * $weightsIH[$j][$i]);
   }

   $hiddenVal[$i] = tanh($hiddenVal[$i]);

 }

 //calculate the output of the network
 //the output neuron is linear
   $outPred = 0.0;

   for($i = 0;$i<$numHidden;$i++)
   {
    $outPred = $outPred + $hiddenVal[$i] * $weightsHO[$i];
   }
    //calculate the error
    $errThisPat = $outPred - $trainOutput[$patNum];
 }


//************************************
 function WeightChangesHO()
 //adjust the weights hidden-output
 {
  global $numHidden;
  global $LR_HO;
  global $errThisPat; 
  global $hiddenVal;
  global $weightsHO;

   for($k = 0;$k<$numHidden;$k++)
   {
    $weightChange = $LR_HO * $errThisPat * $hiddenVal[$k];
    $weightsHO[$k] = $weightsHO[$k] - $weightChange;

    //regularisation on the output weights
    if ($weightsHO[$k] < -5)
    {
        $weightsHO[$k] = -5;
    }
    elseif ($weightsHO[$k] > 5)
    {
        $weightsHO[$k] = 5;
    }
   }
 }


//************************************
 function WeightChangesIH()
 //adjust the weights input-hidden
 {
  global $trainInputs;
  global $numHidden;
  global $numInputs;
  global $hiddenVal;
  global $weightsHO;
  global $weightsIH;
  global $LR_IH;
  global $patNum;
  global $errThisPat; 

  for($i = 0;$i<$numHidden;$i++)
  {
   for($k = 0;$k<$numInputs;$k++)
   {
    $x = 1 - ($hiddenVal[$i] * $hiddenVal[$i]);
    $x = $x * $weightsHO[$i] * $errThisPat * $LR_IH;
    $x = $x * $trainInputs[$patNum][$k];
    $weightChange = $x;
    $weightsIH[$k][$i] = $weightsIH[$k][$i] - $weightChange;
   }
  }
 }


//************************************
 function initWeights()
 {
  global $numHidden;
  global $numInputs;
  global $weightsIH;
  global $weightsHO;

  for($j = 0;$j<$numHidden;$j++)
  {
    $weightsHO[$j] = (rand()/getrandmax() - 0.5)/2;

    for($i = 0;$i<$numInputs;$i++)
    {
    $weightsIH[$i][$j] = (rand()/getrandmax() - 0.5)/5;
    }
  }

 }


//************************************
 function initData()
 {
  global $trainInputs; 
  global $trainOutput;

  print "initialising data</br>";

  // the data here is the XOR data
  // it has been rescaled to the range
  // [-1][1]
  // an extra input valued 1 is also added
  // to act as the bias
  // the output must lie in the range -1 to 1

  $trainInputs[0][0]  = 1;
  $trainInputs[0][1]  = -1;
  $trainInputs[0][2]  = 1;    //bias
  $trainOutput[0] = 1;

  $trainInputs[1][0]  = -1;
  $trainInputs[1][1]  = 1;
  $trainInputs[1][2]  = 1;       //bias
  $trainOutput[1] = 1;

  $trainInputs[2][0]  = 1;
  $trainInputs[2][1]  = 1;
  $trainInputs[2][2]  = 1;        //bias
  $trainOutput[2] = -1;

  $trainInputs[3][0]  = -1;
  $trainInputs[3][1]  = -1;
  $trainInputs[3][2]  = 1;     //bias
  $trainOutput[3] = -1;

 }


//************************************
 function displayResults()
 {
  global $numPatterns;
  global $patNum;
  global $outPred;
  global $trainOutput;

  for($i = 0;$i<$numPatterns;$i++)
   {
    $patNum = $i;
    calcNet();
    print "pat = ".($patNum+1)." actual = ".$trainOutput[$patNum]." neural model = ".$outPred."</br>";
   }
 }


//************************************
function calcOverallError()
{
 global $numPatterns;
 global $patNum;	
 global $errThisPat;
 global $RMSerror;	

 $RMSerror = 0.0;
 for($i = 0;$i<$numPatterns;$i++)
  {
   $patNum = $i;
   calcNet();
   $RMSerror = $RMSerror + ($errThisPat * $errThisPat);
  }
   $RMSerror = $RMSerror/$numPatterns;
   $RMSerror = sqrt($RMSerror);
 }

?>