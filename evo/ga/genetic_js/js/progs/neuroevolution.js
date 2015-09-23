neuroevolution = {
	
	vote: 1
	
	numbers: [],

	//Fitness of neuron is count of energy it's gained
    fitness: function(value){ //console.info(value);
		if(this.numbers.length == 0)
			this.fillArrayOfNumbers();
		diff = this.differenceBetweenNumbers(this.expected, value); //console.info('Diff: '+diff);
		return diff;
    },
    
    // the size of values that should be passed to fitness
    numberOfArgs: function(){
		return 1;
	},
    
    // the max value needed for the arguments
    maxArg: function(){
		return 100000;
	},
    
    // convert the current chromosome value which can have a maxValue into something fitness can use.
    getArg: function(value, maxValue){//console.info(value);
        return Math.round(value * (this.numbers.length - 1) / maxValue);
    },

    // Prints the solution
    paint: function(values){
		$('#result').html('Expected: <b>' + this.expected + '</b><br>Result: <b>' + JSON.stringify(values) + '</b>');
    }
}