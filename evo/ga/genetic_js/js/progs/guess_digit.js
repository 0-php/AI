guess_digit = {
	expected: random_int(100000), //0 to 1000 (maxArg)
	numbers: [],
	
	fillArrayOfNumbers: function(){
		for(var i=0; i<30; i++)
			this.numbers.push(random_int(10));
	},
	
	differenceBetweenNumbers: function(a, b){ //console.info('a: '+a+', b: '+ b[0]);
		if(a > b)
			return a - b;
		else
			return b - a;
	},

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