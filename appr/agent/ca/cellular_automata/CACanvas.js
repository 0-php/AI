// CACanvas.js - draw an ElementaryCA to an HTML5 canvas.
//
// Copyright Andy Gimblett 2010
//
// http://www.cs.swan.ac.uk/~csandy/
// 
// Released under a BSD license: http://creativecommons.org/licenses/BSD/
//
// Changelog:
//    2010.05.14.2056 AMG v1.0
//                        Created first standalone version.
//    2010.05.14.2120 AMG v1.1
//                        Added drawEvery parameter, so we don't (have
//                        to) copy from buffer to canvas on every line
//                        - which was seriously slowing down large
//                        canvases.  Large canvases now render great!
//    2010.05.15.1347 AMG v2.0
//                        Renamed from DrawCA.js, and made into an
//                        object.  Speedup.
//    2010.05.17.1914 AMG v3.0
//                        Wrapped up in own namespace (rather than
//                        object); added ticks on canvas and
//                        generations/generations-per-second readout.
//                        Generally cleaned up.
//    2010.05.19.1648 AMG v3.1
//                        Added alteration between stop/restart on
//                        button.
//    2010.05.20.1120 AMG v3.2
//                        Added callback allowing action when finished
//                        drawing (e.g. play a sound).

var CACanvas = function() {

  /* Private */

  var parms;           // Control parameters.

  var gen;             // Which generation are we drawing?
  var width;           // Width of a single generation in cells.
  var maxGens;         // Maximum generations to render.

  var cv;              // Canvas HTML element.
  var ct;              // Canvas drawing context.
  var pix;             // ImageData object to draw into.
  var startStopBtn;    // Start/stop button.
  var finCallback;     // Callback function to call when finished drawing.

  var tickWidth=35;    // Length of generation tickmarks in pixels.

  var drawEvery;       // How many generations between canvas updates?
  var redrawInterval;  // Timer between generations.
  var gensPerSec="";   // How many generations per second?
  var startTime;       // When did we start (for gensPerSec).

  var writeGen;        // Closure for writing generation to canvas.
  var writeGps;        // Closure for writing gens per sec to canvas.

  // Compute and draw the next line.
  var nextGen = function() {
    gen++;
    drawLine(ElementaryCA.calcNext());
    if (gen >= maxGens) {
      finishedDrawing();
    }
  };

  // Draw a CA state (array of bits) on the specified canvas line.
  var drawLine = function(bits) {
    var rowOff = gen*4*width;
    var v;
    for (var i=0; i < width; i++) {
      v = (1-bits[i]) * 255;
      pix.data[rowOff + i*4    ] = v;   // Red
      pix.data[rowOff + i*4 + 1] = v;   // Green
      pix.data[rowOff + i*4 + 2] = v;   // Blue
      pix.data[rowOff + i*4 + 3] = 255; // Alpha
    }
    maybeUpdate();
  };

  // Perhaps update the canvas and/or info.
  var maybeUpdate = function() {
    var ms, cs;
    // Only actually copy to canvas if we've drawn enough.
    if (gen % drawEvery === 0 || gen >= maxGens-1) {
      ct.putImageData(pix, 0, 0);
    }
    if (drawEvery < 10 || gen % 10 === 0 || gen >= maxGens-1) {
      writeGen(gen);
      if (gen % 100 === 0 || gen >= maxGens-1) {
        ms = new Date() - startTime;
        writeGps(ms > 0 ? Math.round(gen*1000/ms) : "");
      }
    }
  };

  // Wipe the canvas, obvs.
  var wipeCanvas = function() {
    cv.width = width + tickWidth + 75;
    cv.height = maxGens + 12;
    ct.save();
    ct.fillStyle = "#fff";
    ct.fillRect(0, 0, cv.width, cv.height);
    ct.restore();
  };

  // Draw ticks.
  var initTicks = function() {
    var gap = 2;
    var txtGap = 10;
    var txtWidth = 20;
    ct.strokeStyle = "#ddd";
    ct.fillStyle = "#999";
    ct.font = "9px Arial";
    ct.textAlign = "right";   
    ct.textBaseline = "top";
    for (var y = 0; y <= maxGens; y += 100) {
      ct.moveTo(width+gap, y+0.5);
      ct.lineTo(width+gap+tickWidth, y+0.5);
      ct.stroke();
      ct.fillText(Math.floor(y), width+gap+tickWidth-2, y+2);
    }
    ct.font = "10px Arial";
    ct.textAlign = "right";
    ct.fillText("gen:", width+gap+tickWidth+txtGap+txtWidth, 0);
    ct.fillText("g/s:", width+gap+tickWidth+txtGap+txtWidth, 12);
    ct.textAlign = "left";
    var x = width+gap+tickWidth+txtGap+txtWidth+gap;
    // Create closure to write generation number to canvas.
    writeGen = function(n) {
      ct.fillStyle = "#fff";
      ct.fillRect(x, 0, cv.width, 10);
      ct.fillStyle = "#999";
      ct.fillText(n, x, 0);
    };
    writeGps = function(n) {
      ct.fillStyle = "#fff";
      ct.fillRect(x, 12, cv.width, 12);
      ct.fillStyle = "#999";
      ct.fillText(n, x, 12);
    };
  };

  var stopDrawing = function() {
    redrawInterval = clearInterval(redrawInterval);
    ct.restore();
    startStopBtn.text("Restart");
    startStopBtn.unbind();
    startStopBtn.click(draw);
  };

  var startDrawing = function() {
    startStopBtn.text("Stop");
    startStopBtn.unbind();
    startStopBtn.click(stopDrawing);
    ct.save();
    initTicks();
    var startNow = new Date();
    startTime = startNow.getTime();
    drawLine(ElementaryCA.mostRecent());
    redrawInterval = setInterval(nextGen, 0);
  };

  // initialize with specified parameters.
  var initialize = function(canvas, startStop, finCb, cparms) {
    cv = canvas[0];
    startStopBtn = startStop;
    finCallback = finCb;
    parms = cparms;
    ct = cv.getContext("2d");
  };

  // Reset the canvas.
  var reset = function() {
    stopDrawing();
    gen=0;
    width = parms.width;
    maxGens = parms.height;
    wipeCanvas();
    ElementaryCA.initialize(width, parms.rule, parms.startState, parms.wrapEdges);
    pix = ct.createImageData(width, maxGens);
    drawEvery = parms.drawEvery;
  };

  // Clear the canvas and start drawing.
  var draw = function() {
    reset();
    startDrawing();
  };

  var finishedDrawing = function() {
    stopDrawing();
    finCallback();
  };

  /* Public */

  return {
    initialize : initialize,
    reset : reset,
    startDrawing : startDrawing,
    draw : draw
  };

}();