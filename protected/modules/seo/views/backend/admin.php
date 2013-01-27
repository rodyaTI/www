<?php
    $this->pageTitle=Yii::app()->name . ' - ' . tt('Manage SEO settings');
    $this->adminTitle = tt('Manage SEO settings');

	$this->menu = array(
		array(),
	);
?>

<?php
    $this->widget('CustomGridView', array(
        'dataProvider'=>$model->search(),
        'filter'=>$model,
        'afterAjaxUpdate' => 'function(){$("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove();}',
        'columns'=>array(
            array(
                'header' => tt('Name'),
                'type'=>'raw',
                'value'=>'CHtml::encode(tt($data->name))',
            ),
            array(
                'header' => tt('Value'),
                'name'=>'value_'.Yii::app()->language,
                'type'=>'raw',
                'value'=>'CHtml::encode($data->value)',
            ),
            array(
                //'class'=>'CButtonColumn',
                'class'=>'bootstrap.widgets.BootButtonColumn',
                'template' => '{update}',
            ),
        ),
    ));
?>
