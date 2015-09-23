<?php
class cache {
	/**
	 * Get a cached item
	 * @param $id The hashkey to lookup
	 * @return cached data
	 */
	function getCache($id) {
		$q = db::init()->query("cache.get",array("id"=>$id));
		if($q->rowCount()) {
			$data = $q->fetch(PDO::FETCH_NUM);
			return $data[0];
		}
		return null;
	}
	
	/**
	 * Save a data item in the cache
	 * @param $hash The hashed key
	 * @param $id The networkID
	 * @param $data The data to cache
	 */
	function saveCache($hash,$id,$data) {
		db::init()->query("cache.save",array("id"=>$hash,"network"=>$id,"data"=>$data));
	}
	
	/**
	 * Update the cache with the new outputs
	 * @param $id ID of the network
	 * @param $data Training data
	 */
	function updateCache($id,$data,$nn) {
		foreach($data as $set) {
			$inputs = explode("|",trim($set['pattern']));
			$outputs = $nn->run($inputs);
			db::init()->query("cache.save",array("id"=>$id.$set['pattern'],"network"=>$id,"data"=>implode("|",$outputs)));
		}
	}
	
	/**
	 * Clear the cache and unmanaged networks
	 */
	function clearCache() {
		db::init()->query("cache.clear",array("n"=>CACHE_LIFE));
		db::init()->query("networks.clear",array("n"=>UNMANAGED_LIFE));
	}
	
	/**
	 * Truncates the cache table
	 */
	function clearAll() {
		db::init()->query("cache.clearAll");
	}
	
	/**
	 * Quickly caches one pattern and output from the nmesh cache
	 * @param $id Network ID
	 * @param $input Input string
	 * @param $data Output array
	 */
	function quickCache($id,$input,$data) {
		db::init()->query("cache.save",array("id"=>$id.implode("|",trim($input)),"network"=>$id,"data"=>implode("|",$data)));
	}
}