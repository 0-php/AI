<?php
header("Content-Type: text/html; charset=utf-8");
include("../../../classes/perceptron.class.php");
/**
 * Example
 */
	$filename = 'w1.txt';
	/**
	 * Наш перцептрон будет говорить что дали на вход, квадрат или прямую.
	 * Следует учесть, что в этом примере перцептрона спрашивают о том, чего не было в учении.
	 */
	$neural = new Perceptron(64);	// матрица будет 8х8, размерность 64.
	if(1/*!is_file($filename)*/){
		/**
		 * Учим квадраты
		 */
		$v1 =	array(1, 1, 1, 1, 1, 1, 1, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 1, 1, 1, 1, 1, 1, 1,
		              );
		$neural->learn( $v1, 1 );
		$v1 =	array(0, 0, 0, 0, 0, 0, 0, 0,
		              0, 1, 1, 1, 1, 1, 1, 0,
		              0, 1, 0, 0, 0, 0, 1, 0,
		              0, 1, 0, 0, 0, 0, 1, 0,
		              0, 1, 0, 0, 0, 0, 1, 0,
		              0, 1, 0, 0, 0, 0, 1, 0,
		              0, 1, 1, 1, 1, 1, 1, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              );
		$neural->learn( $v1, 1 );
		$v1 =	array(0, 1, 1, 1, 1, 1, 1, 1,
		              0, 1, 0, 0, 0, 0, 0, 1,
		              0, 1, 0, 0, 0, 0, 0, 1,
		              0, 1, 0, 0, 0, 0, 0, 1,
		              0, 1, 0, 0, 0, 0, 0, 1,
		              0, 1, 0, 0, 0, 0, 0, 1,
		              0, 1, 0, 0, 0, 0, 0, 1,
		              0, 1, 1, 1, 1, 1, 1, 1,
		              );
		$neural->learn( $v1, 1 );
		$v1 =	array(0, 0, 0, 0, 0, 0, 0, 0,
		              1, 1, 1, 1, 1, 1, 1, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 1, 1, 1, 1, 1, 1, 1,
		              );
		$neural->learn( $v1, 1 );
		$v1 =	array(0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              1, 1, 1, 1, 1, 1, 1, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 1, 1, 1, 1, 1, 1, 1,
		              );
		$neural->learn( $v1, 1 );
		$v1 =	array(0, 0, 1, 1, 1, 1, 1, 1,
		              0, 0, 1, 0, 0, 0, 0, 1,
		              0, 0, 1, 0, 0, 0, 0, 1,
		              0, 0, 1, 0, 0, 0, 0, 1,
		              0, 0, 1, 0, 0, 0, 0, 1,
		              0, 0, 1, 0, 0, 0, 0, 1,
		              0, 0, 1, 1, 1, 1, 1, 1,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              );
		$neural->learn( $v1, 1 );
		$v1 =	array(1, 1, 1, 1, 1, 1, 1, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 1, 1, 1, 1, 1, 1, 1,
		              );
		$neural->learn( $v1, 1 );

		/**
		 * Теперь учим прямые.
		 */
		$v1 =	array(1, 1, 1, 1, 1, 1, 1, 1,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              );
		$neural->learn( $v1,-1 );


		$v1 =	array(0, 0, 0, 0, 0, 0, 0, 1,
		              0, 0, 0, 0, 0, 0, 1, 0,
		              0, 0, 0, 0, 0, 1, 0, 0,
		              0, 0, 0, 0, 1, 0, 0, 0,
		              0, 0, 0, 1, 0, 0, 0, 0,
		              0, 0, 1, 0, 0, 0, 0, 0,
		              0, 1, 0, 0, 0, 0, 0, 0,
		              1, 0, 0, 0, 0, 0, 0, 0,
		              );
		$neural->learn( $v1,-1 );

		$v1 =	array(0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              1, 1, 1, 1, 1, 1, 1, 1,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              );
		$neural->learn( $v1, -1 );

		$v1 =	array(0, 1, 0, 0, 0, 0, 0, 0,
		              0, 1, 0, 0, 0, 0, 0, 0,
		              0, 1, 0, 0, 0, 0, 0, 0,
		              0, 1, 0, 0, 0, 0, 0, 0,
		              0, 1, 0, 0, 0, 0, 0, 0,
		              0, 1, 0, 0, 0, 0, 0, 0,
		              0, 1, 0, 0, 0, 0, 0, 0,
		              0, 1, 0, 0, 0, 0, 0, 0,
		              );
		$neural->learn( $v1,-1 );

		$v1 =	array(1, 0, 0, 0, 0, 0, 0, 0,
		              0, 1, 0, 0, 0, 0, 0, 0,
		              0, 0, 1, 0, 0, 0, 0, 0,
		              0, 0, 0, 1, 0, 0, 0, 0,
		              0, 0, 0, 0, 1, 0, 0, 0,
		              0, 0, 0, 0, 0, 1, 0, 0,
		              0, 0, 0, 0, 0, 0, 1, 0,
		              0, 0, 0, 0, 0, 0, 0, 1,
		              );
		$neural->learn( $v1,-1 );

		$v1 =	array(0, 0, 0, 0, 0, 1, 0, 0,
		              0, 0, 0, 0, 0, 1, 0, 0,
		              0, 0, 0, 0, 0, 1, 0, 0,
		              0, 0, 0, 0, 0, 1, 0, 0,
		              0, 0, 0, 0, 0, 1, 0, 0,
		              0, 0, 0, 0, 0, 1, 0, 0,
		              0, 0, 0, 0, 0, 1, 0, 0,
		              0, 0, 0, 0, 0, 1, 0, 0,
		              );
		$neural->learn( $v1,-1 );
		/**
		 * Выучили небольшой выборкой.
		 * Запишем веса в файл.
		 */
		$neural->weight_save("w1.txt");
	} else {
		// Чтобы каждый раз не учиться, грузим.
		$neural->weight_load($filename);
	}

	// Даем на вход квадрат, которого нету в выборке
		$v1 =	array(0, 0, 0, 0, 0, 0, 0, 0,
		              0, 1, 1, 1, 1, 1, 1, 0,
		              0, 1, 0, 0, 0, 0, 1, 0,
		              0, 1, 0, 0, 0, 0, 1, 0,
		              0, 1, 0, 0, 0, 0, 1, 0,
		              0, 1, 0, 0, 0, 0, 1, 0,
		              0, 1, 1, 1, 1, 1, 1, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              );
	echo $neural->ask( $v1 )==1?"Квадрат":"прямая";
	echo "\r\n";
	// Еще один
		$v1 =	array(0, 0, 1, 1, 1, 1, 1, 1,
		              0, 0, 1, 0, 0, 0, 0, 1,
		              0, 0, 1, 0, 0, 0, 0, 1,
		              0, 0, 1, 0, 0, 0, 0, 1,
		              0, 0, 1, 0, 0, 0, 0, 1,
		              0, 0, 1, 0, 0, 0, 0, 1,
		              0, 0, 1, 0, 0, 0, 0, 1,
		              0, 0, 1, 1, 1, 1, 1, 1,
		              );
	echo $neural->ask( $v1 )==1?"Квадрат":"прямая";
	echo "\r\n";

	// Теперь спросим про линии
		$v1 =	array(0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              1, 1, 1, 1, 1, 1, 1, 1,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              );

	echo $neural->ask( $v1 )==1?"Квадрат":"прямая";
	echo "\r\n";

		// Здесь просим про прямую длиной 5, а не 8.
		$v1 =	array(0, 0, 0, 0, 1, 0, 0, 0,
		              0, 0, 0, 1, 0, 0, 0, 0,
		              0, 0, 1, 0, 0, 0, 0, 0,
		              0, 1, 0, 0, 0, 0, 0, 0,
		              1, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              );

	echo $neural->ask( $v1 )==1?"Квадрат":"прямая";
	echo "\r\n";
?>