<?php
$this->breadcrumbs=array(
	Yii::t('common', 'User managment'),
);

$this->menu=array(
	array('label'=>tt('Add user'), 'url'=>array('/users/backend/main/create')),
);

$this->adminTitle = Yii::t('common', 'User managment');

$this->widget('CustomGridView', array(
	'id'=>'user-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'afterAjaxUpdate' => 'function(){$("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove();}',
	'columns'=>array(
		array(
			'name' => 'username',
			'header' => tt('User name'),
		),
		array(
			'name' => 'active',
			'header' => tt('Status'),
			'type' => 'raw',
			'value' => 'Yii::app()->controller->returnStatusHtml($data, "user-grid", 1, 1)',
			'headerHtmlOptions' => array(
				'class'=>'user_status_column',
			),
			'filter' => array(0 => tt('Inactive'), 1 => tt('Active')),
		),
		'phone',
		'email',
		array(
			//'class'=>'CButtonColumn',
			'class'=>'bootstrap.widgets.BootButtonColumn',
			'template'=>'{update}{delete}',
			'deleteConfirmation' => tt('Are you sure you want to delete this user?'),
			'buttons' => array(
				'delete' => array(
					'visible' => '$data->id != 1',
				),
			)
		),
	),
)); 
?>
