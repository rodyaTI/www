<?php
$this->breadcrumbs=array(
	tc('User managment') => array('admin'),
	tt('Add user'),
);
$this->menu = array(
	array('label'=>tc('User managment'), 'url'=>array('admin')),
);
$this->adminTitle = tt('Add user');
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>