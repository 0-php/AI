function Block(){
	this.keys = [];
	this.items = [];
}

Block.prototype.add = function(value){
	var key = value.toSource();
	var pos = this.search(key);
	if(this.keys[pos] != key){
		this.keys.splice(pos, 0, key);
		this.items.splice(pos, 0, value);
    }
}

Block.prototype.contains = function(value){
	var key = value.toSource();
	var pos = this.search(key);
	return this.keys[pos] === key;
}

Block.prototype.search = function(key){
	var low = 0;
	var high = this.keys.length;
	while(low < high){
		mid = low + Math.floor((high - low) / 2);
		if(this.keys[mid] < key)
			low = mid + 1;
		else
			high = mid;
	}
	return low;
}