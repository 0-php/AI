// From Megaupload auto-fill captcha
// @copyright      2009 Shaun Friedle
// @license        GPL version 3; http://www.gnu.org/copyleft/gpl.html

function NeuralNet(){
	this.h_layer = [];
	this.o_layer = [];
}

NeuralNet.prototype.create_layer = function(weights){
	var layer = [];

	for(var i=0; i<weights.length; i++){
		var neuron = new Neuron;
		neuron.threshold = weights[i][0];
		neuron.weights = weights[i][1];
		layer.push(neuron);
	}

	return layer;
}

NeuralNet.prototype.feed = function(inputs){
	var h_outputs = [];

	for(var i=0; i<this.h_layer.length; i++){
		this.h_layer[i].feed(inputs);
		h_outputs.push(this.h_layer[i].output());
	}
	for(var i=0; i<this.o_layer.length; i++)
		this.o_layer[i].feed(h_outputs);
}

NeuralNet.prototype.output = function(){
	var output = [];

	for(var i=0; i<this.o_layer.length; i++)
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
	for(var i=0; i<inputs.length; i++)
		this.activation += inputs[i] * this.weights[i];
	this.activation += this.bias * this.threshold;
}

Neuron.prototype.output = function(){
	return 1 / (1 + Math.exp(-this.activation));
}