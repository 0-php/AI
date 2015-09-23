/**
 * Cellular automation functions
 */

var Board; //array of arrays
var animating = true; //is it currently animating?
var generation = 0; //Which generation are we in
var Alives; //array of currently alive cells
var AlmostDeads; //array of almostdead cells
var NewlyDeads; //array of cells that died this generation

var xsize = 25; //x value grid size
var ysize = 25; //y value grid size

/**
 * Cell states
 */
var dead = 0; 
var alive = 1;
var almostdead = 2;


/**
 * Added so that we have a clean copy of the grid when in between generations
 */
Array.prototype.clone = function () {
	var a = new Array(); 
	for (var property in this) 
		{
			a[property] = typeof (this[property]) == 'object'  ? this[property].clone() : this[property];
		} 
	return a;
}


/**
 * Added in case the user makes the grid extremely small to where the board set up is out of bounds
 */
function withinRange(x, y){

	if(x >= 0 && y >= 0 && x < xsize && y < ysize){
		return true;
	}
	return false;
}

/**
 * Is the node that we're checking the node we're on?
 */
function checkYourself(x, y, ax, ay){
	if (x == ax && y == ay){
		return true;
	}
	return false;
}

/**
 * Count the neighbors
 */
function Neighbors(Board, x, y)
{
	var n = 0;
	for(var dx=-1;dx <= 1; ++dx)
		for(var dy=-1;dy <= 1; ++dy)
		{
			var ax = x+dx;
    		var ay = y+dy;
    		if(ax < 0) ax = xsize - 1;
    		if(ay < 0) ay = ysize - 1;
    		if(ay == ysize) ay = 0;
    		if(ax == xsize) ax = 0;
    		if (withinRange(ax, ay) ){
        		if(!checkYourself(x,y,ax,ay)){
	    			if(Board[ax][ay]==alive) n++;
        		}
    		}
		}
	return n;
}



/**
 * Step through the board and decide who lives and who dies
 */
function NextStep(Board)
{
	//Make clones of our data and reset them for updates
	var tempBoard = Board.clone();
	var tempAlives = Alives.clone();
	var tempAlmostDeads = AlmostDeads.clone();
	var checkedAlready = [];
	AlmostDeads = [];
	NewlyDeads = [];

	//	Turn all the almost deads into deads
	for (var i = 0; i < tempAlmostDeads.length; i++){
		var coors = tempAlmostDeads[i].split("_");
		var x = parseInt(coors[0]);
		var y = parseInt(coors[1]);
		if(Board[x][y] == almostdead)
		{
			
			 Kill(tempBoard,x,y); //automatically kill the nearly dead ones
			 NewlyDeads.push(x + "_" + y);  
		}
	}


	//	Only check the neighbors of the alives
	for (var i = 0; i < Alives.length; i++){
		var coors = Alives[i].split("_");
		var x = parseInt(coors[0]);
		var y = parseInt(coors[1]);

		//grabbing neighbors
		for(var dx=-1;dx <= 1; ++dx){
			for(var dy=-1;dy <= 1; ++dy)
			{
				var ax = x+dx;
	    		var ay = y+dy;
	    		if(ax < 0) ax = xsize - 1;
	    		if(ay < 0) ay = ysize - 1;
	    		if(ay == ysize) ay = 0;
	    		if(ax == xsize) ax = 0;
	    		
	    		//If you already checked this cell once this generation, no reason to check it again.
	    		if(!alreadyCheckedThisGeneration(checkedAlready, ax, ay)){
		    		checkedAlready.push(ax + "_" + ay);
	       			n = Neighbors(Board,ax, ay);
	      
	       			if(n == 3) MakeLive(tempBoard, tempAlives, ax, ay);
	       			if((n < 2)||(n > 3)) NotQuiteDead(tempBoard, tempAlives,ax,ay);
	    		}
			}
		}
	}
	
	window.Alives = tempAlives;
	window.Board = tempBoard; 

	generation++; //display what generation we're on
	document.getElementById("generation").innerHTML = generation;
	UpdateBoard();
}


/**
 * Has this cell been checked during this generation?
 */
function alreadyCheckedThisGeneration(checkedAlready, x, y){
	var index = checkedAlready.indexOf(x+"_"+y);
	if(index != -1){
		return true;
	}
	return false;
}
/**
 * changes from state 2 (almostdead) to state 0 (dead)
 */
function Kill(tempBoard,x,y)
{
	if(withinRange(x, y)){
		if(tempBoard[x][y] == almostdead){
			tempBoard[x][y] = dead;
			AlmostDeads = RemoveFromAlives(AlmostDeads, x, y);
		}
	}
}

/**
 * Removes an element from array.  I mistakenly called this function Alives
 */
function RemoveFromAlives(tAlives, x,y){
	var index = tAlives.indexOf(x+"_"+y);
	tAlives.splice(index, 1);
	return tAlives;
}

/**
 * changes from state 1 (alive) to state 2 (almostdead)
 */
function NotQuiteDead(tempBoard, tempAlives, x, y){
	if(withinRange(x, y)){
		if(tempBoard[x][y] == alive){
			tempBoard[x][y] = almostdead;
			AlmostDeads.push(x + "_" + y);
			
			tempAlives = RemoveFromAlives(tempAlives, x, y);
			
		}
	}
	
}

/**
 * changes to state 1 (alive)
 */
function MakeLive(tempBoard,tempAlives,x,y)
{

	if(withinRange(x, y)){
		if(tempBoard[x][y] == dead || tempBoard[x][y] == almostdead){
			tempBoard[x][y] = alive;
			tempAlives.push(x+"_"+y);
		}
	}
}

/**
 * Update only the cells that were altered recently
 * NewlyDeads, AlmostDeads, and Alives are the arrays that have recently been updated
 */
function UpdateBoard(){

	for (var i = 0; i < NewlyDeads.length; i++){
		var coors = NewlyDeads[i].split("_");
		var el = document.getElementById("Cell" + coors[0] + "_" + coors[1]);
		el.style.backgroundColor = "gray";
		el.style.Color = "gray";
	}
	for(var i =0; i < AlmostDeads.length; i++){
		var coors = AlmostDeads[i].split("_");
		var el = document.getElementById("Cell" + coors[0] + "_" + coors[1]);
		el.style.backgroundColor = "blue";
		el.style.Color = "blue";
	}
	for(var i =0; i < Alives.length; i++){
		var coors = Alives[i].split("_");
		var el = document.getElementById("Cell" + coors[0] + "_" + coors[1]);
		el.style.backgroundColor = "green";
		el.style.Color = "green";
	}
}

/**
 * displays formatted html/css for a visual representation
 */
function DrawBoard(Board)
{
	var Text = "";
	for(var y = 0; y < ysize; ++y)
	{
		for(var x = 0; x < xsize; ++x){
			switch(Board[x][y]){
			case alive:
				Text += "<span id='Cell" + x + "_" + y + "' class='alive' onclick='toggleCell(" + x + "," + y + ");'></span>";
				break;
			case dead:
				Text += "<span id='Cell" + x + "_" + y + "' class='dead' onclick='toggleCell(" + x + "," + y + ");'></span>";
				break;
			case almostdead:
				Text += "<span id='Cell" + x + "_" + y + "' class='almostdead' onclick='toggleCell(" + x + "," + y + ");'></span>";
				break;
			}
		}		

		Text += "<br/>";
	}
	document.getElementById("board").innerHTML = Text;
}

/**
 * When the user clicks on a new cell
 */
function toggleCell(x, y){
	if (Board[x][y] == alive){
		Board[x][y] = dead;
		var el = document.getElementById("Cell" + x + "_" + y);
		el.style.backgroundColor = "gray";
		el.style.Color = "gray";
		Alives = RemoveFromAlives(Alives,x,y);
	} else {
		Board[x][y] = alive;
		var el = document.getElementById("Cell" + x + "_" + y);
		el.style.backgroundColor = "green";
		el.style.Color = "green";
		Alives.push(x+"_"+y);
	}
}

/**
 * Steps through without user input
 */
function Animate(Board){
	if(window.animating){
		setTimeout("Animate(Board)", 100);
		NextStep(Board);
	}
}

function stopAnimation(){
	window.animating = false;
}
function startAnimation(){
	window.animating = true;
}

function isInteger (s)
{
   var i;

   if (isEmpty(s))
   if (isInteger.arguments.length == 1) return 0;
   else return (isInteger.arguments[1] == true);

   for (i = 0; i < s.length; i++)
   {
      var c = s.charAt(i);

      if (!isDigit(c)) return false;
   }

   return true;
}

function isEmpty(s)
{
   return ((s == null) || (s.length == 0));
}

function isDigit (c)
{
   return ((c >= "0") && (c <= "9"));
}

/**
 * Called when the user changes the grid size
 */
function newXY(){
	var newX = document.getElementById("xsize");
	var newY = document.getElementById("ysize");
	if (isInteger(newX.value) && isInteger(newY.value)){
		stopAnimation();
		xsize = newX.value;
		ysize = newY.value;
		document.getElementById("boardType").value = "blinker";
		Main();
	} else {
		alert("The value entered is not a valid Integer.");
	}
}

/**
 * Called when the user changes the board setup type
 */
function setupNewBoard(value){
	for(var y = 0; y < ysize; ++y)
	{
		for(var x = 0; x < xsize; ++x){
			Board[x][y] = 0;
		}
	}
	Alives = [];
	if(value == "blinker")
	{
	    Board[1][0] = 1;
	    Board[1][1] = 1;
	    Board[1][2] = 1;
	    Alives.push("1_0", "1_1", "1_2");
    }
    else if(value == "glider")
    {
	    Board[2][0] = 1;
	    Board[2][1] = 1;
	    Board[2][2] = 1;
	    Board[1][2] = 1;
	    Board[0][1] = 1;
	    Alives.push("2_0", "2_1", "2_2", "1_2", "0_1");
    }
    else if(value == "flower")
    {
        Board[4][6] = 1;
        Board[5][6] = 1;
        Board[6][6] = 1;
        Board[7][6] = 1;
        Board[8][6] = 1;
        Board[9][6] = 1;
        Board[10][6] = 1;
        Board[4][7] = 1;
        Board[6][7] = 1;
        Board[8][7] = 1;
        Board[10][7] = 1;
        Board[4][8] = 1;
        Board[5][8] = 1;
        Board[6][8] = 1;
        Board[7][8] = 1;
        Board[8][8] = 1;
        Board[9][8] = 1;
        Board[10][8] = 1;
        Alives.push("4_6", "5_6", "6_6", "7_6", "8_6", "9_6", "10_6", "4_7", "6_7", "8_7", "10_7", "4_8", "5_8", "6_8", "7_8", "8_8", "9_8", "10_8");
    }
	DrawBoard(Board);
	generation = 0;
}

/**
 * Onload.  Sets up our board 
 */
function Main()
{
    // *** Change this variable to choose a different baord setup from below
    var BoardSetup = "blinker";
    
	Board = new Array(xsize);
	for(var x = 0; x < xsize; x++)
	{
		Board[x] = new Array(ysize);
		for(var y = 0; y < ysize; y++)
			Board[x][y] = 0;
	}
	Alives = new Array();
	AlmostDeads = new Array();
	NewlyDeads = new Array();
	
	if(BoardSetup == "blinker")
	{
	    Board[1][0] = 1;
	    Board[1][1] = 1;
	    Board[1][2] = 1;
	    Alives.push("1_0", "1_1", "1_2");
    }
    else if(BoardSetup == "glider")
    {
	    Board[2][0] = 1;
	    Board[2][1] = 1;
	    Board[2][2] = 1;
	    Board[1][2] = 1;
	    Board[0][1] = 1;
	    Alives.push("2_0", "2_1", "2_2", "1_2", "0_1");
    }
    else if(BoardSetup == "flower")
    {
        Board[4][6] = 1;
        Board[5][6] = 1;
        Board[6][6] = 1;
        Board[7][6] = 1;
        Board[8][6] = 1;
        Board[9][6] = 1;
        Board[10][6] = 1;
        Board[4][7] = 1;
        Board[6][7] = 1;
        Board[8][7] = 1;
        Board[10][7] = 1;
        Board[4][8] = 1;
        Board[5][8] = 1;
        Board[6][8] = 1;
        Board[7][8] = 1;
        Board[8][8] = 1;
        Board[9][8] = 1;
        Board[10][8] = 1;
        Alives.push("4_6", "5_6", "6_6", "7_6", "8_6", "9_6", "10_6", "4_7", "6_7", "8_7", "10_7", "4_8", "5_8", "6_8", "7_8", "8_8", "9_8", "10_8");
    }
    generation = 0;
    
	DrawBoard(Board);

}