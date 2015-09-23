<?php
/**
* Bayes
*
* Calculates posterior probabilities for m hypotheses and n evidence alternatives. The code was inspired by a procedural TrueBasic version
* (Bayes.tru) bundled with Grimstead and Snell's excellent online textbook "Introduction to Probability".
*/
class Bayes {

  /**
  * Number of evidence and hypothesis alternatives (that is, number of rows and columns).
  */
  var $m = 0;
  var $n = 0;
  /**
  * Output labels for evidence alternatives.
  */
  var $row_labels = array();

  /**
  * Output labels for hypothesis alternatives.
  */
  var $column_labels = array();

  /**
  * Vector container for prior probabilities.
  */
  var $priors = array();

  /**
  * Matrix container for likelihood of evidence e given hypothesis h.
  */
  var $likelihoods = array();

  /**
  * Matrix container for posterior probabilties.
  */
  var $posterior = array();

  /**
  * Vector container for evidence probabilties.
  */
  var $evidence = array();

  /**
  * Initialize the Bayes algorithm by setting the priors, likelihoods
  * and dimensions of the likelihood and posterior matrices.
  */
  function Bayes($priors, $likelihoods){
    $this->priors = $priors;
    $this->likelihoods = $likelihoods;
    $this->m = count($priors);
    $this->n = count($likelihoods[0]);
    return true;
  }

  /**
  * Output method for setting row labels prior to display.
  */
  function setRowLabels($row_labels) {
    $this->row_labels = $row_labels;
    return true;
  }

  /**
  * Output method for setting column labels prior to display.
  */
  function setColumnLabels($column_labels) {
    $this->column_labels = $column_labels;
    return true;
  }

  function getConditionalProbability($A, $B, $Data) {
		$NumAB   = 0;
		$NumB    = 0;
		$NumData = count($Data);
		for ($i=0; $i < $NumData; $i++) {
		  if (in_array($B, $Data[$i])) {
		    $NumB++;
		    if (in_array($A, $Data[$i])) $NumAB++;
		  }
		}
		return $NumAB / $NumB;
	}

  /**
  * Compute the posterior probability matrix given the priors and
  * likelihoods.
  *
  * The first set of loops computes the denominator of the canonical
  * Bayes equation. The probability appearing in the denominator
  * serves a normalizing role in the computation - it ensures that
  * posterior probabilities sum to 1.
  *
  * The second set of loops:
  *
  *   1. multiplies the prior[$h] by the likelihood[$h][$e]
  *   2. divides the result by the denominator
  *   3. assigns the result to the posterior[$e][$h] probability matrix
  */
  function getPosterior() {
    // Find probability of evidence e
    for($e=0; $e < $this->n; $e++) {
      for ($h=0; $h < $this->m; $h++) {
        $this->evidence[$e] += $this->priors[$h] * $this->likelihoods[$h][$e];
      }
    }
    // Find probability of hypothesis given evidence
    for($e=0; $e < $this->n; $e++) {
      for ($h=0; $h < $this->m; $h++) {
        $this->posterior[$e][$h] = $this->priors[$h] * $this->likelihoods[$h][$e] / $this->evidence[$e];
      }
    }
    return true;
  }

  function getPosterior_my(){
    foreach($this->likelihoods as $key=>$value){
	 foreach($this->likelihoods[$key] as $key2=>$value2){
	  //if(!isset($this->priors[$key])) $this->priors[$key]=1;
	  $this->evidence[$key] += $this->priors[$key] * $this->likelihoods[$key][$key2];
	 }
	}
    // Find probability of hypothesis given evidence
    foreach($this->likelihoods as $key=>$value){
	 foreach($this->likelihoods[$key] as $key2=>$value2){
	  //if(!isset($this->evidence[$key])) $this->evidence[$key]=1;
	  $this->posterior[$key][$key2] = $this->priors[$key] * $this->likelihoods[$key][$key2] / $this->evidence[$key];
	 }
	}
    return true;
  }

  function getPosterior_my_stable(){
    foreach($this->likelihoods as $key=>$value){
	 foreach($this->likelihoods[$key] as $key2=>$value2){
	  if($this->priors[$key2] == 0) $this->priors[$key2]=1;
	  $this->evidence[$key] += $this->priors[$key2] * $this->likelihoods[$key][$key2];
	 }
	}
    // Find probability of hypothesis given evidence
    foreach($this->likelihoods as $key=>$value){
	 foreach($this->likelihoods[$key] as $key2=>$value2){
	  if($this->priors[$key2] == 0) $this->priors[$key2]=1;
	  $this->posterior[$key][$key2] = $this->priors[$key2] * $this->likelihoods[$key][$key2] / $this->evidence[$key];
	 }
	}
    return true;
  }

  // Output method for displaying posterior probability matrix
  function toHTML($number_format="%01.3f"){
   echo "<table border='1' cellpadding='5' cellspacing='0'>
     <tr><td> </td>";
   foreach($this->likelihoods as $key=>$value){
    echo "<td align='center'><b>".$this->column_labels[$key]."</b></td>";
   }
   echo "</tr>";
   foreach($this->likelihoods as $key=>$value){
    echo "<tr><td><b>".$this->row_labels[$key]."</b></td>";
    foreach($this->likelihoods[$key] as $key2=>$value2){
     echo "<td align='right'>"; printf($number_format, $this->posterior[$key][$key2]); echo "</td>";
    }
    echo "</tr>";
   }
   echo "</table>Posterior: <pre>"; print_r($this->posterior); echo "</pre>Evidence: <pre>"; print_r($this->evidence); echo "</pre>";
   /*
      for($e=0; $e < $this->n; $e++) {
        ?>
        <tr>
          <td><b><?php echo $this->row_labels[$e] ?></b></td>
          <?php
          for ($h=0; $h < $this->m; $h++) {
            ?>
            <td align='right'>
               <?php printf($number_format, $this->posterior[$e][$h]) ?>
            </td>
            <?php
          }
          ?>
        </tr>
        <?php
      }
      ?>
    </table>
    <?php*/
  }
}
?>