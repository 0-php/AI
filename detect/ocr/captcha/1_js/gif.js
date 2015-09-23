
function GIF(){
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

GIF.prototype.from_array = function(data){
    this.width = data[6] + data[7] * 0x100;
    this.height = data[8] + data[9] * 0x100;

    var gct_size = Math.pow(2, (data[10] & 7) + 1);

    for(var i = 0; i < gct_size; i++)
        this.gct.push([data[13 + i * 3], data[13 + i * 3 + 1], data[13 + i * 3 + 2]]);

    this.min_code_size = data[127];
    var code_size = this.min_code_size + 1;

    this.init_code_dict();

    this.current_byte = 128;
    var code = null;
    var previous = null;
    var next_code = Math.pow(2, this.min_code_size) + 2;
    while(1){
        if(!this.bytes){
            var byte_ = data[this.current_byte];
            if(byte_ == 0)
                break;
            this.bytes = byte_;
            this.current_byte++;
          } else {
            code = this.get_code(data, code_size);
            if(code < this.code_dict.length){
                if(this.code_dict[code] == 'clear'){
                    this.init_code_dict();
                    code_size = this.min_code_size + 1;
                    next_code =  Math.pow(2, this.min_code_size) + 2;
                    previous = null;
                    continue;
                }
                if(this.code_dict[code] == 'end')
                    break;
                if(previous == null){
                    this.pixels = this.pixels.concat(this.code_dict[code]);
                    previous = code;
                    continue;
                  }
              }

            if(code < this.code_dict.length){
            	console.info('Trying to get '+code+' from '+this.code_dict);
                this.code_dict[next_code] = this.code_dict[previous].concat(this.code_dict[code][0]);
            } else {
            	console.info('Trying to get '+code+' from '+this.code_dict);
                this.code_dict[next_code] = this.code_dict[previous].concat(this.code_dict[previous][0]);
            }

            this.pixels = this.pixels.concat(this.code_dict[code]);

            previous = code; console.info(next_code);
            next_code++;
            if(next_code >= Math.pow(2, code_size) && code_size < 12)
                code_size++;
          }
      }
  }

GIF.prototype.get_code = function(data, code_size){
	//console.info('Getting code of size '+code_size+' from '+data);
    if(this.remaining[0] >= code_size){
        var mask = Math.pow(2, code_size) - 1;
        var code = this.remaining[1] & mask;
        this.remaining[0] -= code_size;
        this.remaining[1] >>= code_size;
    } else { console.info(this.remaining);
        var read_bits = 0;
        var code = 0;
        while (read_bits < code_size){
            var byte_ = data[this.current_byte];
            this.current_byte++;
            this.bytes--;

            var read_in = Math.min(8, code_size - read_bits);
            var read_new = Math.max(0, read_in - this.remaining[0]);
            var new_mask = Math.pow(2, read_new) - 1;
            var rem_mask = Math.pow(2, read_in - read_new) - 1;

            code |= (((byte_ & new_mask) << this.remaining[0]) | (this.remaining[1] & rem_mask)) << read_bits;

            this.remaining = [this.remaining[0] - (read_in - read_new), this.remaining[1] >> (read_in - read_new)];

            this.remaining = [this.remaining[0] + (8 - read_new), this.remaining[1] | (byte_ >> read_new << this.remaining[0])];

            read_bits += read_in;
        }
    }
	console.info(code);
    return code;
}

GIF.prototype.get_pixel = function(x, y){
    if(0 <= x && x < this.width && 0 <= y && y < this.height) //If in bound
        return this.gct[this.pixels[x + y * this.width]];
    else
        return null;
}

GIF.prototype.init_code_dict = function(){
    var code_dict = [];

    for(var i = 0; i < this.gct.length; i++)
        code_dict[i] = [i];

    code_dict[Math.pow(2, this.min_code_size)] = 'clear';
    code_dict[Math.pow(2, this.min_code_size) + 1] = 'end';

    this.code_dict = code_dict;
}

GIF.prototype.to_canvas = function(){
    var canvas = document.createElement('canvas');
    canvas.width = this.width;
    canvas.height = this.height;

    var image = canvas.getContext('2d').getImageData(0, 0, canvas.width, canvas.height);
    image.coord_to_index = coord_to_index;
    image.set_pixel = set_pixel;
    image.get_pixel = get_pixel;

    for(var x = 0; x < this.width; x++)
        for(var y = 0; y < this.height; y++)
            image.set_pixel(x, y, this.get_pixel(x, y)[0]);

    canvas.getContext('2d').putImageData(image, 0, 0);

    return canvas;
}