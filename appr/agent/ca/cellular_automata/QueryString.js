// QueryString.js - query string handling (for cellular automata tool).
//
// Copyright Andy Gimblett 2010
//
// http://www.cs.swan.ac.uk/~csandy/
// 
// Released under a BSD license: http://creativecommons.org/licenses/BSD/
//
// Changelog:
//    2010.05.19.1255 AMG v1.0
//                        Created.

var QueryString = function() {

  /* Private */

  // Seek a parameter in a querystring.  Adapted from
  // http://blog.pothoven.net/2006/07/get-request-parameters-through.html
  var qsParm = function(qs, parm) {
    var begin, end;
    if (qs.length <= 0) {
      return null;
    }
    // Add "=" to the parameter name (i.e. parm=value)
    parm = parm + "=";
    // Find the beginning of the string
    begin = qs.indexOf(parm);
    if (begin == -1) {
      return null;
    }
    // Multiple parameters are separated by the "&" sign
    end = qs.indexOf ("&", begin);
    if (end == -1) {
      end = qs.length;
     }
    return unescape(qs.substring(begin + parm.length, end));
  };

  /* Public */

  return {

    getQueryString :
    function() {
      return window.location.search.substring(1);
    },

    intParm :
    function(qs, parm) {
      return parseInt(qsParm(qs, parm), 10);
    },

    qsParm : qsParm

  };

}();
