		// Settings
		var x = 150; // How many cells on the X axis
		var y = 150; // How many cells on the Y axis
		var pixels_per_value = 4; // How many pixels wide should be a cell
		var delay_between_frames = 100;// In ms


		// Calculate the canvas width and height
		var canvas_x = x * pixels_per_value;
		var canvas_y = y * pixels_per_value;

		// Create our matrix
		var matrix = new Array(y);
		var matrix_tmp = new Array(y);
		for(i = 0; i < y; i++){
			matrix[i] = new Array(x);
			matrix_tmp[i] = new Array(x);
		}

		// Fill with some random points
		var random = 1600;
		var m,n;
		for(i = 0; i < random; i++){
			m = Math.floor(Math.random() * y);
			n = Math.floor(Math.random() * x);
			matrix[m][n] = 1;
		}

		// Get the canvas reference and the 2D context
		var canvas = document.getElementById('drawBoard');
		// Set canvas size
		canvas.width = canvas_x;
		canvas.height = canvas_y;
		var context = canvas.getContext('2d');

		// Draw the first cell generation
		draw_matrix();

		// Call for the next generation of the cell colony
		setTimeout("process_life()", delay_between_frames);

		function process_life(){
			var neighbours = 0;
			matrix_tmp = matrix.clone();
			for(i = 0; i < y; i++){
				for(j = 0; j < x; j++){
					neighbours = count_neighbours(i,j);
					// Underpopulation
					if(neighbours < 2)
						matrix_tmp[i][j] = 0;
					// Overcrowding
					if(neighbours > 3)
						matrix_tmp[i][j] = 0;
					// Birth
					if(neighbours == 3)
						matrix_tmp[i][j] = 1;
				}
			}
			matrix = matrix_tmp.clone();
			draw_matrix();
			setTimeout("process_life()", delay_between_frames);
		}

		// Added so we can copy the array between steps
		Array.prototype.clone = function(){
			var tmp = new Array(); 
			for(var property in this)
				tmp[property] = typeof(this[property]) == 'object'  ? this[property].clone() : this[property];
			return tmp;
		}


		function count_neighbours(i, j){
			var count = 0;
			// Check for its maximum 8 neighbours
			if(matrix[i][j-1] == 1) count++;
			if(matrix[i][j+1] == 1) count++;
			//
			if(matrix[i-1] != undefined){
				if(matrix[i-1][j] == 1) count++;
				if(matrix[i-1][j-1] == 1) count++;
				if(matrix[i-1][j+1] == 1) count++;
			}
			if(matrix[i+1] != undefined){
				if(matrix[i+1][j] == 1) count++;
				if(matrix[i+1][j-1] == 1) count++;
				if(matrix[i+1][j+1] == 1) count++;
			}
			return count;
		}

		function draw_matrix(){
			for(i = 0; i < y; i++){
				for(j = 0; j < x; j++)
					draw_matrix_point(j, i, matrix[i][j]);
			}
		}


		function draw_matrix_point(i, j, status){
			var origin_x = i * pixels_per_value;
			var origin_y = j * pixels_per_value;
			if(status == 1)
				context.fillStyle = '#88FF88';
			else
				context.fillStyle = '#000000';
			draw_rectangle(context, origin_x, origin_y, pixels_per_value, pixels_per_value);
		}

		// Draw a rectangle based on x/y origin point and having a specified width and height
		function draw_rectangle(context, origin_x, origin_y, width, height){
			context.beginPath();
			context.rect(origin_x, origin_y, width, height);
			context.closePath();
			context.fill();
		}

		// Clear a canvas with a specified color
		function clear_2d(canvas){
			canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
		}