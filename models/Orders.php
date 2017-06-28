<?php

namespace app\models;

use yii\db\ActiveRecord;

class Orders extends ActiveRecord
{
	public function tableName()
	{
		return 'orders';
	}
	
	public function getCustomer()
	{
		return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
	}
}