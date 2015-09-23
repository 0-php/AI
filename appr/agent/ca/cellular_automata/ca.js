// ca.js - Wolfram-style cellular automata in HTML5 Canvas
//
// Copyright Andy Gimblett 2010
//
// http://www.cs.swan.ac.uk/~csandy/
// 
// Released under a BSD license: http://creativecommons.org/licenses/BSD/
//
// Changelog:
//    2010.05.14.1739 AMG v1.0
//                        Created.
//    2010.05.14.2058 AMG v2.0
//                        Factored the stuff actually doing the work
//                        out into separate modules.  Added control of
//                        rules, dimensions, and start state.
//    2010.05.14.2120 AMG v2.1
//                        Added drawEvery parameter, so we don't (have
//                        to) copy from buffer to canvas on every line
//                        - which was seriously slowing down large
//                        canvases.  Large canvases now render great!
//    2010.05.15.1019 AMG v2.2
//                        Starting drawing while it's already in
//                        progress now interrupts the in-progress
//                        operation.
//    2010.05.15.1028 AMG v2.3
//                        Added "choose rule at random" feature.
//    2010.05.17.1914 AMG v2.4
//                        Changes tracking CADrawing.js; added
//                        wrapping option and rudimentary save-to-png.
//    2010.05.19.1342 AMG v2.5
//                        Added querystring handling and permalink
//                        ability.
//    2010.05.20.1148 AMG v2.6
//                        Added "play chime" callback, called when
//                        drawing is finished if relevant option is
//                        ticked.

// Extract parameters from query string, with sensible handling of bad
// or empty values.
var getQsParms = function(qs, defaults){
  var qp = QueryString.qsParm;
  var ip = QueryString.intParm;
  return {
    "rule" : function(){ // Special case required for rule 0 as 0 is false.
      var parsed = parseInt(qp(qs, "rule"), 10);
      if(parsed === 0)
		return parsed;
      return Math.abs((parsed || defaults.rule) % 256);
    }(),
    "width" : Math.abs(ip(qs, "width") || defaults.width),
    "height" : Math.abs(ip(qs, "height") || defaults.height),
    "drawEvery" : Math.abs(ip(qs, "drawEvery") || defaults.drawEvery),
    "startState" : function(){
      var ss = qp(qs, "startState") || defaults.startState;
      if (ss in { "singleton-left" : '', "singleton-centre" : '',
                   "singleton-right" : '', "random" : '' }){
        return ss;
      }
      return defaults.startState;
    }(),
    "wrapEdges" : function(){
      if (QueryString.getQueryString() === ""){
        return defaults.wrapEdges;
      }
      var w = qp(qs, "wrapEdges");
      return (w === "on");
    }()
  };
};

// Get current parameters from form.
var getParms = function(){
  var f = $jq("#caControl");
  var caControlParms = {
    "rule" : parseInt(f.find("#rule")[0].value, 10),
    "width" : parseInt(f.find("#width")[0].value, 10),
    "height" : parseInt(f.find("#height")[0].value, 10),
    "drawEvery" : parseInt(f.find("#drawEvery")[0].value, 10),
    "startState" : f.find("#startState").val(),
    "wrapEdges" : f.find("#wrapEdges:checked").attr("checked")
  };
  return caControlParms;
};

var makeToggle = function(toggled, toggler, startHidden, onShow, onHide){
  if(startHidden)
    toggled.hide();
  toggler.click(function(){
    if (toggled.is(":visible")){
      if (onShow !== undefined){
        onShow();
      }
    } else {
      if (onHide !== undefined){
        onHide();
      }
    }
    toggled.toggle(400);
    return false;
  });
};

var setupExplanationToggle = function(){
  var ta = $jq("#toggleAbout");
  var taTxt = ta.text();
  makeToggle($jq("#about"), ta, true,
    function(){ ta.text(taTxt); },
    function(){ ta.text("(hide explanantion)"); });
};

var setupSidebarToggle = function(){
  var ts = $jq("#tglSidebar");
  makeToggle($jq("#sidebarHideable"), ts, false,
    function(){
      $jq("#tglSbImg").attr("src", "sidebarOpen.png");
    },
    function(){
      $jq("#tglSbImg").attr("src", "sidebarClose.png");
    }
 );
};

var readLocalStorage = function(){
  var pc = localStorage.getItem("playChime");
  pc = pc || "false";
  $jq("#playChime").attr("checked", pc === "true");
};

var writeLocalStorage = function(){
  var pc = $jq("#playChime").attr("checked");
  localStorage.setItem("playChime", pc.toString());
};

// initialize the form with parameters from query string, or defaults
// from HTML if no query string.
var init = function(){
  var parms = getQsParms(QueryString.getQueryString(), getParms());
  var f = $jq("#caControl");
  f.find("#rule").val(parms.rule);
  f.find("#width").val(parms.width);
  f.find("#height").val(parms.height);
  f.find("#drawEvery").val(parms.drawEvery);
  f.find("#startState").val(parms.startState);
  f.find("#wrapEdges").attr("checked", parms.wrapEdges);
  setupExplanationToggle();
  setupSidebarToggle();
  readLocalStorage();
};

var permalink = function(){
  var href = window.location.href;
  var qAt = href.indexOf("?");
  var root = (qAt == -1) ? href : href.substring(0, qAt);
  var seed = "";
  // if ($jq("#startState").val() == "random"){
  //   var ss = ElementaryCA.getSeedString();
  //   var sb = $jq.base64Encode(ss);
  //   seed = "&seed=" + sb;
  // }
  return root + "?" + $jq("#caControl").serialize() + seed;
};

var go = function(){
  var parms = getParms();
  var playChime = function(){
    if ($jq("#playChime").attr("checked")){
      $jq("#chime")[0].volume = 0.5;
      $jq("#chime")[0].play();
    }
  };
  CACanvas.initialize($jq("#ca"), $jq("#startStopBtn"), playChime, parms);
  CACanvas.reset();
  $jq("#permalink")[0].href = permalink();
  CACanvas.startDrawing();
};

var chooseRandomRule = function(){
    $jq('#rule').val(randInt(255));
    go();
};

var canvasToPng = function(){
  window.location = $jq("#ca")[0].toDataURL("image/png");
};

var togglePlayChime = function(){
  writeLocalStorage();
};
