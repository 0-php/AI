<?
include("../Perceptron.php");
/**
 * Example
 */
	$filename = 'w1.json';

	/**
	 * ��� ���������� ����� �������� ��� ���� �� ����, ������� ��� ������.
	 * ������� ������, ��� � ���� ������� ����������� ���������� � ���, ���� ������ � ������.
	 */

	$neural = new Perceptron(64);	// ������� ����� 8�8, ����������� 64.

	if(!isset($filename))
	{

		/**
		 * ���� ��������
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
		$neural->teach( $v1, 1 );
		$v1 =	array(0, 1, 1, 1, 1, 1, 1, 1,
		              0, 1, 0, 0, 0, 0, 0, 1,
		              0, 1, 0, 0, 0, 0, 0, 1,
		              0, 1, 0, 0, 0, 0, 0, 1,
		              0, 1, 0, 0, 0, 0, 0, 1,
		              0, 1, 0, 0, 0, 0, 0, 1,
		              0, 1, 0, 0, 0, 0, 0, 1,
		              0, 1, 1, 1, 1, 1, 1, 1,
		              );
		$neural->teach( $v1, 1 );
		$v1 =	array(0, 0, 0, 0, 0, 0, 0, 0,
		              1, 1, 1, 1, 1, 1, 1, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 1, 1, 1, 1, 1, 1, 1,
		              );
		$neural->teach( $v1, 1 );
		$v1 =	array(0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              1, 1, 1, 1, 1, 1, 1, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 1, 1, 1, 1, 1, 1, 1,
		              );
		$neural->teach( $v1, 1 );
		$v1 =	array(0, 0, 1, 1, 1, 1, 1, 1,
		              0, 0, 1, 0, 0, 0, 0, 1,
		              0, 0, 1, 0, 0, 0, 0, 1,
		              0, 0, 1, 0, 0, 0, 0, 1,
		              0, 0, 1, 0, 0, 0, 0, 1,
		              0, 0, 1, 0, 0, 0, 0, 1,
		              0, 0, 1, 1, 1, 1, 1, 1,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              );
		$neural->teach( $v1, 1 );
		$v1 =	array(1, 1, 1, 1, 1, 1, 1, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 0, 0, 0, 0, 0, 0, 1,
		              1, 1, 1, 1, 1, 1, 1, 1,
		              );
		$neural->teach( $v1, 1 );




		/**
		 * ������ ���� ������.
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
		$neural->teach( $v1,-1 );


		$v1 =	array(0, 0, 0, 0, 0, 0, 0, 1,
		              0, 0, 0, 0, 0, 0, 1, 0,
		              0, 0, 0, 0, 0, 1, 0, 0,
		              0, 0, 0, 0, 1, 0, 0, 0,
		              0, 0, 0, 1, 0, 0, 0, 0,
		              0, 0, 1, 0, 0, 0, 0, 0,
		              0, 1, 0, 0, 0, 0, 0, 0,
		              1, 0, 0, 0, 0, 0, 0, 0,
		              );
		$neural->teach( $v1,-1 );

		$v1 =	array(0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              1, 1, 1, 1, 1, 1, 1, 1,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              );
		$neural->teach( $v1, -1 );

		$v1 =	array(0, 1, 0, 0, 0, 0, 0, 0,
		              0, 1, 0, 0, 0, 0, 0, 0,
		              0, 1, 0, 0, 0, 0, 0, 0,
		              0, 1, 0, 0, 0, 0, 0, 0,
		              0, 1, 0, 0, 0, 0, 0, 0,
		              0, 1, 0, 0, 0, 0, 0, 0,
		              0, 1, 0, 0, 0, 0, 0, 0,
		              0, 1, 0, 0, 0, 0, 0, 0,
		              );
		$neural->teach( $v1,-1 );

		$v1 =	array(1, 0, 0, 0, 0, 0, 0, 0,
		              0, 1, 0, 0, 0, 0, 0, 0,
		              0, 0, 1, 0, 0, 0, 0, 0,
		              0, 0, 0, 1, 0, 0, 0, 0,
		              0, 0, 0, 0, 1, 0, 0, 0,
		              0, 0, 0, 0, 0, 1, 0, 0,
		              0, 0, 0, 0, 0, 0, 1, 0,
		              0, 0, 0, 0, 0, 0, 0, 1,
		              );
		$neural->teach( $v1,-1 );

		$v1 =	array(0, 0, 0, 0, 0, 1, 0, 0,
		              0, 0, 0, 0, 0, 1, 0, 0,
		              0, 0, 0, 0, 0, 1, 0, 0,
		              0, 0, 0, 0, 0, 1, 0, 0,
		              0, 0, 0, 0, 0, 1, 0, 0,
		              0, 0, 0, 0, 0, 1, 0, 0,
		              0, 0, 0, 0, 0, 1, 0, 0,
		              0, 0, 0, 0, 0, 1, 0, 0,
		              );
		$neural->teach( $v1,-1 );


		/**
		 * ������� ��������� ��������.
		 * ������� ���� � ����.
		 */

		$neural->weight_save("w1.txt");
	}
	else
	{
		// ����� ������ ��� �� ������, ������.
		$neural->weight_load($filename);;
	}


	// ���� �� ���� �������, �������� ���� � �������
		$v1 =	array(0, 0, 1, 1, 1, 1, 1, 1,
		              0, 0, 1, 0, 0, 0, 0, 1,
		              0, 0, 1, 0, 0, 0, 0, 1,
		              0, 0, 1, 0, 0, 0, 0, 1,
		              0, 0, 1, 0, 0, 0, 0, 1,
		              0, 0, 1, 0, 0, 0, 0, 1,
		              0, 0, 1, 0, 0, 0, 0, 1,
		              0, 0, 1, 1, 1, 1, 1, 1,
		              );
	echo $neural->ask( $v1 )==1?"���������":"������";
	echo "\r\n";
	// ��� ����
		$v1 =	array(0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 1, 1, 1, 1, 1, 0,
		              0, 0, 1, 0, 0, 0, 1, 0,
		              0, 0, 1, 0, 0, 0, 1, 0,
		              0, 0, 1, 0, 0, 0, 1, 0,
		              0, 0, 1, 0, 0, 0, 1, 0,
		              0, 0, 1, 1, 1, 1, 1, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              );
	echo $neural->ask( $v1 )==1?"���������":"������";
	echo "\r\n";

	// ������ ������� ��� �����
		$v1 =	array(0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              1, 1, 1, 1, 1, 1, 1, 1,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              );

	echo $neural->ask( $v1 )==1?"���������":"������";
	echo "\r\n";

		// ����� ������ ��� ������ ������ 5, � �� 8.
		$v1 =	array(0, 0, 0, 0, 1, 0, 0, 0,
		              0, 0, 0, 1, 0, 0, 0, 0,
		              0, 0, 1, 0, 0, 0, 0, 0,
		              0, 1, 0, 0, 0, 0, 0, 0,
		              1, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              0, 0, 0, 0, 0, 0, 0, 0,
		              );

	echo $neural->ask( $v1 )==1?"���������":"������";
	echo "\r\n";
?>