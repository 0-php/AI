Genetic = {
Chromosome: function(bitsPerValue, numberOfValues){
	this.bitString = new String();
    this.fitnessValue = 0;
    this.numberOfValues = numberOfValues;
    this.bitsPerValue = bitsPerValue;
    this.maxInRange = /*Math.pow(2, */bitsPerValue/*);*/
    this.args = [];
    // init with a random value
    for(var i = 0; i < bitsPerValue * numberOfValues; ++i)
        this.bitString += (Math.random() > 0.5) ? 1 : 0;

    // Take the binary string 01010110, get the decimal value and then put it between the ranges
	// Converts binary to decimal
    this.value = function(value, fitness){ //console.info('value '+value);
        var str = this.bitString.substring(value * this.bitsPerValue, (value + 1) * this.bitsPerValue); //Getting next chunk from bitString
        var intValue = parseInt(str, 2); //console.info('intValue: '+intValue);
        result = fitness.getArg(intValue, this.maxInRange); //console.info(this.maxInRange);
		return result;
    };

    this.computeFitness = function(fitness){
        for(var i=0; i < this.numberOfValues; ++i)
            this.args[i] = this.value(i, fitness);
        return fitness.fitness(this.args);
    };

    this.mutate = function(){
        var flipPoint = Math.floor(Math.random() * this.bitString.length);
        var end = this.bitString.substring(flipPoint + 1);
        var flip = (this.bitString.charAt(flipPoint) == "1") ? "0" : "1";
        this.bitString = this.bitString.substring(0, flipPoint);
        this.bitString += flip;
        this.bitString += end;
    };

    this.crossover = function(other){
        var crossoverPoint = Math.random() * this.bitString.length;
        this.bitString = this.bitString.substring(0, crossoverPoint) + other.bitString.substring(crossoverPoint);
    };
},

Population: function(size, fitness, mutationRate, crossoverRate){
    this.people = [size];
    this.fitness = fitness;
    this.mutationRate = mutationRate;
    this.crossoverRate = crossoverRate;

    var bits = fitness.maxArg().toString(2).length + 1;
    for(var i = 0; i < size; ++i)
        this.people[i] = new Genetic.Chromosome(bits, fitness.numberOfArgs());

    this.comparePeople = function(a, b){
		return a.fitnessValue - b.fitnessValue;
	};

    this.buildNextGeneration = function(){
        var peopleSize = this.people.length;
        // Calculate the fitness values of all the items and then sort by rank
        for(var i = 0; i < peopleSize; ++i)
            this.people[i].fitnessValue = this.people[i].computeFitness(this.fitness);
        this.people.sort(this.comparePeople);

        // replace those that get crossovered or mutated
        var loosers = this.crossoverRate + this.mutationRate;
        var remaining = Math.round(peopleSize * (1 - loosers));
        for(var i = remaining; i < peopleSize; ++i){
            this.people[i].bitString = this.people[peopleSize - i].bitString;
            if((Math.random() * loosers) > this.mutationRate){
                this.people[i].mutate();
            } else {
                var choice = Math.round(Math.random() * remaining);
                this.people[i].crossover(this.people[choice]);
            }
        }
    };
},

run: function(fitness){
    var generations = document.forms[0].generations.value;
    var populationSize = document.forms[0].populationSize.value;
    var mutationRate = parseFloat(document.forms[0].mutationRate.value);
    var crossoverRate = parseFloat(document.forms[0].crossoverRate.value);

    // Check args
    if(crossoverRate + mutationRate > 1){
        alert("cross over and mutation rate combined need to be smaller then 1.0");
        return;
    }
    if(populationSize * crossoverRate + mutationRate < 1){
        alert("populationSize * crossoverRate + mutationRate needs to be smaller then 1.0");
        return;
    }

    var population = new this.Population(populationSize, fitness, mutationRate, crossoverRate);

    var log = [];
    var logStr = [];
    //try {
        for(var i = 0; i < generations; ++i){ console.info('Generation ' + i);
            population.buildNextGeneration();
            if(log[log.length - 1] != population.people[0].fitnessValue){
                log[log.length] = population.people[0].fitnessValue;
                logStr[logStr.length] = "Generation " + i +": <b>" + population.people[0].fitnessValue //+ "</b> (" + bes;
            }
        }
    /*}/* catch(e){
        alert("When executing function:" + e.message);
        return;
    }*/

    var best = population.people[0];
    document.forms[0].best.value = "";
    for(var i = 0; i < fitness.numberOfArgs(); ++i)
        document.forms[0].best.value += best.value(i, fitness) + "; ";
    document.forms[0].maxFitness.value = best.fitnessValue;

    var canvas = document.getElementById('graph');

	var w = canvas.width;
	var h = canvas.height;
	var ctx = canvas.getContext('2d');
	ctx.clearRect(0, 0, w, h);

	// draw horizontal lines
	for(var i = 0; i < h; ++i)
		ctx.fillRect(0, 10 + i * 10, 1, w);

	// Create background gradient
	var linearGradient = ctx.createLinearGradient(0, 0, 0, w);
	linearGradient.addColorStop(0, '#00ABEB');
	linearGradient.addColorStop(0.75, '#fff');
	linearGradient.addColorStop(1, '#fff');
	ctx.fillStyle = linearGradient;
	ctx.fillRect(0,0,w,h);

	ctx.fillStyle = '#fef';
	for(var i = 0; i < h; i += 10)
		ctx.fillRect(2, 10 + i, w - 2, 1);

	linearGradient = ctx.createLinearGradient(0, 0, 0, w);
	linearGradient.addColorStop(1, '#00ABEB');
	linearGradient.addColorStop(0.75, '#000');
	linearGradient.addColorStop(0, '#000');
	ctx.fillStyle = linearGradient;
	ctx.strokeRect(1, 1, w - 2, h - 2);
	for(var i = 0; i < log.length; ++i)
		ctx.fillRect(5 + i * 5, h - log[i] - 2, 3, log[i]);

    var graphData = document.getElementById('graphdata');
    graphData.innerHTML = logStr.join("<br>");

    var args = [];
    for(var i = 0; i < best.numberOfValues; ++i)
        args[i] = best.value(i, fitness);
    fitness.paint(args);

    delete fitness;
    delete population;
}
}