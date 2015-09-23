<?php
	/**
	 * простой перцептрон.
	 *
	 */
	class Perceptron {
		private $W;		// веса
		private $size;	// размерность
		private $porog;	// порог

		/**
		 * Спрашиваем перцетрон.
		 * Иначе говоря функция активации
		 *
		 * @param array $vector
		 * @return int
		 */
		public function ask($vector){
			$sum = 0;
			for($i=0;$i<count($vector);$i++)
			{
				$sum += $vector[$i]*$this->W[$i];
			}
			if($sum > $this->porog) return 1;
			return -1;
		}

		/**
		 * Конструктор
		 * Аргумент - размерность перцептрона
		 *
		 * @param int $n
		 */
		public function __construct($n){
			$this->size	= $n;
			$this->porog = 100;
			$this->init_weight();
		}

		/**
		 * Инициализация начальных весов.
		 * Рандом
		 *
		 */
		public function init_weight(){
			for($i=0;$i<$this->size;$i++) $this->W[] = rand(0, 10);
		}

		/**
		 * Сохраняем в файл
		 * Если файл есть - перезапишет
		 *
		 * @param string $filename
		 */
		public function weight_save($filename){
				$serialize = serialize($this->W);
				fwrite( fopen($filename,"w"), $serialize);
		}

		/**
		 * Грузим весы из файла
		 *
		 *
		 * @param string $filename
		 */
		public function weight_load($filename){
			$this->W = unserialize(file_get_contents($filename));

		}

		public function teach($vector, $d){
			if($d!=$this->ask($vector))
				// teach
				for($i=0;$i<$this->size;$i++)
					$this->W[$i] += $d*$vector[$i];
		}
	}
?>