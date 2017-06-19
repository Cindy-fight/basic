<?php
use app\models\Date;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\base\Widget;
?>

<table><tr><td>
	<?php 
		$model = new Date();
		$form = ActiveForm::begin([
				'action'	=> ['campaign/getalltransactions'],
				'method'	=> 'post',
		]);
	?>
	<?= $form->field($model, 'date_start')->widget(DatePicker::className(), ['dateFormat' => 'yyyy-MM-dd'])?>
	<?php ActiveForm::end();?>
</td></tr></table>