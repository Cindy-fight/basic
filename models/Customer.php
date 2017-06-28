<?php

namespace app\models;

use \yii\db\ActiveRecord;

class Customer extends \yii\db\ActiveRecord
{
	const STATUS_ACTIVE = 1;
	
	public static function tableName()
	{
		return 'customer';
	}
	
	public function getOrders()
	{
		//关联建立一对多关系
		return $this->hasMany(Order::className(), ['customer_id' => 'id'])->inverseOf('customer');
	}
	
	public function getBigOrders($threshold = 100){
		return $this->hasMany(Order::className(), ['customer_id' => 'id'])
		->where('subtotal > :threshold', [':threshold' => $threshold])
		->orderBy('id');
	}
}