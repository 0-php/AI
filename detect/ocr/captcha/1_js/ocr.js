// ==UserScript==
// @name           Megaupload auto-fill captcha
// @version        0.1.1
// @namespace      http://herecomethelizards.co.uk/mu_captcha/
// @description    Auto-fills the megaupload captcha and auto-starts download
// @include        http://megaupload.com/*
// @include        http://www.megaupload.com/*
// @include        http://megaporn.com/*
// @include        http://www.megaporn.com/*
// @copyright      2009 Shaun Friedle
// @license        GPL version 3; http://www.gnu.org/copyleft/gpl.html
//
// ==/UserScript==

function coord_to_index(x, y){
	return x * 4 + y * 4 * this.width;
}

function set_pixel(x, y, colour){
	this.data[this.coord_to_index(x, y)] = colour;
	this.data[this.coord_to_index(x, y) + 1] = colour;
	this.data[this.coord_to_index(x, y) + 2] = colour;
	this.data[this.coord_to_index(x, y) + 3] = 255;
}

function get_pixel(x, y, colour){
	return this.data[this.coord_to_index(x, y)];
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

function decode(captcha){
	var canvas = captcha.to_canvas();
	var image = canvas.getContext('2d').getImageData(0, 0, canvas.width, canvas.height);

	var blocks = get_blocks(image);
	var black_blocks = blocks[0];
	var white_blocks = blocks[1];

	var large_black = get_large_blocks(black_blocks, 4);
	var large_white = get_large_blocks(white_blocks, 3);

	var small_black = [];
	for(var i = 0; i < black_blocks.length; i++){
		var in_large = false;
		for(var j = 0; j < large_black.length; j++)
			if(large_black[j].contains(black_blocks[i].items[0]))
				in_large = true;
		if(!in_large)
			small_black.push(black_blocks[i]);
	}

	var small_white = [];
	for(var i = 0; i < white_blocks.length; i++){
		var in_large = false;
		for(var j = 0; j < large_white.length; j++)
			if(large_white[j].contains(white_blocks[i].items[0]))
				in_large = true;
		if(!in_large)
			small_white.push(white_blocks[i]);
	}

	try {
		chars = get_chars(image, large_black, large_white, small_black, small_white);
	} catch(e){
		return false;
	}
	console.info('CHARS: '+chars);
	net = create_net();
	code = '';
	for(var i = 0; i < chars.length; i++){
		receptors = check_receptors(chars[i]);
		code += guess_letter(net, receptors, i == 3);
	}

	return code;
  }


function get_blocks(image){
	function sort_block(pixel1, pixel2){
		return pixel1[0] - pixel2[0];
	}

	function min_size_count(blocks, min_size){
		var count = 0;
		for(var i = 0; i < blocks.length; i++)
			if(blocks[i].items.length > min_size)
				count++;
		return count;
	}

	var canvas = document.createElement('canvas');
	canvas.width = image.width + 2;
	canvas.height = image.height + 2;
	ctx = canvas.getContext('2d');
	ctx.fillStyle = 'rgb(255,255,255)';
	ctx.fillRect(0, 0, canvas.width, canvas.height);
	ctx.putImageData(image, 1, 1);

	image = ctx.getImageData(0, 0, canvas.width, canvas.height);
	image.coord_to_index = coord_to_index;
	image.set_pixel = set_pixel;
	image.get_pixel = get_pixel;

	found = new Block();
	bg = get_colour_block(image, 0, 0);

	for(var i = 0; i < bg.items.length; i++)
		found.add(bg.items[i]);

	black_blocks = [];
	white_blocks = [];
	for(var x = 0; x < image.width; x++)
		for(var y = 0; y < image.height; y++){
			var colour = image.get_pixel(x, y);
			var block = null;

			if(found.contains([x, y]))
				continue;

			if(colour == 0){
				block = get_colour_block(image, x, y);
				block.items.sort(sort_block);
				if(block.items.length >= 5)
					black_blocks.push(block);
			}
			if(colour == 255){
				block = get_colour_block(image, x, y);
				block.items.sort(sort_block);
				if(block.items.length >= 5)
					white_blocks.push(block);
			}

			if(block != null)
				for(var i = 0; i < block.items.length; i++)
					found.add(block.items[i]);
		}

	while(min_size_count(black_blocks, 10) < 4){
		var wide = 0;
		for(var i = 0; i < black_blocks.length; i++)
			if(black_blocks[i].items.length > black_blocks[wide].items.length)
				wide = i;

		var blocks = split_block(black_blocks[wide]);
		black_blocks.splice(wide, 1);
		black_blocks.splice(wide, 0, blocks[1]);
		black_blocks.splice(wide, 0, blocks[0]);
	}

	return [black_blocks, white_blocks];
}


function get_chars(image, large_black, large_white, small_black, small_white){
	chars = [];

	for(var i = 0; i < large_black.length; i++){
		var canvas = document.createElement('canvas');
		canvas.width = image.width;
		canvas.height = image.height;
		ctx = canvas.getContext('2d');
		ctx.fillRect(0, 0, canvas.width, canvas.height);

		var image = ctx.getImageData(0, 0, canvas.width, canvas.height);
		image.coord_to_index = coord_to_index;
		image.set_pixel = set_pixel;
		image.get_pixel = get_pixel;

		for(var j = 0; j < large_black[i].items.length; j++)
			image.set_pixel(large_black[i].items[j][0], large_black[i].items[j][1], 255);

		for(var j = 0; j < small_white.length; j++){
			if(i > 0)
				if(small_white[j].items[0][0] < large_black[i-1].items[large_black[i-1].items.length-1][0] && small_white[j].items[small_white[j].items.length-1][0] > large_black[i].items[0][0])

					for(var k = 0; k < small_white[j].items.length; k++)
						image.set_pixel(small_white[j].items[k][0], small_white[j].items[k][1], 255);

			if(i < large_black.length - 1)
				if(small_white[j].items[0][0] < large_black[i].items[large_black[i].items.length-1][0] && small_white[j].items[small_white[j].items.length-1][0] > large_black[i+1].items[0][0])

					for(var k = 0; k < small_white[j].items.length; k++)
						image.set_pixel(small_white[j].items[k][0], small_white[j].items[k][1], 255);
		  }

		for(var j = 0; j < small_black.length; j++){
			if(small_black[j].items.length < 30)
				continue;

			var common_columns = 0;
			for(var k = 0; k < small_black[j].items.length; k++)
				if(large_black[i].items[0][0] <= small_black[j].items[k][0] && small_black[j].items[k][0] <= large_black[i].items[large_black[i].items.length-1][0])
					common_columns++;

			if(common_columns < 10)
				continue;

			if(i > 0){
				var common_previous = 0;
				for(var k = 0; k < small_black[j].items.length; k++)
					if(large_black[i-1].items[0][0] <= small_black[j].items[k][0] && small_black[j].items[k][0] <= large_black[i-1].items[large_black[i-1].items.length-1][0])
						common_previous++;

				if(common_columns < common_previous)
					continue;
			  }

			if(i < large_black.length - 1){
				var common_next = 0;
				for(var k = 0; k < small_black[j].items.length; k++)
					if(large_black[i+1].items[0][0] <= small_black[j].items[k][0] && small_black[j].items[k][0] <= large_black[i+1].items[large_black[i+1].items.length-1][0])
						common_next++;

				if(common_columns < common_next)
					continue;
			  }

			for(var k = 0; k < small_black[j].items.length; k++)
				image.set_pixel(small_black[j].items[k][0], small_black[j].items[k][1], 255);
		  }

		ctx.putImageData(image, 0, 0);

		var canvas2 = document.createElement('canvas');
		canvas2.width = 200;
		canvas2.height = 200;
		ctx2 = canvas2.getContext('2d');
		ctx2.fillRect(0, 0, canvas2.width, canvas2.height);

		if(i % 2)
			ctx2.rotate(0.08726646);
		else
			ctx2.rotate(6.19591884);

		ctx2.drawImage(canvas, 50, 75);

		var start_points = [[50, 65], [50, 70], [85, 55], [90, 80]];
		canvas = crop_canvas(canvas2, start_points[i]);

		image = ctx.getImageData(0, 0, canvas.width, canvas.height);
		image.coord_to_index = coord_to_index;
		image.set_pixel = set_pixel;
		image.get_pixel = get_pixel;

		chars.push(image);
	}

	return chars;
}


function get_colour_block(image, x, y){
	function within(x, y){
		return 0 <= x && x < image.width && 0 <= y && y < image.height;
	}

	var colour = image.get_pixel(x, y);
	var edge = [[x, y]];
	var block = new Block();
	block.add([x, y]);

	while(edge.length > 0){
		var newedge = [];
		for(var i = 0; i < edge.length; i++){
			var x = edge[i][0];
			var y = edge[i][1];
			var adjacent = [[x+1, y], [x-1, y], [x, y+1], [x, y-1]];
			for(var j = 0; j < adjacent.length; j++){
				var s = adjacent[j][0];
				var t = adjacent[j][1];

				if(within(s, t) && !block.contains([s, t]) && image.get_pixel(s, t) == colour){
					block.add([s, t]);
					newedge.push([s, t]);
				}
			}
		}
		edge = newedge;
	}
	return block;
}


function get_large_blocks(blocks, count){
	function sort_set(set1, set2){
		return set1.items[0][0] - set2.items[0][0];
	}

	var large = [];
	for(var i = 0; i < blocks.length; i++){
		if(large.length < count){
			large.push(blocks[i]);
		} else {
			var greatest_diff = [0, null];
			for(var j = 0; j < large.length; j++){
				var diff = blocks[i].items.length - large[j].items.length;
				if(diff > 0 && (diff > greatest_diff[0] || greatest_diff[1] == null))
					greatest_diff = [diff, j];
			}

			if(greatest_diff[1] != null)
				large[greatest_diff[1]] = blocks[i];
		}
	}
	return large.sort(sort_set);
}


function guess_letter(net, receptors, digit){
	var output_map = ['1','2','3','4','5','6','7','8','9','a','b','c','d','e','f','g','h','k','m','n','p','q','r','s','t','u','v','w','x','y','z'];

	var output = net.test(receptors);

	var highest = 0;
	for(var i = 0; i < output.length; i++){
		if(output[i] > output[highest] && ((!digit && i >= 9) || (digit && i < 9)))
			highest = i;
	}

	if(!digit && highest == 0)
		highest = 9;

	return output_map[highest]
}


function split_block(block){
	histogram = [];

	var start = block.items[0][0] + 5;
	var end = block.items[block.items.length-1][0] - 5;

	for(var i = 0; i < block.items.length; i++)
		if(start <= block.items[i][0] && block.items[i][0] <= end){
			if(histogram[block.items[i][0]] == undefined)
				histogram[block.items[i][0]] = 0;

			histogram[block.items[i][0]]++;
		  }

	var low = start;
	for(var i = 0; i < histogram.length; i++)
		if(histogram[i] != undefined && histogram[i] < histogram[low])
			low = i;

	left = new Block();
	right = new Block();

	for(var i = 0; i < block.items.length; i++){
		if(block.items[i][0] <= low)
			left.add(block.items[i]);
		if(block.items[i][0] >= low)
			right.add(block.items[i]);
	  }

	return [left, right];
  }


function load_image(data){
	var gif = new GIF();
	gif.from_array(data_array(data));

	var textbox = document.getElementById('captchafield');

	textbox.style.fontWeight = 'normal';
	textbox.style.fontSize = '7pt';
	textbox.value = 'working...';

	textbox.value = decode(gif);
  }


function data_array(data){
	var data_array = [];

	for(var i = 0; i < data.length; i++)
		data_array.push(data[i].charCodeAt(0) & 0xff);

	return data_array;
  }

function start(){
	var src = $('img').attr('src');
	$.ajax({
		type: 'GET',
		//url: 'http://localhost/tools/all/parsec/tests/js/!nn/captcha/img/'+image.src,
		url: src,
		dataType: 'text/plain; charset=x-user-defined',
		success: function(data, response){
			load_image(data);
		}
	});
}

function create_net(){
	//pre-calculated weights
	var net = new NeuralNet;
	net.h_layer = net.create_layer(h_weights);
	net.o_layer = net.create_layer(o_weights);

	return net;
}