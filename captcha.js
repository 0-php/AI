// ==UserScript==
// @name					 Megaupload auto-fill captcha
// @version				0.1.1
// @namespace			http://herecomethelizards.co.uk/mu_captcha/
// @description		Auto-fills the megaupload captcha and auto-starts download
// @include				http://megaupload.com/*
// @include				http://www.megaupload.com/*
// @include				http://megaporn.com/*
// @include				http://www.megaporn.com/*
// @copyright			2009 Shaun Friedle
// @license				GPL version 3; http://www.gnu.org/copyleft/gpl.html
//
// ==/UserScript==
//Dependency: nn.js, data/captcha_megaupload.json

function Block()
	{
		this.keys = [];
		this.items = [];
	}

Block.prototype.add = function(value)
	{
		var key = value.toSource();
		var pos = this.search(key);
		if (this.keys[pos] != key)
			{
				this.keys.splice(pos, 0, key);
				this.items.splice(pos, 0, value);
			}
	}

Block.prototype.contains = function(value)
	{
		var key = value.toSource();
		var pos = this.search(key);
		return this.keys[pos] === key;
	}

Block.prototype.search = function(key)
	{
		var low = 0;
		var high = this.keys.length;
		while (low < high)
			{
				mid = low + Math.floor((high - low) / 2);
				if (this.keys[mid] < key)
						low = mid + 1;
				else
						high = mid;
			}

		return low;
	}


function GIF()
	{
		this.bytes = 0;
		this.code_dict = [];
		this.current_byte = 0;
		this.height = 0;
		this.gct = []
		this.pixels = []
		this.min_code_size = 0
		this.remaining = [0, 0]
		this.width = 0;
	}

GIF.prototype.from_array = function(data)
	{
		this.width = data[6] + data[7] * 0x100;
		this.height = data[8] + data[9] * 0x100;

		var gct_size = Math.pow(2, (data[10] & 7) + 1);

		for (var i = 0; i < gct_size; i++)
				this.gct.push([data[13+i*3], data[13+i*3+1], data[13+i*3+2]]);

		this.min_code_size = data[127];
		var code_size = this.min_code_size + 1;

		this.init_code_dict();

		this.current_byte = 128;
		var code = null;
		var previous = null;
		var next_code = Math.pow(2, this.min_code_size) + 2;
		while (1)
			{
				if (!this.bytes)
					{
						var byte_ = data[this.current_byte];
						if (byte_ == 0)
								break;
						this.bytes = byte_;
						this.current_byte++;
					}
				else
					{
						code = this.get_code(data, code_size);
						if (code < this.code_dict.length)
							{
								if (this.code_dict[code] == 'clear')
									{
										this.init_code_dict();
										code_size = this.min_code_size + 1;
										next_code =	Math.pow(2, this.min_code_size) + 2;
										previous = null;
										continue;
									}
								if (this.code_dict[code] == 'end')
										break;
								if (previous == null)
									{
										this.pixels = this.pixels.concat(this.code_dict[code]);
										previous = code;
										continue;
									}
							}

						if (code < this.code_dict.length)
								this.code_dict[next_code] = 
										this.code_dict[previous].concat(this.code_dict[code][0]);
						else
								this.code_dict[next_code] = 
										this.code_dict[previous].concat(this.code_dict[previous][0]);

						this.pixels = this.pixels.concat(this.code_dict[code]);

						previous = code;
						next_code++;
						if (next_code >= Math.pow(2, code_size) && code_size < 12)
								code_size++;
					}
			}
	}

GIF.prototype.get_code = function(data, code_size)
	{
		if (this.remaining[0] >= code_size)
			{
				var mask = Math.pow(2, code_size) - 1;
				var code = this.remaining[1] & mask;
				this.remaining[0] -= code_size;
				this.remaining[1] >>= code_size;
			}
		else
			{
				var read_bits = 0;
				var code = 0;
				while (read_bits < code_size)
					{
						var byte_ = data[this.current_byte];
						this.current_byte++;
						this.bytes--;

						var read_in = Math.min(8, code_size - read_bits);
						var read_new = Math.max(0, read_in - this.remaining[0]);
						var new_mask = Math.pow(2, read_new) - 1;
						var rem_mask = Math.pow(2, read_in - read_new) - 1;

						code |= (((byte_ & new_mask) << this.remaining[0]) |
										 (this.remaining[1] & rem_mask)) << read_bits;

						this.remaining = [this.remaining[0] - (read_in - read_new),
															this.remaining[1] >> (read_in - read_new)];

						this.remaining = [this.remaining[0] + (8 - read_new), 
								this.remaining[1] | (byte_ >> read_new << this.remaining[0])];

						read_bits += read_in;
					}
			}

		return code;
	}

GIF.prototype.get_pixel = function(x, y)
	{
		if (0 <= x && x < this.width && 0 <= y && y < this.height)
				return this.gct[this.pixels[x+y*this.width]];
		else
				return null;
	}

GIF.prototype.init_code_dict = function()
	{
		var code_dict = [];

		for (var i = 0; i < this.gct.length; i++)
				code_dict[i] = [i];

		code_dict[Math.pow(2, this.min_code_size)] = 'clear';
		code_dict[Math.pow(2, this.min_code_size)+1] = 'end';

		this.code_dict = code_dict;
	}

GIF.prototype.to_canvas = function()
	{
		var canvas = unsafeWindow.document.createElement('canvas');
		canvas.width = this.width;
		canvas.height = this.height;
		
		var image = canvas.getContext('2d').getImageData(0, 0, canvas.width, canvas.height);
		image.coord_to_index = coord_to_index;
		image.set_pixel = set_pixel;
		image.get_pixel = get_pixel;

		for (var x = 0; x < this.width; x++)
				for (var y = 0; y < this.height; y++)
						image.set_pixel(x, y, this.get_pixel(x, y)[0]);

		canvas.getContext('2d').putImageData(image, 0, 0);

		return canvas;
	}


function coord_to_index(x, y)
	{
		return x*4+y*4*this.width;
	}

function set_pixel(x, y, colour)
	{
		this.data[this.coord_to_index(x, y)] = colour;
		this.data[this.coord_to_index(x, y)+1] = colour;
		this.data[this.coord_to_index(x, y)+2] = colour;
		this.data[this.coord_to_index(x, y)+3] = 255;
	}

function get_pixel(x, y, colour)
	{
		return this.data[this.coord_to_index(x, y)];
	}


function crop_canvas(canvas, start_point){
		var image = canvas.getContext('2d').getImageData(0, 0, canvas.width, canvas.height);
		image.coord_to_index = coord_to_index;
		image.set_pixel = set_pixel;
		image.get_pixel = get_pixel;

		var char_pos = null;
		for (var interval = 25; interval > 1; interval = Math.floor(interval / 2))
			{
				for (var x = start_point[0]; x < start_point[0] + 50; x += interval)
					{
						for (var y = start_point[1]; y < start_point[1] + 50; y++)
							{
								if (image.get_pixel(x, y) == 255)
										char_pos = [x, y];
							}
					}
				if (char_pos != null)
						break;
			}
		
		for (var dir = -1; dir <= 2; dir += 2)
				for (var x = char_pos[0]; 0 <= x && x < image.width; x += dir)
					{
						var white_pixels = 0;
						for (var y = Math.max(0, char_pos[1]-50);
								 y < Math.min(image.height, char_pos[1]+50); y += 4)
								if (image.get_pixel(x, y) >= 128)
										white_pixels++;

						if (!white_pixels)
								for (var y = Math.max(0, char_pos[1]-50);
										 y < Math.min(image.height, char_pos[1]+50); y++)
										if (image.get_pixel(x, y) >= 128)
												white_pixels++;

						if (!white_pixels)
								if (dir == -1)
									{
										var im_left = x + 1;
										break;
									}
								else
									{
										var im_right = x - 1;
										break;
									}
					}

		for (var dir = -1; dir <= 2; dir += 2)
				for (var y = char_pos[1]; 0 <= y && y < image.height; y += dir)
					{
						var white_pixels = 0;
						for (var x = Math.max(0, char_pos[0]-50);
								 x < Math.min(image.width, char_pos[0]+50); x += 4)
								if (image.get_pixel(x, y) >= 128)
										white_pixels++;

						if (!white_pixels)
								for (var x = Math.max(0, char_pos[0]-50);
										 x < Math.min(image.width, char_pos[0]+50); x++)
										if (image.get_pixel(x, y) >= 128)
												white_pixels++;

						if (!white_pixels)
								if (dir == -1)
									{
										var im_top = y + 1;
										break;
									}
								else
									{
										var im_bottom = y - 1;
										break;
									}
					}

		width = im_right-im_left;
		height = im_bottom-im_top;

		var cropped_canvas = unsafeWindow.document.createElement('canvas');
		cropped_canvas.width = 100;
		cropped_canvas.height = 100;
		cropped_canvas.getContext('2d').drawImage(canvas, im_left, im_top, width, height,
																							0, 0, width, height);

		return cropped_canvas;		
	}


function decode(captcha)
	{
		var canvas = captcha.to_canvas();
		var image = canvas.getContext('2d').getImageData(0, 0, canvas.width, canvas.height);
		
		var blocks = get_blocks(image);
		var black_blocks = blocks[0];
		var white_blocks = blocks[1];

		var large_black = get_large_blocks(black_blocks, 4);
		var large_white = get_large_blocks(white_blocks, 3);

		var small_black = [];
		for (var i = 0; i < black_blocks.length; i++)
			{
				var in_large = false;
				for (var j = 0; j < large_black.length; j++)
						if (large_black[j].contains(black_blocks[i].items[0]))
								in_large = true;
				if (!in_large)
						small_black.push(black_blocks[i]);
			}

		var small_white = [];
		for (var i = 0; i < white_blocks.length; i++)
			{
				var in_large = false;
				for (var j = 0; j < large_white.length; j++)
						if (large_white[j].contains(white_blocks[i].items[0]))
								in_large = true;
				if (!in_large)
						small_white.push(white_blocks[i]);
			}
		
		try
			{
				chars = get_chars(image, large_black, large_white, small_black, small_white);
			}
		catch (e)
			{
				return false;
			}

		net = create_net();
		code = '';
		for (var i = 0; i < chars.length; i++)
			{
				receptors = check_receptors(chars[i]);
				code += guess_letter(net, receptors, i == 3);
			}

		return code;
	}


function get_blocks(image)
	{
		function sort_block(pixel1, pixel2)
			{
				return pixel1[0] - pixel2[0];
			}

		function min_size_count(blocks, min_size)
			{
				var count = 0;
				for (var i = 0; i < blocks.length; i++)
						if (blocks[i].items.length > min_size)
								count++;
				return count;
			}

		var canvas = unsafeWindow.document.createElement('canvas');
		canvas.width = image.width + 2;
		canvas.height = image.height + 2;
		canvas.getContext('2d').fillStyle = 'rgb(255,255,255)';
		canvas.getContext('2d').fillRect (0, 0, canvas.width, canvas.height);		
		canvas.getContext('2d').putImageData(image, 1, 1);

		image = canvas.getContext('2d').getImageData(0, 0, canvas.width, canvas.height);
		image.coord_to_index = coord_to_index;
		image.set_pixel = set_pixel;
		image.get_pixel = get_pixel;

		found = new Block();
		bg = get_colour_block(image, 0, 0);

		for (var i = 0; i < bg.items.length; i++)
				found.add(bg.items[i]);

		black_blocks = [];
		white_blocks = [];
		for (var x = 0; x < image.width; x++)
				for (var y = 0; y < image.height; y++)
					{
						var colour = image.get_pixel(x, y);
						var block = null;

						if (found.contains([x, y]))
								continue;

						if (colour == 0)
							{
								block = get_colour_block(image, x, y);
								block.items.sort(sort_block);
								if (block.items.length >= 5)
										black_blocks.push(block);
							}
						if (colour == 255)
							{
								block = get_colour_block(image, x, y);
								block.items.sort(sort_block);
								if (block.items.length >= 5)
										white_blocks.push(block);
							}

						if (block != null)
								for (var i = 0; i < block.items.length; i++)
										found.add(block.items[i]);
					}
		
		while (min_size_count(black_blocks, 10) < 4)
			{
				var wide = 0;
				for (var i = 0; i < black_blocks.length; i++)
						if (black_blocks[i].items.length > black_blocks[wide].items.length)
								wide = i;

				var blocks = split_block(black_blocks[wide]);
				black_blocks.splice(wide, 1);
				black_blocks.splice(wide, 0, blocks[1]);
				black_blocks.splice(wide, 0, blocks[0]);
			}

		return [black_blocks, white_blocks];
	}


function get_chars(image, large_black, large_white, small_black, small_white)
	{
		chars = [];

		for (var i = 0; i < large_black.length; i++)
			{
				var canvas = unsafeWindow.document.createElement('canvas');
				canvas.width = image.width;
				canvas.height = image.height;
				canvas.getContext('2d').fillRect(0, 0, canvas.width, canvas.height);

				var image = canvas.getContext('2d').getImageData(0, 0, canvas.width, canvas.height);
				image.coord_to_index = coord_to_index;
				image.set_pixel = set_pixel;
				image.get_pixel = get_pixel;
				
				for (var j = 0; j < large_black[i].items.length; j++)
						image.set_pixel(large_black[i].items[j][0], large_black[i].items[j][1], 255);

				for (var j = 0; j < small_white.length; j++)
					{
						if (i > 0)
								if (small_white[j].items[0][0] < 
										large_black[i-1].items[large_black[i-1].items.length-1][0] &&
										small_white[j].items[small_white[j].items.length-1][0] >
										large_black[i].items[0][0])

										for (var k = 0; k < small_white[j].items.length; k++)
												image.set_pixel(small_white[j].items[k][0],
																				small_white[j].items[k][1], 255);

						if (i < large_black.length - 1)
								if (small_white[j].items[0][0] < 
										large_black[i].items[large_black[i].items.length-1][0] &&
										small_white[j].items[small_white[j].items.length-1][0] > 
										large_black[i+1].items[0][0])

										for (var k = 0; k < small_white[j].items.length; k++)
												image.set_pixel(small_white[j].items[k][0],
																				small_white[j].items[k][1], 255);
					}

				for (var j = 0; j < small_black.length; j++)
					{
						if (small_black[j].items.length < 30)
								continue;

						var common_columns = 0;
						for (var k = 0; k < small_black[j].items.length; k++)
								if (large_black[i].items[0][0] <= small_black[j].items[k][0] &&
										small_black[j].items[k][0] <= 
										large_black[i].items[large_black[i].items.length-1][0])
										
										common_columns++;

						if (common_columns < 10)
								continue;

						if (i > 0)
							{
								var common_previous = 0;
								for (var k = 0; k < small_black[j].items.length; k++)
										if (large_black[i-1].items[0][0] <= small_black[j].items[k][0] &&
												small_black[j].items[k][0] <= 
												large_black[i-1].items[large_black[i-1].items.length-1][0])
												
												common_previous++;

								if (common_columns < common_previous)
										continue;
							}

						if (i < large_black.length - 1)
							{
								var common_next = 0;
								for (var k = 0; k < small_black[j].items.length; k++)
										if (large_black[i+1].items[0][0] <= small_black[j].items[k][0] &&
												small_black[j].items[k][0] <= 
												large_black[i+1].items[large_black[i+1].items.length-1][0])
												
												common_next++;

								if (common_columns < common_next)
										continue;
							}

						for (var k = 0; k < small_black[j].items.length; k++)
								image.set_pixel(small_black[j].items[k][0],
																small_black[j].items[k][1], 255);
					}

				/*if (i > 0)
						if (large_black[i].items[0][0] - 
								large_white[i-1].items[large_white[i-1].items.length-1][0] <= 5)

								for (var j = 0; j < large_white[i-1].items.length; j++)
										image.set_pixel(large_white[i-1].items[j][0],
																		large_white[i-1].items[j][1], 255);

				if (i < large_black.length - 1)
						if (large_white[i].items[0][0] - 
								large_black[i].items[large_black[i].items.length-1][0] <= 5)
								for (var j = 0; j < large_white[i].items.length; j++)
										image.set_pixel(large_white[i].items[j][0],
																		large_white[i].items[j][1], 255);*/

				
				canvas.getContext('2d').putImageData(image, 0, 0);

				var canvas2 = unsafeWindow.document.createElement('canvas');
				canvas2.width = 200;
				canvas2.height = 200;
				canvas2.getContext('2d').fillRect(0, 0, canvas2.width, canvas2.height);

				if (i % 2)
						canvas2.getContext('2d').rotate(0.08726646);
				else
						canvas2.getContext('2d').rotate(6.19591884);

				canvas2.getContext('2d').drawImage(canvas, 50, 75);

				var start_points = [[50, 65], [50, 70], [85, 55], [90, 80]];
				canvas = crop_canvas(canvas2, start_points[i]);

				image = canvas.getContext('2d').getImageData(0, 0, canvas.width, canvas.height);
				image.coord_to_index = coord_to_index;
				image.set_pixel = set_pixel;
				image.get_pixel = get_pixel;

				chars.push(image);
			}

		return chars;
	}


function get_colour_block(image, x, y)
	{
		function within(x, y)
			{
				return 0 <= x && x < image.width && 0 <= y && y < image.height;
			}

		var colour = image.get_pixel(x, y);
		var edge = [[x, y]];
		var block = new Block();
		block.add([x, y]);

		while (edge.length > 0)
			{
				var newedge = [];
				for (var i = 0; i < edge.length; i++)
					{
						var x = edge[i][0];
						var y = edge[i][1];
						var adjacent = [[x+1, y], [x-1, y], [x, y+1], [x, y-1]];
						for (var j = 0; j < adjacent.length; j++)
							{
								var s = adjacent[j][0];
								var t = adjacent[j][1];

								if (within(s, t) && !block.contains([s, t]) && image.get_pixel(s, t) == colour)
									{
										block.add([s, t]);
										newedge.push([s, t]);
									}
							}
					}
				edge = newedge;
			}
		return block;
	}


function get_large_blocks(blocks, count)
	{
		function sort_set(set1, set2)
			{
				return set1.items[0][0] - set2.items[0][0];
			}

		var large = [];
		for (var i = 0; i < blocks.length; i++)
			{
				if (large.length < count)
						large.push(blocks[i]);
				else
					{
						var greatest_diff = [0, null];
						for (var j = 0; j < large.length; j++)
							{
								var diff = blocks[i].items.length - large[j].items.length;
								if (diff > 0 && (diff > greatest_diff[0] || greatest_diff[1] == null))
										greatest_diff = [diff, j];
							}
					
						if (greatest_diff[1] != null)
								large[greatest_diff[1]] = blocks[i];
					}
			}

		return large.sort(sort_set);
	}

function split_block(block)
	{
		histogram = [];

		var start = block.items[0][0] + 5;
		var end = block.items[block.items.length-1][0] - 5;

		for (var i = 0; i < block.items.length; i++)
				if (start <= block.items[i][0] && block.items[i][0] <= end)
					{
						if (histogram[block.items[i][0]] == undefined)
								histogram[block.items[i][0]] = 0;

						histogram[block.items[i][0]]++;
					}

		var low = start;
		for (var i = 0; i < histogram.length; i++)
				if (histogram[i] != undefined && histogram[i] < histogram[low])
						low = i;

		left = new Block();
		right = new Block();

		for (var i = 0; i < block.items.length; i++)
			{
				if (block.items[i][0] <= low)
						left.add(block.items[i]);
				if (block.items[i][0] >= low)
						right.add(block.items[i]);
			}

		return [left, right];
	}

function load_image(data)
	{
		var gif = new GIF();
		gif.from_array(data_array(data));

		var form = document.getElementById('captchaform');
		var textbox = document.getElementById('captchafield');
								
		textbox.style.fontWeight = 'normal';
		textbox.style.fontSize = '7pt';
		textbox.value = 'working...';
				
		textbox.value = decode(gif);
				
		form.submit();
	}


function data_array(data)
	{
		var data_array = [];

		for (var i = 0; i < data.length; i++)
				data_array.push(data[i].charCodeAt(0) & 0xff);

		return data_array;
	}


function start()
	{
		if (document.getElementById('downloadlink') != null)
			{
				if (GM_getValue('autostart', false))
						var text = document.createTextNode('Toggle auto-start (currently on)');
				else
						var text = document.createTextNode('Toggle auto-start (currently off)');

				var link = document.createElement('a');
				link.appendChild(text);
				link.setAttribute('href', '#');
				link.style.color = '#000000';
				link.style.fontWeight = 'bold';
				link.style.textDecoration = 'none';

				link.addEventListener('click', toggle_autostart, false);

				var div = document.getElementsByTagName('div')[10];
				div.appendChild(document.createElement('br'));
				div.appendChild(link);

				if (GM_getValue('autostart', false))
						window.location.href = document.getElementById('downloadlink').firstChild.href;

			}
		
		else if (window.location.href.match('\\?d='))
			{
				var image = document.getElementById('captchaform').parentNode.getElementsByTagName('img')[0];

				GM_xmlhttpRequest({method: 'GET',
													url: image.src,
													overrideMimeType: 'text/plain; charset=x-user-defined',
													onload: function(response) { load_image(response.responseText); }
													});
			}
	}


function toggle_autostart(e)
	{
		if (GM_getValue('autostart', false))
			{
				GM_setValue('autostart', false);
				e.target.firstChild.nodeValue = 'Toggle auto-start (currently off)';
			}
		else
			{
				GM_setValue('autostart', true);
				e.target.firstChild.nodeValue = 'Toggle auto-start (currently on)';
				window.location.href = document.getElementById('downloadlink').firstChild.href;
			}
	}

start();
