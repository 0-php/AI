// From Megaupload auto-fill captcha

function NeuralNet(){
	this.h_layer = [];
	this.o_layer = [];
}

NeuralNet.prototype.create_layer = function(weights){
	var layer = [];

	for(var i = 0; i < weights.length; i++){
		var neuron = new Neuron;
		neuron.threshold = weights[i][0];
		neuron.weights = weights[i][1];

		layer.push(neuron);
	}

	return layer;
}

NeuralNet.prototype.feed = function(inputs){
	var h_outputs = [];
	
	for(var i = 0; i < this.h_layer.length; i++){
		this.h_layer[i].feed(inputs);
		h_outputs.push(this.h_layer[i].output());
	}

	for(var i = 0; i < this.o_layer.length; i++)
		this.o_layer[i].feed(h_outputs);
}

NeuralNet.prototype.output = function(){
	var output = [];

	for(var i = 0; i < this.o_layer.length; i++)
		output.push(this.o_layer[i].output());

	return output;
}

NeuralNet.prototype.test = function(inputs){
	this.feed(inputs);
	return this.output();
}


function Neuron(){
	this.activation = 0;
	this.bias = -1;
	this.threshold = 0;
	this.weights = [];
}

Neuron.prototype.feed = function(inputs){
	this.activation = 0;
	for(var i = 0; i < inputs.length; i++)
		this.activation += inputs[i] * this.weights[i];
	this.activation += this.bias * this.threshold;
}

Neuron.prototype.output = function(){
	return 1 / (1 + Math.exp(-this.activation));
}

function create_net(){
	//pre-calculated weights
	//import 'captcha_megaupload.json'
	var net = new NeuralNet;
	net.h_layer = net.create_layer(h_weights);
	net.o_layer = net.create_layer(o_weights);

	return net;
}

function guess_letter(net, receptors, digit){
	var output_map = ['1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];

	var output = net.test(receptors);

	var highest = 0;
	for(var i = 0; i < output.length; i++){
		if (output[i] > output[highest] && ((!digit && i >= 9) || (digit && i < 9)))
			highest = i;
	}

	if (!digit && highest == 0)
		highest = 9;

	return output_map[highest]
}

function check_receptors(image){
	receptors = [];
	for(var x = 0; x < 33; x += 3)
		for(var y = 0; y < 30; y += 2)
			if(image.get_pixel(x, y) >= 128)
				receptors.push(1);
			else
				receptors.push(0);

	return receptors;
}
