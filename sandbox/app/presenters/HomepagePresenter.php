<?php
use \Nette\Application\UI\Form;
use \Nette\Caching\Cache;
/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter
{
	/** @var \Nette\Caching\Cache */
	private $cache;
	
	public function startup() {
		parent::startup();
		$storage = new Nette\Caching\Storages\FileStorage(__DIR__ . '/../../temp');
		$this->cache = new Cache($storage);
	}

	public function renderDefault()	{
		$this->template->anyVariable = 'any value';
	}
	
	public function renderPreview($key) {		
		$this->template->data = $this->cache->load($key);
		
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
			"bonusDiameter" => 0,
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
		
		$data = $model->getAnswer();
		$key = time();
		$this->cache->save($key, $data);
		$this->redirect("preview", $key);
	}
	
	protected function createComponentCylinderAndBrick() {
		$form = new Form;
		$form->addText("height", "Height");
		$form->addText("width", "Width");
		$form->addText("length", "Length");
		$form->addText("arcHeight", "Arch height");
		$form->addText("bonusHeight", "Kalnik vyska");
		$form->addText("bonusDiameter", "Kalnik diameter");
		$form->addText("step", "Step");
		$form->addSelect("output", "Output units", $this->getOutputArray());
		$form->addSubmit("submit", "Calculate");
		$form->onSuccess[] = $this->cylinderAndBrickSubmitted;
		$form->setDefaults(array(
			"output" => "0.001",
			"bonusHeight" => 0,
			"bonusDiameter" => 0,
			"step" => 1,
		));
		return $form;
	}
	
	public function cylinderAndBrickSubmitted(Form $form) {
		$values = $form->getValues();
		
		$height = $values->height;
		$width = $values->width;
		$length = $values->length;
		$arcHeight = $values->arcHeight;
		$bonusHeight = $values->bonusHeight;
		$bonusDiameter = $values->bonusDiameter;
		$step = $values->step;
		$outputUnit = $values->output;
		
		$model = new CubeAndCircleSegment($height, $width, $length, $arcHeight, $step, $outputUnit);
		$model->setBonus($bonusDiameter, $bonusHeight);
		$model->calculate();
		$model->convert();
		
		$data = $model->getAnswer();
		$key = time();
		$this->cache->save($key, $data);
		$this->redirect("preview", $key);
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
