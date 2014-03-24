<?php
class cubeSegment
{
	private $depth;
	private $length;
	private $heigth;
	private $step;
	private $outputType;
	private $answer = array();

	function __construct($heigth, $length, $depth, $step, $outputType) {
		$this->depth = $depth;
		$this->length = $length;
		$this->heigth = $heigth;
		$this->step = $step;
		$this->outputType = $outputType;
		$this->calculate();
		$this->convertToAnotherType($outputType);
		$this->printExcel();
	}

	function convertToAnotherType($type) {
		for($i = 0; $i < count($this->answer); $i++)
			$this->answer[$i]['V'] *= $type;
	}

	function calculate() {
		$step = $this->step;
		$pos = 0;

		for($i = 0; $i <= $this->heigth; $i = $i + $step) {
			$this->answer[$pos]['h'] = $i;
			$this->answer[$pos]['V'] = $this->calculateVolume($i);
			$pos++;
		}
	}

	// @h - height of the segment
	function calculateVolume($h)
	{
		return ($h * $this->depth * $this->length);
	}

	function calculateTotalVolume()
	{
		return ($this->length * $this->depth * $this->heigth * $this->outputType);
	}

	function printExcel()
	{
		require_once('./PHPExcel.php');

		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		// activate sheet
		$objPHPExcel->setActiveSheetIndex(0);

		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Sirka: '.$this->length);
		$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Vyska: '.$this->heigth);
		$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Dĺžka: '.$this->depth);
		$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Celkový objem:');
		$objPHPExcel->getActiveSheet()->SetCellValue('E1', $this->calculateTotalVolume());

		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);

		$objPHPExcel->getActiveSheet()->SetCellValue('A2', 'Výška hladiny');
		$objPHPExcel->getActiveSheet()->SetCellValue('B2', 'Objem');

		$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);

		$i = 3;
		foreach($this->answer as $ans)
		{
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, $ans['h']);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, $ans['V']);
			$i++;
		}

		$filename = 'kvader_lit_tab_'.$this->heigth.'x'.$this->length.'x'.$this->depth.'.xlsx';

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