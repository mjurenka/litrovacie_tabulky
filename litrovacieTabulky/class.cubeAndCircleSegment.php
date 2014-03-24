<?php
class cubeAndCircleSegment
{
	private $radius;
	private $arcHeigth;
	private $height;
	private $width;
	private $depth;
	private $step;
	private $outputType;
	private $answer = array();
	
	function __construct($height, $width, $depth, $arcHeigth, $step, $outputType) {
		$this->height = $height;
		$this->width = $width;
		$this->depth = $depth;
		$this->arcHeigth = $arcHeigth;
		$this->radius = $this->calculateRadiusOfArc($arcHeigth, $width);
		$this->step = $step;
		$this->outputType = $outputType;
		$this->calculate();
		$this->convertToAnotherType($outputType);
		$this->printExcel();
	}
	
	function calculateRadiusOfArc($arcHeigth, $arcWidth) {
		$r = $arcHeigth / 2;
		$r += (pow($arcWidth, 2) / (8 * $arcHeigth));
		return $r;
	}
	
	function convertToAnotherType($type)
	{
		for($i = 0; $i < count($this->answer); $i++)
			$this->answer[$i]['V'] *= $type;			
	}
	
	function calculate() {
		// 0 - arcHeigth : calculate vol of segment
		// arcHeigth - half: calculate volume of brick
		$step = $this->step;
		$pos = 0;
		$halfHeight = $this->height / 2;
		$maxVolArc = 0;
		
		for($i = 0; $i <= $halfHeight; $i = $i + $step) {
			$this->answer[$pos]['h'] = $i;
			if($i <= $this->arcHeigth) {
				$this->answer[$pos]['V'] = $this->calculateVolumeArc($i);
				$maxVolArc = $this->answer[$pos]['V'];
			} else {
				$this->answer[$pos]['V'] = $maxVolArc + $this->calculateVolumeBrick($i - $this->arcHeigth);
			}
			$pos++;
		}
			
		for($i = 1; $i <= $halfHeight; $i = $i + $step) {
			$middle = $this->answer[$halfHeight]['V'];
			$mirror = $this->answer[$halfHeight - $i]['V'];
			$diff = $middle - $mirror;
			$new = $middle + $diff;
			
			$this->answer[$pos]['h'] = $halfHeight + $i;
			$this->answer[$pos]['V'] = $new;
			$pos++;			
		}
			
	}
	
	// @h - height of the segment
	function calculateVolumeArc($h)	{
		return ($this->calculateArea($h) * $this->depth);
	}
	
	function calculateVolumeBrick($h) {
		return $this->width * $this->depth * $h;
	}
	
	// @h = height of the segment
	function calculateArea($h)
	{
		$R = $this->radius;
		// central angle
		$C = $this->calculateCentralAngle($h);
		
		$ans = (pow($R, 2) / 2) * ($C - sin($C));
		
		return $ans;
	}
	
	// @h = height of the segment
	function calculateCentralAngle($h)
	{
		$R = $this->radius;
		
		// calculate in radians
		$ans = 2 * acos( ($R - $h) / $R);
		
		// echo $h.':'.$ans.'<br />';
		return $ans;
	}
	
	function printTable()
	{
		echo $this->radius;
		echo '<table>
				<tr>
					<th>h</th>
					<th>V</th>
				</tr>';
		foreach($this->answer as $ans)
			echo '<tr>
					<td>'.$ans['h'].'</td>
					<td>'.$ans['V'].'</td>
				</tr>';
		echo '</table>';
	}
	
	function calculateTotalVolume()
	{
		return 0;
	}
	
	function printExcel()
	{
		require_once('./PHPExcel.php');
		
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
			
		// activate sheet
		$objPHPExcel->setActiveSheetIndex(0);
	
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Polomer: '.$this->radius);		
		$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Dĺžka: '.$this->depth);
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
		foreach($this->answer as $ans)
		{
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, $ans['h']);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, $ans['V']);
			$i++;
		}
		
		$filename = 'lit_tab_MIX_' . $this->depth . '.xlsx';
			
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