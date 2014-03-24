<?php
use \Nette\Application\UI\Form;
/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter
{

	public function renderDefault()	{
		$this->template->anyVariable = 'any value';
	}
	
	protected function createComponentLaidDownCylinder() {
		$form = new Form;
		$form->addText("diameter", "Diameter (even number)");
		$form->addText("length", "Length");
		$form->addText("bonusHeight", "Kalnik vyska");
		$form->addText("bonusDiameter", "Kalnik diameter");
		$form->addText("step", "Step");
		$form->addSelect("output", "Output units", $this->getOutputArray());
		$form->addSubmit("submit", "Calculate");
		$form->onSuccess[] = $this->laidDownCylinderSubmitted;
		$form->setDefaults(array(
			"output" => "0.001",
			"bonusHeight" => 0,
			"bonusVolume" => 0,
			"step" => 1,
		));
		return $form;
	}
	
	public function laidDownCylinderSubmitted(Form $form) {
		$values = $form->getValues();
		
		$radius = $values->diameter / 2;
		$length = $values->length;
		$step = $values->step;
		$outputUnit = $values->output;
		$model = new CircleSegment($radius, $length, $step, $outputUnit);
		$model->setBonus($values->bonusDiameter, $values->bonusHeight);
		$model->calculate();
		$model->convert();
		$model->printExcel();
		die(print_r($model->getAnswer()));	
	}
	
	protected function createComponentCylinderAndBrick() {
		$form = new Form;
		$form->addText("height", "Height");
		$form->addText("width", "Width");
		$form->addText("length", "Length");
		$form->addText("arcHeight", "Arch height");
		$form->addText("step", "Step");
		$form->addSelect("output", "Output units", $this->getOutputArray());
		$form->addSubmit("submit", "Calculate");
		
		$form->setDefaults(array("output" => "0.001"));
		return $form;
	}
	
	protected function createComponentBrick() {
		$form = new Form;
		$form->addText("height", "Height");
		$form->addText("width", "Width");
		$form->addText("length", "Length");
		$form->addText("step", "Step");
		$form->addSelect("output", "Output units", $this->getOutputArray());
		$form->addSubmit("submit", "Calculate");
		
		$form->setDefaults(array("output" => "0.001"));
		return $form;
	}
	
	private function getOutputArray() {
		$out = array();
		$out["0.000000000001"] = "km3";
		$out["0.000001"] = "hm3";
		$out["0.000001"] = "m3";
		$out["0.001"] = "dm3 / l";
		$out["1"] = "cm3";
		$out["1000"] = "mm3";
		return $out;
	}

}
