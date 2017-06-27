<?php

namespace app\controllers;

use yii\web\Controller;
use yii\db\Query;

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
	
	
}







