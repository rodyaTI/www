<?php
$this->breadcrumbs=array(
	Yii::t('common', 'User managment') => array('admin'),
	$model->email.($model->username != '' ? ' ('.$model->username.')' : ''),
);

$this->menu=array(
	/*array('label'=>Yii::t('common', 'User managment'), 'url'=>array('admin')),
	array('label'=>tt('Add user'), 'url'=>array('create')),
	array('label'=>tt('Edit user'), 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>tt('Delete user'), 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),
		'confirm'=>tt('Are you sure you want to delete this user?'))),*/
	array('label'=>tt('Add user'), 'url'=>array('/users/backend/main/create')),
);
$model->scenario = 'backend';

$this->adminTitle = $model->email.($model->username != '' ? ' ('.$model->username.')' : '');
?>

<div class="view" id="user-list">
	<dl>
		<dt>
			<?php echo CHtml::encode($model->getAttributeLabel('username')); ?>:
		</dt>
		<dd>
			<?php echo CHtml::encode($model->username); ?>
		</dd>
	</dl>
	<dl>
		<dt>
			<?php echo CHtml::encode($model->getAttributeLabel('email')); ?>:
		</dt>
		<dd>
			<?php echo CHtml::encode($model->email); ?>
		</dd>
	</dl>
	<dl>
		<dt>
			<?php echo CHtml::encode($model->getAttributeLabel('phone')); ?>:
		</dt>
		<dd>
			<?php echo CHtml::encode($model->phone); ?>
		</dd>
	</dl>
	<dl>
		<dt>
			<?php echo CHtml::encode($model->getAttributeLabel('additional_info')); ?>:
		</dt>
		<dd>
			<?php echo CHtml::encode($model->getAdditionalInfo()); ?>
		</dd>
	</dl>
	<dl>
		<dt>
			<?php echo CHtml::encode(tt('Status')); ?>:
		</dt>
		<dd>
			<?php echo ($model->active) ? tt('Active') : tt('Inactive'); ?>
		</dd>
	</dl>
</div>
