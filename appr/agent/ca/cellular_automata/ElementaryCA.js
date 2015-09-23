// ElementaryCA.js - elementary cellular automata in JavaScript.
//
// Copyright Andy Gimblett 2010
//
// http://www.cs.swan.ac.uk/~csandy/
// 
// Released under a BSD license: http://creativecommons.org/licenses/BSD/
//
// Changelog:
//    2010.05.14.2050 AMG v1.0
//                        Created first standalone version.
//    2010.05.17.1914 AMG v2.0
//                        Wrapped up in own namespace; added
//                        singleton-left and singleton-right options.
//    2010.05.19.1715 AMG v2.1
//                        Fixed buffer handling (for startup), and
//                        generally tidied.

// Generate a random integer in the range 0..n
function randInt(n) {
  return Math.round(Math.random()*n);
}

var ElementaryCA = function() {

  /* Private */

  var w;           // Width of CA.
  var seed;        // Seed.
  var buffers;     // Buffers for calculation.
  var nextBuffer;  // Number of next buffer (alternates between 0 and 1).
  var rule;        // Rule number to generate for.
  var ancestors;   // Ancestors function to use.

  // Compute rule array, given a rule number.
  var buildRule = function(rno) {
    var bs = rno.toString(2);
    var rule = [];
    for (var i=bs.length-1; i>=0; i--) {
      rule.push(parseInt(bs[i],2));
    }
    for (i=rule.length; i<8; i++) {
      rule.push(0); // Padding to full 8 bits.
    }
    return rule;
  };

  // Given a source step (in s), calculate the next step (into d).
  var doCalc = function(s, d) {
    for (var i=0; i < w; i++) {
      var ans = ancestors(s, i);
      d[i] = rule[ans];
    }
  };

  // Return the last-computed step of the CA
  var getMostRecent = function() {
    return buffers[1-nextBuffer];
  };

  // Empty array for seed.
  var emptyArray = function(w) {
    var arr = [];
    for (var i=0; i < w; i++) {
      arr[i] = 0;
    }
    return arr;
  };

  // Seed with a single 1, on the left.
  var seedSingle1Left = function(w) {
      var arr = emptyArray(w);
      arr[0] = 1;
      return arr;
  };

  // Seed with a single 1, in the centre.
  var seedSingle1Middle = function(w) {
      var arr = emptyArray(w);
      arr[Math.round(w/2)] = 1;
      return arr;
  };

  // Seed with a single 1, on the right.
  var seedSingle1Right = function(w) {
      var arr = emptyArray(w);
      arr[w-1] = 1;
      return arr;
  };

  // Seed with random bits.
  var seedRandom = function(w) {
      var arr = [];
      for (var i=0; i < w; i++) {
        arr[i] = randInt(1);
      }
      return arr;
  };

  // Mapping from start name to start-generation function.
  var seedFunctions = {
    "singleton-left"    : seedSingle1Left,
    "singleton-centred" : seedSingle1Middle,
    "singleton-right"   : seedSingle1Right,
    "random"            : seedRandom
  };

  // Compute the three ancestors for element i, with wraparound.
  var ancestorsWrap = function(s, i) {
    var x = i>0 ? s[i-1] : s[w-1];
    var y = s[i];
    var z = i<w-1 ? s[i+1] : s[0];
    return x*4 + y*2 + z;
  };

  // Compute the three ancestors for element i, without wraparound.
  var ancestorsNoWrap = function(s, i) {
    var x = i>0 ? s[i-1] : 0;
    var y = s[i];
    var z = i<w-1 ? s[i+1] : 0;
    return x*4 + y*2 + z;
  };

  /* Public */

  return {

  initialize :
  function (width, ruleNo, seedName, wrap){
    w = width;
    seed = (seedFunctions[seedName])(w);
    // We use two buffers, alternating between them.
    buffers = [seed, []];
    nextBuffer = 1;
    rule = buildRule(ruleNo);
    ancestors = wrap ? ancestorsWrap : ancestorsNoWrap;
  },

  // Return the most recently computed step of the CA
  mostRecent : getMostRecent,

  // Calculcate the next step of the CA.
  calcNext :
  function() {
    // Calculate, from appropriate source to appropriate destination.
    doCalc(buffers[1-nextBuffer], buffers[nextBuffer]);
    // Swap src/dest buffers for next time.
    nextBuffer = 1 - nextBuffer;
    // Return data for the step just calculated.
    return getMostRecent();
  },

  // Return seed as a string; convert each chunk of bw (e.g. 8) bits into a character, and concatenate all the characters.
  getSeedString :
  function() {
    var bw = 8;           // bit width
    var ss = "";          // seed string to return
    var padded = [];      // workspace (padded to a multiple of bw bits)
    var sb=0;             // one chunk of seed bits.
    var sl, pl;           // lengths of seed and padded copy, in bits.
    var i;
    if (!seed) {
      return "";
    }
    sl = seed.length;
    // Copy original to workspace.
    for (i=0; i < sl; i++) {
      padded[i] = seed[i];
    }
    // Pad workspace to a multiple of bw bits.
    pl = sl % bw === 0 ? 0 : bw * (Math.floor(sl / bw) + 1) - sl;
    for (i=sl; i < sl + pl; i++) {
      padded[i] = 0;
    }
    // Accumulate characters.
    for (i=0; i < padded.length; i++) {
      sb = (sb << 1) | padded[i];
      if ((i+1) % bw === 0) {
        ss += String.fromCharCode(sb);
        sb = 0;
      }
    }
    return ss;
  },

  getSeed :
  function() {
    return seed;
  }

  };

}();
