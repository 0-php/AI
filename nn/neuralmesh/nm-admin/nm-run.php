<?php
require("lib/controller.class.php");
$app = new Controller;
$app->inc("nmesh");
$data = $app->model->get("network")->get($_GET['n']);
$nn = $app->model->get("network")->nn;
set_time_limit(0);

function buildGraph($input,$width=25) {

   // get percentage
   $perc = ($input+1) / 2; //add one to shift out of negatives

   $bar1 = '<font color="#303030">|</font>';
   $bar2 = '<font color="#e0e0e0">|</font>';

   $bars =  ceil($perc * $width);

   $output  = str_repeat($bar1,$bars);
   $output .= str_repeat($bar2,($width-$bars));

   return $output;
}

if($_POST) {
	$data = $app->model->get("cache")->getCache($_GET['n'].implode("|",$_POST['input']));
	
	if($data === null) {
		if(count($_POST['input']) != $nn->inputs) throw new Exception("Incorrect number of entries!");
		$outputs = $nn->run($_POST['input']);
		//save into cache
		$app->model->get("cache")->saveCache($_GET['n'].implode("|",$_POST['input']),$_GET['n'],implode("|",$outputs));
	} else {
		$outputs = explode("|",$data); //get from cache
	}
	
	$return = "<table>";
	$maxout = max($outputs);
	$count = 1;	
	foreach($outputs as $output) {
		$return .= "<tr><td>Output $count: <strong>".$output."</strong></td>";
		$return .= "<td>".buildGraph($output).($output == $maxout ? "&laquo;" : "")."</td></tr>";
		$count++;
	}
	$return .= "</table>";
}

$app->display("header");
?>

<div id="tools">
<fieldset>
<legend>Note:</legend>
These values range from -1 to 1 rather than binary so instead of working with 0 to 1, 
you need to use -1. The bar graph is a visual representation of the range with the lowest 
being closer to -1, center being 0 and highest being closer to 1.
</fieldset>
</div>

<form action="nm-run.php?n=<?php echo $_GET['n']; ?>" method="post">
Input: <br/>
<?php
if(!isset($_POST['input'])) {
	for($i=0;$i<$nn->inputs;$i++)
		echo '<input type="text" name="input[]" value="-1" size="1" />';
} else {
	foreach($_POST['input'] as $input)
		echo '<input type="text" name="input[]" value="'.$input.'" size="1" />';
}
?>
<input type="submit" value="Run" />
</form>

<?php
if(isset($return) && strlen($return)) echo "<br>".$return;
$app->display("footer");
?>