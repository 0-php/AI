<?php
//Actually, it's EM with Adding Components

class RBF_Network {
	var $x; //X [m,n] object-feature matrix, where objects are rows and features are columns
	var $n; //Space dimention
	var $m; //Set dimention
	var $y //Y [m,1] vector of the corresponded object class lables
	var $pp; //PP = [R, mo, eps, Maxk] where
	var $r; //R - permissible dispersion of likelyhood
	var $m0; //m0 - minimal size of set, that determines one the Gaussian distribution
	var $eps; //eps - accuracy parameter (that is passed to EMk)
	var $maxk; //Maxk - maximal number of components in mixture
	//
	//Output arguments
	//W - row of mixture components weights
	//M - (k,n) matrix, j-string of M is mean of j-component in mixture
	//Sigma - (kn,n) blok matrix consisting of k (n,n) matrixes where j-blok is covariance matrixes of j-component in mixture
	//k - number of mixture components 
	//Ym - values of dependent variable in the centres of components
	function RBF_Network($x, $y, $pp){
		$this->x = $x;
		$this->n = count($x['objects']);
		$this->m = count($x['features']);
		$this->y = $y;
		$this->pp = $pp;
		$this->r = $pp[0];
		$this->m0 = $pp[1];
		$this->eps = $pp[2];
		$this->maxk = $pp[3];
		//approximation
		//only 1 component in mixture
		$k = 1;
		$w = 1
		$m = mean($x);
		//make covariance matrix
		$sigma = ($X - ones($m, 1) * $M)'*(X-ones(m,1)*M)/m; 
	}

}

?>