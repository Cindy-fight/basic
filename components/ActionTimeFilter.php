<?php

namespace app\components;

use Yii;
use yii\base\ActionFilter;

class ActionTimeFilter extends ActionFilter
{
	private $startTime;

	public function beforeAction($action){
		$this->startTime = microtime(true);
		return parent::beforeAction($action);
	}

	public function afterAction($action, $result){
		$time = microtime(true) - $this->startTime;
		Yii::trace("Action '{$action->uniqueId}' spent $time second.");
		return parent::afterAction($action, $result);
	}
}