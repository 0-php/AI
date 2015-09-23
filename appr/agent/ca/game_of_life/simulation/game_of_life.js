//colors
var arrCol = new Array("#000066", "#99ff99");

function createCells(arr, m, n, widthCell, border){
	var i, j, k, val, col;
	if("" + widthCell == "undefined")
		widthCell = 10;
	if("" + border == "undefined")
		border = 1;
	document.writeln("<table style='color:#ffff00; table-layout:fixed;' cellpadding='0' cellspacing='0' border='" + border + "'>");

	k = 0;
	for(i = 0; i < m; i++){
		// document.writeln("<tr style='height:" + widthCell + "px'>");
		document.writeln( "<tr height='" + widthCell + "'>" );
		for(j = 0; j < n; j++){
			val = arr[ k++ ];
			col = (val ? arrCol[1] : arrCol[0]);

			document.writeln("<td width='" + widthCell + " height='" + widthCell + "' id='c" + i + "_" + j + "'" + " style='background-color:" + col + ";'><img src='point.png' width='0' height='0'></td>");
		}
		document.writeln( "</tr>" );
	}

	document.writeln( "</table>" );
}
function Init(bUpdate){
	var i;
	for(i = 0; i < sz; i++)
		arr[ i ] = 0;

	for(i = 0; i < nInit; i++)
		arr[Math.floor(sz * Math.random())] = 1;

	if(bUpdate)
		updateCells();
}
//add one more alive cell
function addLive(){
	var k = Math.floor(sz * Math.random());
	var i = Math.floor(k / n);	//VI
	var j = k % n;
	var s = "c" + i + "_" + j;
	var obj = document.getElementById(s);
	if(obj){
		obj.style.backgroundColor = arrCol[1];
		arr[k] = 1;
	}
	return false;
}
//update
function updateCells(){
	var k;
	for(k = 0; k < sz; k++){
		if(arrObj[k]){
			arrObj[k].style.backgroundColor = arr[k] ? arrCol[1] : arrCol[0];
			if(iDebug)
				arrObj[k].innerText = arrNeighb[k];
		}
	}
	return false;
}
//new iteration
function Iterate(){
	var i;
	//calc new array
	for(i = 0; i < sz; i++)
		arrNeighb[i] = 0;

	for(i = 0; i < sz; i++){	//check all 8 neighboors
		if(i % n == 0){	//left border
		} else {	//not left
			if(arr[i - 1] == 1)
				arrNeighb[i]++;
			if(i - 1 - n >= 0 && arr[i - 1 - n] == 1)
				arrNeighb[ i ]++;
			if(i - 1 + n < sz && arr[i - 1 + n] == 1)
				arrNeighb[ i ]++;
		}
		if(i % n == n - 1){	//right border
		} else {	//not right
			if(arr[ i + 1 ] == 1)
				arrNeighb[ i ]++;
			if(i + 1 - n >= 0 && arr[i + 1 - n] == 1)
				arrNeighb[ i ]++;
			if(i + 1 + n < sz && arr[i + 1 + n] == 1)
				arrNeighb[ i ]++;
		}
		//top
		if(i - n >= 0 && arr[i - n] == 1)
			arrNeighb[i]++;
		//bottom
		if(i + n >= 0 && arr[i + n] == 1)
			arrNeighb[i]++;
	}

	//generate
	for(i = 0; i < sz; i++){
		if(arr[i] == 0 && arrNeighb[i] == 3){	//new alive
			arr[i] = 1;
		} else if(arr[i] == 1 && (arrNeighb[i] == 2 || arrNeighb[i] == 3)){	//keep alive
		} else {	//die
			arr[ i ] = 0;
		}
	}

	//update table
	updateCells();
	return false;
}
function Start(){
	cycle = 1;
	Next();
	return false;
}
function Next(){
	if(cycle == 1){
		Iterate();
		window.setTimeout("Next()", 50);
	}
	return false;
}
function Stop(){
	cycle = 0;
	return false;
}

var i, j, k, s;
var m = 40;	//rows
var n = 50;	//columns
var widthCell = 6;	//cell width
var border = 0;		//border width
var nInit = 500;	//initially alive
var sz = m * n;		//table size
var arr = new Array( sz );
var arrNeighb = new Array( sz );	//number of neighboors
var arrObj = new Array( sz );		//reference by ID
var cycle = 0;	//loop
var iDebug = 0;

Init(false);

createCells(arr, m, n, widthCell, border);

//assign objects
i = 0;
j = 0;
for(k = 0; k < sz; k++){
	s = "c" + i + "_" + j;
	j++;
	if(j == n){	//next row
		j = 0;
		i = i + 1;
	}
	obj = document.getElementById(s);
	arrObj[ k ] = obj;
}