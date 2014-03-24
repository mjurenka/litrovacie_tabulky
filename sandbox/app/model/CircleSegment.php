<?php
class CircleSegment extends Nette\Object {
	private $radius;
	private $length;
	private $heigth;
	private $step;
	private $outputType;
	private $answer = array();
	private $bonusHeigth = 0;
	private $bonusRadius = 0;
	private $bonusVolume = 0;
	
	function __construct($radius, $length, $step, $outputType)
	{
		$this->radius = $radius;
		$this->length = $length;
		$this->heigth = 2 * $radius;
		$this->step = $step;
		$this->outputType = $outputType;
		$this->convertToAnotherType($outputType);
//		$this->calculate();
//		$this->printExcel();
	}
		
	public function setBonus($diameter, $heigth) {
		$this->bonusHeigth = $heigth;
		$this->bonusRadius = $diameter / 2;
		$this->bonusVolume = $this->calculateVolumeCylinder($this->bonusRadius, $this->bonusHeigth, $this->outputType);
	}
	public function getAnswer() {
		return $this->answer;
	}
	
	public function convert() {
		$this->convertToAnotherType($this->outputType);
	}
	
	function convertToAnotherType($type) {
		for($i = 0; $i < count($this->answer); $i++)
			$this->answer[$i]['V'] *= $type;			
	}
	function calculate() {
		$step = $this->step;
		$pos = 0;
		for($i = 0; $i <= $this->radius; $i = $i + $step) {
			$this->answer[$pos]['h'] = $i;
			$this->answer[$pos]['V'] = $this->calculateVolume($i);
			$pos++;			
		}	
		
		for($i = 1; $i <= $this->radius; $i = $i + $step) {
			$middle = $this->answer[$this->radius]['V'];
			$mirror = $this->answer[$this->radius - $i]['V'];
			$diff = $middle - $mirror;
			$new = $middle + $diff;
			
			$this->answer[$pos]['h'] = $this->radius + $i;
			$this->answer[$pos]['V'] = $new;
			$pos++;			
		}
		
		// bonus volume
		foreach($this->answer as $position => $data) {
			$this->answer[$position]['h'] += $this->bonusHeigth;
			$this->answer[$position]['V'] += $this->bonusVolume;
		}
		// remove first item
		array_shift($this->answer);
		for($i = $this->bonusHeigth; $i > 0; $i = $i - $step) {
			$data = array();
			$data['h'] = $i;
			$data['V'] = $this->bonusVolume / $this->bonusHeigth * $i;
			array_unshift($this->answer, $data);
		}
			
	}
	
	// @h - height of the segment
	function calculateVolume($h) {
		return ($this->calculateArea($h) * $this->length);
	}
	
	// @h = height of the segment
	function calculateArea($h) {
		$R = $this->radius;
		// central angle
		$C = $this->calculateCentralAngle($h);
		
		$ans = (pow($R, 2) / 2) * ($C - sin($C));
		
		return $ans;
	}
	
	// @h = height of the segment
	function calculateCentralAngle($h) {
		$R = $this->radius;
		
		// calculate in radians
		$ans = 2 * acos( ($R - $h) / $R);
		
		// echo $h.':'.$ans.'<br />';
		return $ans;
	}
	
	
	
	function calculateTotalVolume() {
		return (pi() * $this->radius * $this->radius * $this->length * $this->outputType);
	}
	
	public function calculateVolumeCylinder($radius, $height, $outputUnit) {
		return (pi() * $radius * $radius * $height);
	}
	
	function printExcel() {
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		// activate sheet
		$objPHPExcel->setActiveSheetIndex(0);
	
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Polomer: '.$this->radius);		
		$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Dĺžka: '.$this->length);
		$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Celkový objem:');
		$objPHPExcel->getActiveSheet()->SetCellValue('D1', $this->calculateTotalVolume());
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
		
		$objPHPExcel->getActiveSheet()->SetCellValue('A2', 'Výška hladiny');		
		$objPHPExcel->getActiveSheet()->SetCellValue('B2', 'Objem');
		
		$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
		
		$i = 3;
		foreach($this->answer as $ans) {
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, $ans['h']);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, $ans['V']);
			$i++;
		}
		
		$filename = 'lit_tab_'.$this->radius.'x'.$this->length.'.xlsx';
			
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
	}
}
?>