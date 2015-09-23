pseudobio = {

	suesors: [],
	effectors: [],
	neurons: [], //Sensitivity, synapses [probability of link, 
	synapses: [], //Weight, persistance
	
	init: function(){
		for(i=0; i<this.neurons_count; i++)
			this.neuron();
	}
	
	run: function(){
		if(this.data != null){
			for(i=0; i<this.data.length; i++)
				for(i<
		}
	},
	
	add_data: function(data){
		this.data = data;
		this.run();
		this.data = null;
	},
	
	neuron: function(energy){
		neuron = {
			energy: energy,
			sensitivity: Math.random(0, 1)
		}
		this.neurons[this.neurons.length] = neuron;
	},
	
	synapse: function(){
		
	}
	
}