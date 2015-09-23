// First try to implement Hierarchical Temporary Memory (06.01.2012)

htm = {

	inputs: [],
	data: [],
	time: 0,
	regions: [], //Cortex layers analogue
	regions_count: 6,
	columns: [], //List of all columns
	columns2d: [], //List of all columns in 2d array
	columns_count: 100,
	iterations_in_past: 1000, //When computing something average in time

	//Spatial pooler
	input: [], //The input to this level at time t. input[t][j] is 1 if the j'th input is on
	overlap: [], //The spatial pooler overlap of column c with a particular input pattern
	activeColumns: [],  //List of column indices that are winners due to bottom-up input (output of spatial pooler)
	overlapColumns: [], //List of columns overlaps with inputs in time (before inhibition)
	desiredLocalActivity: 10, //A parameter controlling the number of columns that will be winners after the inhibition step
	inhibitionRadius: 5, //Average connected receptive field size of the columns
	neighbors: [], //A list of all the columns that are within inhibitionRadius of column c
	minOverlap: 10, // A minimum number of inputs that must be active for a column to be considered during the inhibition step
	boost: 0, // The boost value for column c as computed during learning - used to increase the overlap value for inactive column
	synapse: [], //A data structure representing a synapse - contains a permanence value and the source input index.
	connectedPerm: 0.8, //If the permanence value for a synapse is greater than this value, it is said to be connected
	potentialSynapses: [], //The list of potential synapses and their permanence values
	connectedSynapses: [], //A subset of potentialSynapses(c) where the permanence value is greater than connectedPerm. These are the bottom-up inputs that are currently connected to column c
	permanenceInc: 0.05, //Amount permanence values of synapses are incremented when activity-based learning occurs
	permanenceDec: 0.05, //Amount permanence values of synapses are decremented when activity-based learning occurs
	activeDutyCycle: [], //A sliding average representing how often column c has been active after inhibition (e.g. over the last 1000 iterations)
	overlapDutyCycle: [], //A sliding average representing how often column c has had significant overlap (i.e. greater than minOverlap) with its inputs (e.g. over the last 1000 iterations)
	minDutyCycle: 0, //A variable representing the minimum desired firing rate for a cell. If a cell's firing rate falls below this value, it will be boosted.  This value is calculated as 1% of the maximum firing rate of its neighbors

	//Temporal pooler
	cell: [], //2-dim array (column, cell) with list of all cells
	cellsPerColumn: 4, //Number of cells in each column
	activeState: [], //3-dim array (column c, cell i, time t), one number per cell. Represents the active state of the column cell at time given the current feed-forward input and the past temporal context. activeState[c][i][t] is the contribution from column cell at time. If 1, the cell has current feed-forward input as well as an appropriate temporal context
	predictiveState: [], //3-dim array (column c, cell i, time t), one number per cell. Represents the prediction of the column cell at time, given the bottom-up activity of other columns and the past temporal context. predictiveState[c][i][t] is the contribution of column cell at time. If 1, the cell is predicting feed-forward input in the current temporal context
	learnState: [], //A boolean indicating whether cell i in column c is chosen as the cell to learn on
	activationThreshold: 10, //Activation threshold for a segment. If the number of active connected synapses in a segment is greater than activationThreshold, the segment is said to be active.
	learningRadius: 100, //The area around a temporal pooler cell from which it can get lateral connections
	initialPerm: 0.5, //Initial permanence value for a synapse
	minThreshold: 0.3, //Minimum segment activity for learning
	newSynapseCount: 100, //The maximum number of synapses added to a segment during learning (to not mantain large list)
	//Data structure holding three pieces of information required to update a given segment:
	//a) segment index (-1 if it's a new segment)
	//b) a list of existing active synapses
	//c) a flag indicating whether this segment should be marked as a sequence segment (defaults to false)
	segmentUpdate: [],
	segmentUpdateList: [], //	//A list of segmentUpdate structures. segmentUpdateList[c][i] is the list of changes for cell i in column c. Temporary changes in cell's synapses, waiting to evaluate if cell predictive

	run: function(data){
		for(i=0; i<this.regions_count; i++)
			this.regions[this.regions.length] = {};
		for(region in this.regions){
			region_height = Math.ceil(Math.sqrt(columns_count));
			for(i=0; i<region_height; i++){
				columns2d[i] = [];
				columns_in_row = Math.min(columns_count - (i * region_height), region_height);
				for(j=0; j<columns_in_row; j++){
					column = { 'x': i, 'y': j, 'overlap': 0, 'boost': 0 };
					this.columns[this.columns.length] = column;
					this.columns2d[i][j] = column;
				}
			}
			for(i=0; i<this.columns_count; i++)
				this.columns[this.columns.length] = { 'x': 0, 'overlap': 0, 'boost': 0 }
			this.spatialPooler();
			this.temporalPooler();
			this.time++;
		}
	},

	spatialPooler: function(){
		t = this.time;
		//Phase 1: Overlap
		for(c in this.columns){
			column = this.columns[c];
			column.overlap = 0;
			for(s in this.connectedSynapses[c])
				column.overlap = column.overlap + input[t][this.connectedSynapses[c][s].sourceInput];
			if(column.overlap < this.minOverlap)
				column.overlap = 0;
			else
				column.overlap = column.overlap * column.boost;
			overlapColumns[t][c] = column.overlap;
		}
		//Phase 2: Inhibition
		for(c in this.columns){
			column = this.columns[c];
			minLocalActivity = kthScore(column.neighbors, this.desiredLocalActivity);
			if(column.overlap > 0 && column.overlap >= this.minLocalActivity)
				this.activeColumns[t][c] = column;
		}
		//Phase 3: Learning
		for(c in this.activeColumns[t]){
			for(s in potentialSynapses[c]){
				synapse = potentialSynapses[c][s];
				if(synapse.active){
					synapse.permanence += permanenceInc;
					synapse.permanence = min(1.0, synapse.permanence)
				} else {
					synapse.permanence -= permanenceDec;
					synapse.permanence = max(0.0, synapse.permanence);
				}
			}
		}
		for(c in this.columns){
			column = this.columns[c];
			this.minDutyCycle[c] = 0.01 * this.maxDutyCycle(column.neighbors);
			this.activeDutyCycle[c] = this.updateActiveDutyCycle(c);
			column.boost = boostFunction(this.activeDutyCycle[c], this.minDutyCycle[c]);

			this.overlapDutyCycle[c] = this.updateOverlapDutyCycle(c);
			if(this.overlapDutyCycle[c] < this.minDutyCycle[c])
				this.increasePermanences(c, 0.1 * this.connectedPerm);
		}
	},

	temporal_pooler: function(){
		//Phase 1: Compute the active state, activeState[t], for each cell
		for(c in this.activeColumns(t)){
			buPredicted = false;
			for(i=0; i<this.cellsPerColumn-1; i++){
				if(this.predictiveState[c][i][t - 1]){
					s = this.getActiveSegment(c, i, t - 1, activeState);
					if(s.sequenceSegment){
						buPredicted = true;
						this.activeState[c][i][t] = 1;
					}
				}
			}
			if(!buPredicted){
				for(i=0; i<this.cellsPerColumn-1; i++)
					this.activeState[c][i][t] = 1;
			}
		}
		//Phase 2: Compute the predicted state, predictiveState[t], for each cell
		for(c, i in cells)
			for(s in segments(c, i))
				if(this.segmentActive(c, i, s, t))
					this.predictiveState[c][i][t] = 1;
		//Phase 3: Update synapses
		for(c, i in cells){
			if(this.earnState(s, i, t) == 1) //Cell predicted it's future state
				this.adaptSegments(this.segmentUpdateList[c][i], true);
			else if(this.predictiveState[c][i][t] == 0 && this.predictiveState[c][i][t - 1] == 1)
				this.adaptSegments(this.segmentUpdateList[c][i], false);
			delete this.segmentUpdateList[c][i]; //Delete not removes element from array, just marks as undefined
		}
	},

	//Given the list of columns, return the k'th highest overlap value
	kthScore: function(cols, k){
		allNeighbors = cols.sort( //Descending sorting overlap in columns
			function(a, b){
				return b.overlap - a.overlap;
			}
		);
		index = min(k - 1, allNeighbors.length - 1);
		return allNeighbors[index];
	},

	//Computes a moving average of how often column c has been active after inhibition.
	updateActiveDutyCycle: function(c){
		iterations = this.iterations_in_past;
		total_activations = 0;
		entries_count = this.activeColumns.length - 1;
		if(entries_count < iterations)
			iterations == entries_count;
		for(i=entries_count; i>entries_count - iterations; i--){
			if(this.activeColumns[i][c])
				total_activations++;
		}
		this.activeDutyCycle[c] = total_activations / iterations;
	},

	//Computes a moving average of how often column c has significant overlap with it's inputs (before inhibition)
	updateOverlapDutyCycle: function(c){
		iterations = this.iterations_in_past;
		total_overlaps = 0;
		entries_count = this.overlapColumns.length - 1;
		if(entries_count < iterations)
			iterations == entries_count;
		for(i=entries_count; i>entries_count - iterations; i--){
			if(this.overlapColumns[i][c])
				total_overlaps++;
		}
		this.overlapDutyCycle[c] = total_overlaps / iterations;
	},

	//The radius of the average connected receptive field size of all the columns.
	//The connected receptive field size of a column includes only the connected synapses (those with permanence values >= connectedPerm).
	//This is used to determine the extent of lateral inhibition between columns.
	averageReceptiveFieldSize: function(){

	},

	//Returns the maximum active duty cycle of the columns in the given list of columns
	maxDutyCycle: function(cols){

	},

	//Increase the permanence value of every synapse in column c by a scale factor s.
	increasePermanences: function(c, s){

	},

	//Returns the boost value of a column. The boost value is a scalar >= 1. If activeDutyCyle(c) is above minDutyCycle(c),
	//the boost value is 1. The boost increases linearly once the column's activeDutyCyle starts falling below its minDutyCycle.
	boostFunction: function(c){

	},

	//This routine returns true if the number of connected synapses on segment s that are active due to the given state at time t is greater than activationThreshold. The parameter state can be activeState, or learnState
	segmentActive: function(s, t, state){

	},

	//For the given column c cell i, return a segment index such that segmentActive[s][t][state] is true. If multiple segments are active, sequence segments are given preference. Otherwise, segments with most activity are given preference
	getActiveSegment: function(c, i, t, state){

	},

	//For the given column c cell i at time t, find the segment with the largest number of active synapses. This routine is aggressive in finding the best match. The permanence value of synapses is allowed to be below connectedPerm. The number of active synapses is allowed to be below activationThreshold, but must be above minThreshold. The routine returns the segment index. If no segments are found, then an index of -1 is returned.
	getBestMatchingSegment: function(c, i, t){

	},

	//For the given column, return the cell with the best matching segment (as defined above). If no cell has a matching segment, then return the cell with the fewest number of segments.
	getBestMatchingCell: function(c){

	},

	//Return a segmentUpdate data structure containing a list of proposed changes to segment s. Let activeSynapses be the list of active synapses where the originating cells have their activeState output = 1 at time step t. (This list is empty if s = -1 since the segment doesn't exist.) newSynapses is an optional argument that defaults to false. If newSynapses is true, then newSynapseCount - count(activeSynapses) synapses are added to activeSynapses. These synapses are randomly chosen from the set of cells that have learnState output = 1 at time step t.
	getSegmentActiveSynapses: function(c, i, t, s, newSynapses/*= false*/){

	},

	//This function iterates through a list of segmentUpdate's and reinforces each segment. For each segmentUpdate element, the following changes are performed. If positiveReinforcement is true then synapses on the active list get their permanence counts incremented by permanenceInc. All other synapses get their permanence counts decremented by permanenceDec. If positiveReinforcement is false, then synapses on the active list get their permanence counts decremented by permanenceDec. After this step, any synapses in segmentUpdate that do yet exist get added with a permanence count of initialPerm
	adaptSegments: function(segmentList, positiveReinforcement){

	}

}