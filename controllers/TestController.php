<?php

namespace app\controllers;

use yii\web\Controller;
use yii\db\Query;
use app\models\Customer;
use yii\web\NotFoundHttpException;
use app\models\Orders;

class TestController extends Controller
{
	public function actionIndex(){
		echo 'Hello World!';
	}
	
	public function actionDatabase()
	{
		//查询语句
		//返回多行 如果没有结果则返回空数组
		$posts = \Yii::$app->db->createCommand('select * from post')->queryAll();
		
		//返回一行 如果没有结果则返回 false
		$post = \Yii::$app->db->createCommand('SELECT * FROM post WHERE id=1')->queryOne();
		
		//返回一列  如果没有结果则返回空数组
		$titles = \Yii::$app->db->createCommand('SELECT title FROM post')->queryColumn();
		
		//返回一个标量值  如果没有结果则返回false
		$count = \Yii::$app->db->createCommand('SELECT COUNT(*) FROM post')->queryScalar();
		
		//绑定参数的三种方法
		$post1 = \Yii::$app->db->createCommand('SELECT * FROM post WHERE id=:id AND status=:status')->bindValue(':id', $_GET['id'])->bindValue(':status', 1)->queryOne();
		
		$params = [':id' => $_GET['id'], ':status' => 1];
		$post2 = \Yii::$app->db->createCommand('SELECT * FROM post WHERE id=:id AND status=:status')->bindValues($params)->queryOne();
		
		$post3 = \Yii::$app->db->createCommand('SELECT * FROM post WHERE id=:id AND status=:status',$params)->queryOne();
		
		
		//非查询语句  返回执行SQL所影响的行数
		$update = \Yii::$app->db->createCommand('UPDATE post SET id=1 AND status=1')->execute();
		// UPDATE (table name, column values, condition)
		$update1 = \Yii::$app->db->createCommand()->update('user', ['status' => 1], 'age' > 30)->execute();
		// INSERT (table name, column values)
		$insert = \Yii::$app->db->createCommand()->insert('user', ['name' => 'sandy', 'age'=> 18])->execute();
		// DELETE (table name, condition)
		$delete = \Yii::$app->db->createCommand()->delete('user','status = 0')->execute();
		//一次插入多行数据  // table name, column names, column values
		$inserts = \Yii::$app->db->createCommand()->batchInsert('user', ['name','age'], [['cindy',18],['sandy',22],['lucy','20']])->execute();
		
		
		//引用表和列名称
		//[[column name]] 使用两对方括号内来将列名括起来
		//{{table name}}  使用两对大括号来将表名括起来
		$count1 = \Yii::$app->db->createCommand("SELECT COUNT([[id]] FROM {{employee}})")->queryScalar();
		
		
		//执行事务(两种方法)
		$db = \Yii::$app->db;
		\Yii::$app->db->transaction(function($db){
			$db->createCommand($sql1)->execute();
			$db->createCommand($sql2)->execute();
		});
		
		$db = \Yii::$app->db;
		$transaction = $db->beginTransaction();
		try {
			$db->createCommand($sql1)->execute();
			$db->createCommand($slq2)->execute();
			$transaction->commit();
			
		}catch (\Exception $e){
			$transaction->rollBack();
			throw $e;
		}
		
		//指定隔离级别(两种方法)
		$isolationLevel = yii\db\Transaction::REPEATABLE_READ;
		\Yii::$app->db->transaction(function ($db){
			
		}, $isolationLevel);
		
		$transaction = \Yii::$app->db->beginTransaction($isolationLevel);
		
		
		//嵌套事务(两种方法，建议在实际开发中使用第二种方法)
		\Yii::$app->db->transaction(function ($db){
			
			$db->transaction(function ($db){
				
			});
		});
		
		$db = \Yii::$app->db;
		$outerTransaction = $db->beginTransaction();
		try {
			$db->createCommand($sql1)->execute();
			
			$innerTransaction = $db->beginTransaction();
			try {
				$db->createCommand($sql2)->execute();
				$innerTransaction->commit();
			}catch (\Exception $e){
				$innerTransaction->rollBack();
				throw $e;
			}
			$outerTransaction->commit();
		}catch (\Exception $e){
			$outerTransaction->rollBack();
			throw $e;
		}
		
		//操纵数据库模式
		// createTable()
		\Yii::$app->db->createCommand()->createTable('post', [
				'id'	=> 'pk',
				'title'	=> 'string',
				'text'	=> 'text',
		]);
		//检索某张表的定义信息，包含列、主键、外键等
		$table = \Yii::$app->db->getTableSchema('post');
	}
	
	public function actionQuery()
	{
		$rows = (new \yii\db\Query())
		->select(['id', 'email'])
		->from('user')
		->where(['lastName' => 'Smith'])
		->limit(10)
		->all();
		
		$subQuery = (new Query())->select('COUNT(*)')->from('user');
		$query = (new Query())->select(['id', 'count' => $subQuery])->from('post');
		$query->select('user_id')->distinct();
		//addSelect() 选取附加字段
		$query->select(['id','username'])->addSelect(['email']);
		
		//where 三种不同的格式
		//字符串格式
		$query->where('status',1);
		$query->where('status=:status', [':status' => $status]);
		//哈希格式
		$query->where([
				'status'	=> 10,
				'type'		=> null,
				'id'		=> [4,8,15],
		]);
		
		//操作符格式
		$query->where(['and', 'id=1', 'id=2']);
		$query->where(['or', 'id=1', 'id=2']);
		$query->where(['between', 'id', 1, 10]);
		$query->where(['notbetween', 'id', 1, 10]);
		$query->where(['in', 'id', [1,2,3]]);
		$query->where(['not in', 'id', [1,2,3]]);
		$query->where(['like', 'name', 'lucy']);
		$query->where(['like', 'name', ['test', 'lucy']]);
		$query->where(['or like', 'name', ['test', 'lucy']]);
		$query->where(['>', 'age', 10]);
		
		// andWhere orWhere
		$query1 = $query->where(['<=', 'id', 10]);
		$query2 = $query1->addWhere(['in', 'age', [16,18,20]]);
		$query3 = $query1->orWhere(['like', 'name', 'lucy']);
		
		//过滤条件  filterWhere 过滤空值
		$query->filterWhere([
				'username' 	=> $username,
				'email'		=> $email,
		]);
		
		$query->orderBy('id desc')->addOrderBy('name ASC');
		
		$query->groupBy('id, status')->addGroupBy('age');
		
		$query->having(['status' => 1])->addHaving(['>', 'age', 30]);
		
		$query->limit(10)->offset(20);
		
		$query->join('LEFT JOIN', 'post', 'post.user_id = user.id');
		
		$query->leftJoin('post', 'post.user_id = user.id');
		
		//union()
		$select1 = (new \yii\db\Query())
		->select('id, category_id as type, name')
		->from('post')
		->limit(10);
		$select2 = (new \yii\db\Query())
		->select('id, type, name')
		->from('user')
		->limit(10);
		
		$select1->union($select2);
		
		//查看SQL 语句
		$command = (new \yii\db\Query())
		->select(['id', 'email'])
		->from('user')
		->where(['last_name' => 'Smith'])
		->limit(10)
		->createCommand();
		
		echo $command->sql;
		print_r($command->params);
		$rows = $command->queryAll();
		
		//索引查询结果
		$indexBy = (new \yii\db\Query())
		->from('user')
		->limit(10)
		->indexBy('id')
		->all();
		
		$indexBy1 = (new \yii\db\Query())
		->from('user')
		->indexBy(function ($row){
			return $row['id'] . $row['username'];
		})->all();
		
		//批处理查询
		$querys = (new Query())->from('user')->orderBy('id');
		foreach ($querys->batch() as $users){
			
		}
		//or
		foreach ($querys->each() as $user){
			
		}
		
	}
	
	public function actionAR(){
		
		//查询数据
		$customers = Customer::find()
		->where(['status' => Customer::STATUS_ACTIVE])
		->orderBy('id')
		->all();
		
		$customer = Customer::find()->where(['id' => 1])->one();
		
		$count = Customer::find()->where(['status' => Customer::STATUS_ACTIVE])->count();
		
		$customer1 = Customer::find()->indexBy('id')->all();
		
		$sql = "SELECT * FROM customer";
		$customer2 = Customer::findBySql($sql)->all();
		
		$customer3 = Customer::findOne(1);
		
		$customer4 = Customer::findOne(['id'=>1, 'status' => Customer::STATUS_ACTIVE,]);
		
		$customer5 = Customer::findAll([1,2,3]);
		
		$customer6 = Customer::findAll(['status' => Customer::STATUS_ACTIVE]);
		
		//以数组形式获取数据
		$customer7 = Customer::find()->asArray()->all();
		
		//批量获取数据
		foreach (Customer::find()->batch(10) as $customer8){ //一次提取10个客户信息
			
		}
		
		foreach (Customer::find()->each(10) as $customer9){  //一次提取10个客户并一个一个的遍历处理
			
		}
		
		foreach (Customer::find()->with('orders')->each() as $customer10){  //贪婪加载模式的批处理查询
			
		}
		
		//操作数据
		$customerModel = new Customer();
		$customerModel->name = 'Cindy';
		$customerModel->email = 'cindy@163.com';
		$customerModel->save();
		
		$customer11 = Customer::findOne($id);
		$customer11->email = 'baobao@163.com';
		$customer11->save();
		
		$customer12 = Customer::findOne($id);
		$customer12->delete();
		
		Customer::deleteAll('age > :age AND gender = :gender', [':age' => 20, ':gender' => 'M']);
		
		Customer::updateAllCounters(['age' => 1]);  //所有客户的age（年龄）字段加1：
		
		//数据输入与有效性验证
		$customerModel = new Customer();
		if ($customerModel->load(\Yii::$app->request->post()) && $customerModel->save()){}
		
		if ($customerModel === null){
			throw new NotFoundHttpException();
		}
		
		//读取默认值
		$customerModel->loadDefaultValues();
		
		//逆关系
	    \yii\db\ActiveQuery::inverseOf();
	    
	    //join类型关联查询
		$orders = Orders::find()->joinWith('customer')->orderBy('customer.id, order.id')->all();
		
		$orders1 = Orders::find()->innerJoinWith('books')->all();
	}
	
	public function actionDisplaydata()
	{
		$formatter = \Yii::$app->formatter;
		echo $formatter->asDate('2017-06-28', 'long');
		echo '<br>';
		echo $formatter->asPercent(0.125, 2);
		echo '<br>';
		echo $formatter->asEmail('ting.wang@maimob.cn');
		echo '<br>';
		echo $formatter->asBoolean(true);
		echo '<br>';
		echo $formatter->asDate(null);
		echo '<br>';
		echo \Yii::$app->formatter->format('2014-01-10', 'date');
		echo '<br>';
		echo \Yii::$app->formatter->asDate('now', 'yyyy-MM-dd');
		echo '<br>';
		echo \Yii::$app->formatter->asDate('now', 'php:Y-m-d');
		echo '<br>';
		echo \Yii::$app->formatter->asDatetime('now');
		echo '<br>';
		echo \Yii::$app->formatter->asTime('now');  //时间显示有问题
		echo '<br>';
		echo \Yii::$app->formatter->asTimestamp('now');
		echo '<br>';
		echo \Yii::$app->formatter->asTime('1498641745');
		echo '<br>';
		echo \Yii::$app->formatter->asTime('2014-10-04 14:41:23 CEST');
		echo '<br>';
		//本地日期格式化(没看出有什么区别)
		\Yii::$app->formatter->locale = 'en-US';
		echo \Yii::$app->formatter->asDatetime('now');
		echo '<br>';
		\Yii::$app->formatter->locale = 'de-DE';
		echo \Yii::$app->formatter->asDatetime('now');
		echo '<br>';
		\Yii::$app->formatter->locale = 'ru-RU';
		echo \Yii::$app->formatter->asDatetime('now');
		echo '<br>';
		echo \Yii::$app->formatter->asDate('now');
	}
	
	public function actionSpecial()
	{
		echo \Yii::t('app', 'Today is {0, date}', time());
		echo '<br/>';
		echo \Yii::t('app', 'Today is {0,date,short}', time());
		echo '<br>';
		echo \Yii::t('app', 'It is {0, time}',time());
	}
	
	public function actionSendemail()
	{
		$email = \Yii::$app->mailer->compose('email')
		->setFrom('ting.wang@maimob.cn')
		->setTo('592557247@qq.com')
		->setSubject('Message Subject')
		->send();
		if ($email){
			return 'success';
		}else {
			return 'fail';
		}
	}
	
	
}







