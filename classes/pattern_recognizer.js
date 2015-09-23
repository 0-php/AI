//Something cool, may contain influence of Hierarhical Temporary Memory, Bucket-Brigade Algorithm...
//Main idea: self-configurating network with prediction fitness
//Dependencies: utility.js -> dump(), math.js -> random_int()

pattern_recognizer = {
	neurons: [],
	effectors: [],
	links: [],
	symbols_list: '0123456789+-'.split(''),
	neurons_count: 50,
	avg_links_per_neuron: 20,
	epochs: 10,

	init: function(data){
		for(i=0; i<this.neurons_count; i++)
			this.neuron_add(0, null);

		neurons = this.neurons;
		links = this.links;

		this.input = data.input;
		this.output = data.output;

		//Adding sensors
		for(i=0; i<this.input.length; i++)
			for(j=0; j<this.input[i].length; j++)
				for(k=0; k<this.symbols_list.length; k++)
					this.neuron_add(0, null, this.symbols_list[k], j);
		//Ading effectors
		for(i=0; i<this.output[0].length; i++)
			for(j=0; j<this.symbols_list.length; j++)
				this.neuron_add(0, null, this.symbols_list[j], i, 1);

		//Random links between neurons
		n = this.neurons.length - 1;
		for(i=0; i<this.avg_links_per_neuron * n; i++)
			this.link_add(random_int(n), random_int(n), null);
	},

	//Teaches network by examples, choosing randomly
	learn: function(){
		for(i=0; i<this.epochs; i++){
			example_id = random_int(this.input.length);
			this.load_random_input(this.input[example_id]);
			this.run();
			this.reward_output(output[example_id]);
		}
	},

	load_random_input: function(input){
		//Activation of sensors according to inputs
		input_id = random_int(this.input.length);
		for(j=0; j<this.input[input_id].length; j++)
			this.neuron_activate(j, this.input[input_id][j]);
	},

	run: function(){
		for(i=0; i<this.epochs; i++){ console.info('Epoch ' + i);
			//Activating neurons, if count of active inputs more than threshold
			for(neuron_id in neurons){
				inputs_activated = 0;
				inputs_count = 0;
				for(link_id in links){
					if(links[link_id].to == neuron_id){
						if(neurons[links[link_id].from].activated)
							inputs_activated++;
					}
				}
				if(neurons[neuron_id].threshold < inputs_activated)
					neurons[neuron_id].activated = true; //console.info('Activation - threshold exceeded');
			}
			//Inhibition

			//Getting effectors
			this.effectors = [];
			for(neuron_id in neurons){
				neuron = neurons[neuron_id];
				if(neuron.is_output)
					this.effectors[this.effectors.length] = neuron;
			}

			//Getting output
			output = {};
			for(effector_id in this.effectors){
				eff = this.effectors[effector_id];
				pos = eff.position;
				if(output[pos])
					output[pos] += eff.symbol;
				else
					output[pos] = eff.symbol;
			}
			//Visualize output
			output_text = '';
			for(position in output)
				output_text += '(' + position + ')';
			$('#output').text(output_text); console.info('Output: ' + output_text);

			//Evaluating results

			for(j=0; j<this.output[0].length; j++){
				for(k=0; k<this.symbols_list.length; k++){
					for(effector_id in this.effectors){
						eff = this.effectors[effector_id];
						if(eff.position == j && eff.symbol == k){
							this.neuron_evaluate(eff);
						}
					}
				}
			}

		}
	},

	reward_output: function(output){

	},

	//Executed each epoch on neurons with outputs, strenghtens weights of previously active synapses if result is better, weaks otherwise
	neuron_evaluate: function(neuron){

	},

	neuron_activate: function(position, symbol){ //console.info('pos: '+position+', symbol: '+symbol);
		for(n in this.neurons)
			if(this.neurons[n].position == position && this.neurons[n].symbol == symbol)
				this.neurons[n].activated = 1;
	},

	//Returns id of random neuron, that not sensor/effector
	random_not_used_neuron_id: function(){
		id = false;
		neuron_ids = [];
		for(n in this.neurons)
			neuron_ids[neuron_ids.length] = n.neuron_id;
		while(id === false){
			id = random_int(this.neurons.length);
			for(n_id in neuron_ids)
				if(n_id == id)
					id = false;
		}
		return id;
	},

	neuron_add: function(activated, threshold, symbol, position, is_output){
		this.neurons[this.neurons.length] = {
			'activated': activated,
			'threshold': threshold || random_int(50),
			'symbol': symbol,
			'position': position,
			'is_output': is_output
		};
	},

	link_add: function(from, to, weight){ //console.info('link added');
		 this.links[this.links.length] = {
			'from': from,
			'to': to,
			'weight': weight || Math.random()
		}
	}
}