<h1><?php echo tt('Update apartment', 'apartments'); ?></h1>
<?php
if(!Yii::app()->user->isGuest){
    $menuItems = array(
    		array('label' => tt('Manage apartments', 'apartments'), 'url'=>array('index')),
    		array('label' => tt('Add apartment', 'apartments'), 'url'=>array('create')),
    		array(
    			'label' => tt('Delete apartment', 'apartments'),
    			'url'=>'#',
    			'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>tt('Are you sure you want to delete this apartment?', 'apartments'))
    ));
} else {
    $menuItems = array();
}

$this->widget('zii.widgets.CMenu', array(
	'items' => $menuItems
));

if(isset($show) && $show){
	Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/scrollto.js', CClientScript::POS_END);
	Yii::app()->clientScript->registerScript('scroll-to','
			scrollto("'.CHtml::encode($show).'");
		',CClientScript::POS_READY
	);
}

$this->renderPartial('_form',array(
		'model'=>$model,
		'categories' => $categories,
));

