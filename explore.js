current_travelling_type = '';

/*
	Exploring any data
*/
function explore(data, meta, callback, callback_parameters){
	if(typeof data == 'undefined')
		data = global();
	travel(data);
}

/*
	Function for travelling any type of data from anywhere (variables, files, network)
	Final goal - global map of available resources
*/
function travel(data){
	map = {};
	current_travelling_type = typeof data;
	size = data.lenth ? data.length : 1;
	console.info('Data: ' + data + ', size: ' + size);
	//for(element in data)
	//	safe_fast_recursion(travel, element);
	//javascript_types.map(travel_isjstype)
	if(current_travelling_type == 'object'){
		object_traverse(data);
	}
}


function travel_isjstype(value){
	if(current_travelling_type == value)
		eval('' + value)
}

//Gives global data
function global(){
	return window;
}