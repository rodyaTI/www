<?php
$this->breadcrumbs=array(
	tt('Manage of the top menu') => array('admin'),
);

$this->menu=array(

	array('label' => tt('Manage of the top menu'), 'url'=>array('admin')),
	array('label'=>tt('Add item'), 'url'=>array('create')),

	/*array('label'=>tt('Add station'), 'url'=>array('create')),*/
	array('label'=> tt('Delete item'), 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=> tt('Are you sure you want to delete this item?'))),
);
$this->adminTitle = tt('Update').': '.$model->title;
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>