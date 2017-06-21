<?php

namespace app\controllers;

use yii\web\Controller;
use yii\helpers\Url;

class DateController extends Controller
{
	public function actionIndex()
	{
		return $this->render('date');
	}

	public function actionUrl()
	{
		echo Url::to(['country/index']) . '<br>';
		echo Url::to(['post/view', 'id' => 100]) . '<br>';
		echo Url::to(['post/view', 'id' => 100, 'content' => 'php']) . '<br>';
		echo Url::to(['post/index'], true) . '<br>';  //http://study.yii.com/index.php?r=post%2Findex
		echo Url::to(['post/index'], 'https') . '<br>';        //https://study.yii.com/index.php?r=post%2Findex
		
	 	echo \Yii::setAlias('@example', 'http://example.com/');
	 	echo Url::to('@example') . '<br>';
	 	echo Url::to(['images/logo.gif'], true) . '<br>';
	 	
	 	echo Url::home() . '<br>';  // 首页
	 	echo url::base() . '<br>';  // 当应用程序依赖于WEB根目录的子文件时有效
	 	echo Url::canonical() . '<br>';  // 当前请求的URL
	 	Url::remember() . '<br>';  //记住当前请求的URL  并在之后的请求中检索它
	 	echo Url::previous() . '<br>';  
	 	
	}
}