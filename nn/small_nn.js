 function run(input){
	var net = {
		"layers": [{
			"0": {},
			"1": {}
		}, {
			"0": {
				"bias": 5.12448975576,
				"weights": {
					"0": -3.5913170003,
					"1": -3.5945029361
				}
			},
			"1": {
				"bias": 1.44806195142,
				"weights": {
					"0": -5.0210994237,
					"1": -5.0557360463
				}
			},
			"2": {
				"bias": 0.6550171276,
				"weights": {
					"0": -3.98426148256,
					"1": -4.0203572373
				}
			}
		}, {
			"0": {
				"bias": -3.0933229796,
				"weights": {
					"0": 7.3289410339,
					"1": -5.6996474316,
					"2": -3.8797992536
				}
			}
		}]
	};

	for(var i = 1; i < net.layers.length; i++){
		var layer = net.layers[i];
		var output = {};

		for(var id in layer){
			var node = layer[id];

			var sum = node.bias;
			for(var iid in node.weights)
				sum += node.weights[iid] * input[iid];
			output[id] = (1 / (1 + Math.exp(-sum)));
		}
		input = output;
	}
	return output;
}

net.train([
	{ input: [0.7, 0.1, 0.3], output: [1] },
	{ input: [1.0, 0.8, 0.7], output: [0] },
	{ input: [0.5, 0.6, 0.7], output: [0] }
	]);

var output = net.run([0.5, 0.5, 0.6]);   // [0.001]