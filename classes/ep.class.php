<?php
class individual {
    var $fitness_value;
    var $gene_value;
    var $birth_date;

    function set($g_val,$f_val,$d_val){
        $this->fitness_value  = $f_val;
        $this->gene_value     = $g_val;
        $this->birth_date     = $d_val;
    }
}

class EP {
    var $population;            //initial population
    var $totalGeneration;
    var $fitnessFunction;        //string
    var $lowerBound;
    var $upperBound;
    var $stepSize;

    var $arr_individual;        //array
    var $arr_offspring;            //array
    var $date;                    //counter for today date
    var $currentGeneration;
    var $file_name;

    function EP(){
        $this->date              = 1;
        $this->currentGeneration = 0;
    }

    function getRandNumber($min, $max){
        if($min > $max){
            $temp = $min;
            $min = $max;
            $max = $temp;
        }
        return $min + lcg_value() * (abs($max-$min));
    }

    function fitness($x){
        $f_str = $this->fitnessFunction ;
        $result = 0;
        $f_str = split(',', $f_str);
        $size_fitness = count($f_str);

        for($i=0, $j=$size_fitness-1; $i<$size_fitness; $i++, $j--)
			$result += $f_str[$i] * (pow($x, $j));

        return $result;
    }

    function buildFirstGeneration(){
        $indiv = new individual();
        for($i=1; $i<=$this->population; $i++){
            $g_value = $this->getRandNumber($this->lowerBound, $this->upperBound);
            while($g_value<=$this->lowerBound || $g_value>=$this->upperBound)
				$g_value = $this->getRandNumber($this->lowerBound, $this->upperBound);
            $f_value = $this->fitness($g_value);
            $indiv->set($g_value, $f_value, $this->date);
            $this->arr_individual[$i] = $indiv;
            $this->date++;
        }
        $this->currentGeneration++;
    }

    function buildChilds(){
        $indiv = new individual();
        for($i=1; $i<=$this->population; $i++){
            $g_value = $this->mutate($this->arr_individual[$i]->gene_value);
            $f_value = $this->fitness($g_value);
            $indiv->set($g_value, $f_value, $this->date);
            $this->arr_offspring[$i] = $indiv;
            $this->date++;
        }
        $this->currentGeneration++;
    }

    function selectionPool(){
        $arr_pool = array_merge($this->arr_individual, $this->arr_offspring);
        array_multisort($arr_pool, 0);
        for($i=1, $j=(count($arr_pool)-1); $i<=10; $i++, $j--)
            $this->arr_individual[$i] = $arr_pool[$j];
    }

    function mutate($val){
        $sigma = $this->stepSize / sqrt(2 / pi());
        $val += $this->getRandNumber((-1 * $sigma), $sigma);
        return $val;
    }

    function debug(){
        if(file_exists($this->file_name))
            unlink($this->file_name);
        $this->showHeader();
        $this->buildFirstGeneration();
        $this->showThisGeneration($this->arr_individual);
        $this->writeFile($this->arr_individual);

        for($i=($this->currentGeneration+1); $i<=($this->totalGeneration); $i++){
            $this->buildChilds();
            $this->selectionPool();
            $this->showThisGeneration($this->arr_individual);
            $this->writeFile($this->arr_individual);
        }

    }

    function showThisGeneration($arr_val){
        $sumFitness = 0;
        for($i=1; $i<=$this->population; $i++)
            $sumFitness += $arr_val[$i]->fitness_value;

        $averageFitness = $sumFitness / $this->population;

        print "<p>\n";
        print "Population data after ".($this->population * $this->currentGeneration)." births (generation ".$this->currentGeneration.")<br />";
        print "Local fitness : max = ".$arr_val[1]->fitness_value." , ave = ".$averageFitness." , min = ".$arr_val[$this->population]->fitness_value."<br />";
        print "<table width='35%' style='font-family:Tahoma;font-size:12px;'>\n";
        print "<tr><td><b>Indiv</b></td><td><b>birthdate</b></td><td><b>fitness</b></td><td><b>gene value</b></td></tr>\n";
        for($i=1; $i<=$this->population; $i++){
            print "<tr>";
            print "<td>".$i."</td>";
            print "<td>".$arr_val[$i]->birth_date."</td>";
            print "<td>".$arr_val[$i]->fitness_value."</td>";
            print "<td>".$arr_val[$i]->gene_value."</td>";
            print "</tr>\n";
        }
        print "</table>\n";
        print "</p>\n";
    }

    function showHeader(){
        print("<b>EP</b><br>");
        print("<b>Simulation time limit (# births): ".$this->totalGeneration*$this->population."</b><br />");
        print("<b>Using a parabolic landscape defined on 1 parameter(s) with</b><br />");
        print("&nbsp;&nbsp;&nbsp;&nbsp;<b>parameter initialization bound of :</b><br />");
        print("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>1 : ".$this->lowerBound."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$this->upperBound."</b><br />");
        print("<b>Using a genome with 1 real-value gene(s) .</b><br />");
        print("<b>Using Gaussian mutation with step size " . $this->stepSize . "</b><br />");
        print("<b>Population size : " . $this->population . "</b><br />");
        print("<center><input type='button' style='width:70px;font-size:12px;font-family:Tahoma;' value='Go' onclick='window.location=\"\"' ></center><br/>");
        print("<hr width='70%' align='left' /><br />");
    }

    function writeFile($arr_val){
        $sumFitness = 0;
        for($i=1 ; $i<=$this->population ; $i++)
            $sumFitness += $arr_val[$i]->fitness_value;

        $averageFitness = $sumFitness / $this->population;

        $c_text = "Population data after ".($this->population * $this->currentGeneration)." births (generation ".$this->currentGeneration.")\r\n";
        $c_text .= "Local fitness : max = ".$arr_val[1]->fitness_value." , ave = ".$averageFitness." , min = ".$arr_val[$this->population]->fitness_value . "\r\n" ;
        $c_text .= "Indiv		birthdate		fitness		gene value\r\n";
        for($i=1; $i<=$this->population; $i++)
            $c_text .= $i."		".$arr_val[$i]->birth_date."		".$arr_val[$i]->fitness_value."		".$arr_val[$i]->gene_value."\r\n";
        $c_text .= "--------------------------------------------------------------------------------\r\n";

        $handle = fopen($this->file_name, "a+");
        fwrite($handle, $c_text);
        fclose($handle);
    }


}