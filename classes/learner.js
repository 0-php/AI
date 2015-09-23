
learner = {
	//sensors_num: 1;
	data: [],
	sensors: [],
	neurons: [],
	links: [],
	energy: 1000,

	learn: function(data){
		this.input(data);
		for(d in data){

		}
	},

	run: function(){

	},

	neuron_add: function(setup){ //potential || 0, threshold || null, is_sensor || 0, source || null){
		neuron = { 'threshold': setup.threshold || Math.random(0, 1), 'potential': setup.potential || 0 };
		if(setup.is_sensor == 1){
			neuron.is_sensor = 1;
			neuron.source = setup.source;
			this.sensors[this.sensors.length] = neuron;
		}
		this.neurons[this.neurons.length] = neuron;
	},

	link_add: function(setup){
		link: { weight: setup.weight || Math.random(0, 1), from: setup.from ||
	}

	input: function(data){
		this.data = data;
		for(i=0; i<data.length; i++){
			s = false;
			for(sensor in this.sensors)
				if(this.sensors[sensor].source == data[i])
					s = this.sensors[sensor];
			if(s != false)
				s.potential = 1;
			else
				this.neuron_add({ 'potential': 1, 'threshold': null, 'is_sensor': 1, 'source': data[i] });
		}
		this.log(this.neurons);
	},

	log: function(info){ console.info(info);
		$('#log').html(dump(info));
	}
}

/**
 * Function : dump()
 * Arguments: The data - array,hash(associative array),object
 *    The level - OPTIONAL
 * Returns  : The textual representation of the array.
 * This function was inspired by the print_r function of PHP.
 * This will accept some data as the argument and return a
 * text that will be a more readable version of the
 * array/hash/object that is given.
 * Docs: http://www.openjs.com/scripts/others/dump_function_php_print_r.php
 */
function dump(arr,level){
	var dumped_text = "";
	if(!level)
		level = 0;

	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0; j<level + 1; j++)
		level_padding += "    ";

	if(typeof(arr) == 'object'){ //Array/Hashes/Objects
		for(var item in arr){
			var value = arr[item];

			if(typeof(value) == 'object'){ //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...<br>";
				dumped_text += dump(value, level + 1);
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"<br>";
			}
		}
	} else { //Stings/Chars/Numbers etc.
		dumped_text = "===>" + arr + "<===("+typeof(arr) + ")";
	}
	return dumped_text;
}