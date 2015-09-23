<? 
require_once ("../core/class_neuralnetwork.php");

// Create a new neural network with 3 input neurons,
// 4 hidden neurons, and 1 output neuron
$n = new NeuralNetwork(3, 4, 1);
$n->setVerbose(false);

// Add test-data to the network. In this case,
// we want the network to learn the 'XOR'-function
$n->addTestData(array (-1, -1, 1), array (-1));
$n->addTestData(array (-1,  1, 1), array ( 1));
$n->addTestData(array ( 1, -1, 1), array ( 1));
$n->addTestData(array ( 1,  1, 1), array (-1));

// we try training the network for at most $max times
$max = 3;

// train the network in max 1000 epochs, with a max squared error of 0.01
while (!($success = $n->train(1000, 0.01)) && $max -- > 0) {
	echo "Nothing found...<hr />";
}

// print a message if the network was succesfully trained
if ($success) {
    $epochs = $n->getEpoch();
	echo "Success in $epochs training rounds!<hr />";
}

// in any case, we print the output of the neural network
for ($i = 0; $i < count($n->trainInputs); $i ++) {
	$output = $n->calculate($n->trainInputs[$i]);
	print "<br />Testset $i; ";
	print "expected output = (".implode(", ", $n->trainOutput[$i]).") ";
	print "output from neural network = (".implode(", ", $output).")\n";
}
?>