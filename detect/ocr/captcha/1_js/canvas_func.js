
function crop_canvas(canvas, start_point){
    var image = canvas.getContext('2d').getImageData(0, 0, canvas.width, canvas.height);
    image.coord_to_index = coord_to_index;
    image.set_pixel = set_pixel;
    image.get_pixel = get_pixel;

    var char_pos = null;
    for(var interval = 25; interval > 1; interval = Math.floor(interval / 2)){
        for(var x = start_point[0]; x < start_point[0] + 50; x += interval){
            for(var y = start_point[1]; y < start_point[1] + 50; y++){
                if(image.get_pixel(x, y) == 255)
                    char_pos = [x, y];
            }
        }
        if(char_pos != null)
            break;
    }

    for(var dir = -1; dir <= 2; dir += 2)
        for(var x = char_pos[0]; 0 <= x && x < image.width; x += dir){
            var white_pixels = 0;
            for(var y = Math.max(0, char_pos[1] - 50); y < Math.min(image.height, char_pos[1] + 50); y += 4)
                if(image.get_pixel(x, y) >= 128)
                    white_pixels++;

            if(!white_pixels)
                for(var y = Math.max(0, char_pos[1] - 50); y < Math.min(image.height, char_pos[1] + 50); y++)
                    if(image.get_pixel(x, y) >= 128)
                        white_pixels++;

            if(!white_pixels){
                if(dir == -1){
                    var im_left = x + 1;
                    break;
                } else {
                    var im_right = x - 1;
                    break;
                }
			}
        }

    for(var dir = -1; dir <= 2; dir += 2)
        for(var y = char_pos[1]; 0 <= y && y < image.height; y += dir){
            var white_pixels = 0;
            for(var x = Math.max(0, char_pos[0] - 50); x < Math.min(image.width, char_pos[0] + 50); x += 4)
                if(image.get_pixel(x, y) >= 128)
                    white_pixels++;

            if(!white_pixels)
                for(var x = Math.max(0, char_pos[0] - 50); x < Math.min(image.width, char_pos[0] + 50); x++)
                    if(image.get_pixel(x, y) >= 128)
                        white_pixels++;

            if(!white_pixels)
                if(dir == -1){
                    var im_top = y + 1;
                    break;
                } else {
                    var im_bottom = y - 1;
                    break;
                }
        }

    width = im_right - im_left;
    height = im_bottom - im_top;

    var cropped_canvas = document.createElement('canvas');
    cropped_canvas.width = 100;
    cropped_canvas.height = 100;
    cropped_canvas.getContext('2d').drawImage(canvas, im_left, im_top, width, height, 0, 0, width, height);

    return cropped_canvas;
}