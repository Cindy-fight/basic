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
	
	public function actionRequest($id=1, $name2 = 'bao')
	{
		$request = \Yii::$app->request;

		$get = $request->get();  //获取GET请求的所有参数
		$post = $request->post(); //获取POST请求的所有参数
		$id = $request->get('id', 1);  //$id = isset($_GET['id']) ? $_GET['id'] : 1;
		$name1 = $request->post('name');  //$name1 = isset($_POST['name']) ? $_POST['name'] : null;
		$name2 = $request->post('name', 'cindy');  // $name2 = isset($_POST['name']) ? $_POST['name'] : 'cindy';
		
		$params = $request->bodyParams;  // 获取 除GET POST 请求之外的其他诸如PUT PATCH之类的请求 过来的参数
		$param = $request->getBodyParam('id', 1);		

		$headers = \Yii::$app->request->headers;  //获取Http头信息
// 		$accept = $headers->get('Accept');
// 		if ($headers->has('User-Agent')){
			
// 			return json_encode($headers);
// 		}

		//客户端信息
		$userHost = \Yii::$app->request->userHost;
		$userIp = \Yii::$app->request->userIP;
		
		return $userHost . $userIp;
	}
	
	public function actionJudgeRequestMethod()
	{
		$request = \Yii::$app->request;
		//判断请求方法
		if ($request->isAjax){
			return 'ajax';
		}elseif ($request->isGet){
			return 'get';
		}elseif ($request->isPost){
			return 'post';
		}elseif ($request->isPut){
			return 'put';
		}elseif ($request->isPatch){
			return 'patch';
		}
	}
	
	public function actionResponse()
	{
		\Yii::$app->response->statusCode = 200;  //状态码的设置
		
		try {
			
		}catch (Exception $e){
			throw new \yii\web\NotFoundHttpException();
		}
		
		//exception
// 		\yii\web\BadRequestHttpException;
// 		\yii\web\NotFoundHttpException;
// 		\yii\web\NotAcceptableHttpException;
		
		//Http头部
		$headers = \Yii::$app->response->headers;
		$headers->add('pragma', 'no-cache');  // 增加一个 Pragma 头，已存在的Pragma 头不会被覆盖。
		$headers->set('pragma', 'no-cache');  // 增加一个 Pragma 头，任何已存在的Pragma 头 都会被丢弃。
		$headers->remove('pragma');  // 删除 Pragma 头，并返回删除的Pragma 头的值到数组。
		
		// 响应主体
		\Yii::$app->response->content = 'Hello World';
		//format data属性
		$reponse = \Yii::$app->response;
		$reponse->format = \yii\web\Response::FORMAT_JSON;
		$reponse->data =['message' => 'Hello World!'];
	}
	
	public function actionTest()
	{
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return [
				'message' => 'Hello World',
				'code'	=> '100',
		];
	
	}
	
	public function actionInfo()
	{
		return \Yii::createObject([
				'class'		=> 'yii\web\Response',
				'format'	=> \yii\web\Response::FORMAT_JSON,
				'data'		=> [
						'message'	=> 'Hello World, Hello My Honey',
						'code'		=> '100',
				],
		]);
	}
	
	
	//注意 Redirect 与 Redirects 作用相同
	public function actionRedirect()
	{
		return $this->redirect('http://study.yii.com/index.php/country/');
	}
	
	public function actionRedirects()
	{
		\Yii::$app->response->redirect('http://study.yii.com/index.php/country/')->send();
	}
	
	// download downloads 作用相同  \yii\web\Response::send();方法的作用是 确保没有其他内容追加到响应中
	public function actionDownload()
	{
		\Yii::$app->response->sendFile('/tmp/channel.csv');  
	}
	
	public function actionDownloads()
	{
		\Yii::$app->response->sendFile('/Users/admin/Sites/log/pay_error_log.log')->send();
	}
	
	//sessions
	public function actionSessions()
	{
		$session = \Yii::$app->session;
		if ($session->isActive){ //检查session是否开启
			
		}else {
			$session->open();
		}
		
		$session->close();
		$session->destroy();  //销毁session中所有已注册的数据
		
		//访问session数据
		//获取session中的变量值
		$language = $session->get('language');
		$language = $session['language'];
		$language = isset($_SESSION['language']) ? $_SESSION['language'] : null;
		
		//设置一个session变量
		$session->set('language', 'en-US');
		$session['language'] = 'en-US';
		$_SESSION['language'] = 'en-US';
		
		//删除一个session变量
		$session->remove('language');
		unset($session['language']);
		unset($_SESSION['language']);
		
		//检查session变量是否已存在
		if ($session->has('language')){}
		if (isset($session['language'])){}
		if (isset($_SESSION['language'])){}
		
		//遍历所有session变量
		foreach ($session as $name => $value){}
		foreach ($_SESSION as $name => $value){}
		
	}
	
	public function actionSessionarray()
	{
		$session = \Yii::$app->session;
		$session['captcha'] = new \ArrayObject();
		$session['captcha']['number'] = 5;
		$session['captcha']['lifetime'] = 3600;
		return $session['captcha']['lifetime'];
	}
	
	public function actionFlash()
	{
		$session = \Yii::$app->session;
		$session->setFlash('postDeleted', 'You have successfully deleted your post.');
		echo $session->getFlash('postDeleted');
	}
	
	public function actionGetflash()
	{
		return \Yii::$app->session->hasFlash('postDeleted');
	}
	
	public function actionAddflash()
	{
		$session = \Yii::$app->session;
		$session->addFlash('alerts', 'You have successfully deleted your post.');
		$session->addFlash('alerts', 'You have successfully add a new friend.');
		$session->addFlash('alerts', 'You are promoted');
		$alerts = $session->getFlash('alerts');
		return json_encode($alerts);
	}
	
	public function actionCookie()
	{
		$cookies = \Yii::$app->request->cookies;
		// 获取名为 "language" cookie 的值，如果不存在，返回默认值"en"
		$language = $cookies->getValue('language', 'en');
		
		// 另一种方式获取名为 "language" cookie 的值
		if (($cookie = $cookies->get('language')) !== null){
			$language = $cookie->value;
		}
		
		// 可将 $cookies当作数组使用
		if (isset($cookies['language'])){
			$language = $cookies['language']->value;
		}
		
		// 判断是否存在名为"language" 的 cookie
		if ($cookies->has('language')){}
		if (isset($cookies['language'])){}
		
		// 在要发送的响应中添加一个新的cookie
		$cookies->add(new \yii\web\Cookie([
				'name'	=> 'language',
				'value'	=> 'zh-CN',
		]));
		
		//// 删除一个cookie
		$cookies->remove('language');
		unset($cookies['language']);
	}

}