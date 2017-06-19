<?php

namespace app\controllers;

use yii\web\Controller;

class DateController extends Controller
{
	public function actionIndex()
	{
		return $this->render('date');
	}
}