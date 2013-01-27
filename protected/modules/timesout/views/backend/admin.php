<?php

$this->menu=array(
	array('label'=>tt('Add value', 'windowto'), 'url'=>array('create')),
);

$this->adminTitle = tt('Manage reference', 'windowto');

$this->widget('CustomGridView', array(
	'id'=>'timesout-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'afterAjaxUpdate' => 'function(){$("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove();}',
	'columns'=>array(
		array(
            'class'=>'CCheckBoxColumn',
            'id'=>'itemsSelected',
            'selectableRows' => '2',
            'htmlOptions' => array(
                'class'=>'center',
            ),
        ),
		array(
			'header' => tc('Title'),
			'name' => 'title_'.Yii::app()->language,
		),
		array(
			'class'=>'bootstrap.widgets.BootButtonColumn',
			'deleteConfirmation' => tt('Are you sure you want to delete this value?', 'windowto'),
			//'class'=>'CButtonColumn',
			'template'=>'{update}{delete}',
		),
	),
));

$this->renderPartial('//site/admin-select-items', array(
	'url' => '/timesout/backend/main/itemsSelected',
	'id' => 'timesout-grid',
	'model' => $model,
	'options' => array(
		'delete' => Yii::t('common', 'Delete')
	),
));
?>
