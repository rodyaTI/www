<?php
$this->breadcrumbs=array(
	//Yii::t('common', 'objects') => array('/site/viewobjects'),
	tt('Manage apartment object types')
);

$this->menu=array(
	array('label'=>tt('Add object type'), 'url'=>array('/apartmentObjType/backend/main/create')),
);

$this->adminTitle = tt('Manage apartment object types');
?>

<?php $this->widget('CustomGridView', array(
	'id'=>'object-categories-grid',
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
            'name' => 'name_'.Yii::app()->language,
			'sortable' => false,
			//'filter' => false,
		),
		array(
			//'class'=>'CButtonColumn',
			'class'=>'bootstrap.widgets.BootButtonColumn',
			'template'=>'{up}{down}{update}{delete}',
			'deleteConfirmation' => tc('Are you sure you want to delete this item?'),
			'htmlOptions' => array('class'=>'infopages_buttons_column'),
			'afterDelete'=>'function(link, success, data){ if (data == 0) {alert("'.tt('backend_apartmentObjType_main_admin_NoDeleteLastElement').'"); } }',
			'buttons' => array(
				'up' => array(
					'label' => tc('Move item up'),
					'imageUrl' => $url = Yii::app()->assetManager->publish(
						Yii::getPathOfAlias('zii.widgets.assets.gridview').'/up.gif'
					),
					'url'=>'Yii::app()->createUrl("/apartmentObjType/backend/main/move", array("id"=>$data->id, "direction" => "up"))',
					'options' => array('class'=>'infopages_arrow_image_up'),
					'visible' => '$data->sorter > 1',
					'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'object-categories-grid'); return false;}",
				),
				'down' => array(
					'label' => tc('Move item down'),
					'imageUrl' => $url = Yii::app()->assetManager->publish(
						Yii::getPathOfAlias('zii.widgets.assets.gridview').'/down.gif'
					),
					'url'=>'Yii::app()->createUrl("/apartmentObjType/backend/main/move", array("id"=>$data->id, "direction" => "down"))',
					'options' => array('class'=>'infopages_arrow_image_down'),
					'visible' => '$data->sorter < "'.$maxSorter.'"',
					'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'object-categories-grid'); return false;}",
				),
			),
		),
	),
));

$this->renderPartial('//site/admin-select-items', array(
	'url' => '/apartmentObjType/backend/main/itemsSelected',
	'id' => 'object-categories-grid',
	'model' => $model,
	'options' => array(
		'delete' => Yii::t('common', 'Delete')
	),
));
?>
