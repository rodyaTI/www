<?php
$this->breadcrumbs=array(
	tt('Manage apartments'),
);

$this->menu = array(
	array('label'=>tt('Add apartment'), 'url'=>array('create')),
);
$this->adminTitle = tt('Manage apartments');

if(Yii::app()->user->hasFlash('mesIecsv')){
	echo "<div class='flash-success'>".Yii::app()->user->getFlash('mesIecsv')."</div>";
}

if (param('useUserads', 1)) {
	Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/jquery.jeditable.js', CClientScript::POS_END);
	Yii::app()->clientScript->registerScript('editable_select', "
		function ajaxSetModerationStatus(elem, id, id_elem, owner_id, items){
			$('#editable_select-'+id_elem).editable('".Yii::app()->controller->createUrl("activate")."', {
				data   : items,
				type   : 'select',
				cancel : 'Отмена',
				submit : 'Ок',
				style  : 'inherit',
				submitdata : function() {
					return {id : id_elem};
				}
			});
		}
	",
	CClientScript::POS_HEAD);

}

$this->widget('CustomGridView', array(
	'id'=>'apartments-grid',
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
			'name' => 'id',
			'htmlOptions' => array(
				'class'=>'apartments_id_column',
			),
			'sortable' => false,
		),
		array(
			'name' => 'active',
			'type' => 'raw',
			'value' => 'Yii::app()->controller->returnControllerStatusHtml($data, "apartments-grid", 1)',
			'htmlOptions' => array(
				//'style' => 'width: 150px;',
				'class'=>'apartments_status_column',
			),
			'sortable' => false,
			'filter' => Apartment::getModerationStatusArray(),
		),
		array(
			'name' => 'owner_active',
			'type' => 'raw',
			'value' => 'Apartment::getApartmentsStatus($data->owner_active)',
			'htmlOptions' => array(
				'class'=>'apartments_status_column',
			),
			'sortable' => false,
			'filter' => Apartment::getApartmentsStatusArray(),
		),
        array(
			'name' => 'type',
			'type' => 'raw',
			'value' => 'Apartment::getNameByType($data->type)',
	        'htmlOptions' => array(
		        'style' => 'width: 100px;',
	        ),
			'filter' => Apartment::getTypesArray(),//CHtml::dropDownList('Apartment[type_filter]', $currentType, Apartment::getTypesArray(true)),
			'sortable' => false,
        ),
		array(
			'name' => 'city_id',
			'value' => '$data->city_id ? $data->city->name : ""',
			'htmlOptions' => array(
				'style' => 'width: 150px;',
			),
			'sortable' => false,
			'filter' => ApartmentCity::getAllCity(),
		),
		array(
			'name' => 'ownerEmail',
			'htmlOptions' => array(
				'style' => 'width: 150px;',
			),
			'type' => 'raw',
			'value' => '($data->user->id != 1) ? CHtml::link(CHtml::encode($data->user->email),array("/users/backend/main/view","id" => $data->user->id))  : tt("administrator", "common")',
		),

		array(
			'header' => tc('Title'),
			'name' => 'title_'.Yii::app()->language,
			'type' => 'raw',
			'value' => 'CHtml::link(CHtml::encode($data->{"title_".Yii::app()->language}),array("/apartments/backend/main/view","id" => $data->id))',
			'sortable' => false,
		),
		array(
			//'class'=>'CButtonColumn',
			'class'=>'bootstrap.widgets.BootButtonColumn',

			'template'=>'{up}{down}{view}{update}{delete}',
			'deleteConfirmation' => tt('Are you sure you want to delete this apartment?'),
			'viewButtonUrl' => "Yii::app()->createUrl('/apartments/backend/main/view', array('id' => \$data->id))",
			'htmlOptions' => array('class'=>'infopages_buttons_column'),
			'buttons' => array(
				'up' => array(
					'label' => tc('Move item up'),
					'imageUrl' => $url = Yii::app()->assetManager->publish(
						Yii::getPathOfAlias('zii.widgets.assets.gridview').'/up.gif'
					),
					'url'=>'Yii::app()->createUrl("/apartments/backend/main/move", array("id"=>$data->id, "direction" => "down", "catid" => "0"))',
					'options' => array('class'=>'infopages_arrow_image_up'),

					'visible' => '$data->sorter < "'.$maxSorter.'"',
					'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'apartments-grid'); return false;}",
				),
				'down' => array(
					'label' => tc('Move item down'),
					'imageUrl' => $url = Yii::app()->assetManager->publish(
						Yii::getPathOfAlias('zii.widgets.assets.gridview').'/down.gif'
					),
					'url'=>'Yii::app()->createUrl("/apartments/backend/main/move", array("id"=>$data->id, "direction" => "up", "catid" => "0"))',
					'options' => array('class'=>'infopages_arrow_image_down'),
					'visible' => '$data->sorter > 1',
					'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'apartments-grid'); return false;}",
				),
			),
		),
	),
));

$this->renderPartial('//site/admin-select-items', array(
	'url' => '/apartments/backend/main/itemsSelected',
	'id' => 'apartments-grid',
	'model' => $model,
	'options' => array(
		'activate' => Yii::t('common', 'Activate'),
		'deactivate' => Yii::t('common', 'Deactivate'),
		'delete' => Yii::t('common', 'Delete')
	),
));
?>
