<?php
$this->pageTitle=Yii::app()->name . ' - ' . ConfigurationModule::t('Manage settings');

$this->breadcrumbs=array(
	ConfigurationModule::t('Settings')=>array('/configuration/backend/main'),
	ConfigurationModule::t('Update {name}', array('{name}'=>$model->title)),
);

$this->adminTitle = ConfigurationModule::t('Update param "{name}"', array('{name}'=>$model->title));

$required = true;
if (in_array($model->name, ConfigurationModel::model()->allowEmpty))
	$required = false;

if($ajax){ ?>
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h3><?php echo $this->adminTitle; ?></h3>
    </div>

    <div class="modal-body">
<?php } ?>

<div class="form">

<?php $form=$this->beginWidget('CustomForm', array(
	'id'=>$this->modelName.'-form',
	'enableAjaxValidation'=>true,
	'htmlOptions'=>array('class'=>'white_noborder')
)); ?>

	<p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

    <input type="hidden" name="config_id" id="config_id" value="<?php echo $model->id; ?>">
	<input type="hidden" id="config_required" value="<?php echo $required ?>">

	<div class="rowold">
		<?php echo CHtml::activeLabel($model, 'value', array('required' => $required));  ?>
		<?php echo $form->textArea($model, 'value', array('class' => 'width450', 'id' => 'config_value')); ?>
		<?php echo $form->error($model, 'value'); ?>
	</div>

<?php if(!$ajax){ ?>
    <div class="rowold buttons">
           <?php $this->widget('bootstrap.widgets.BootButton',
                       array('buttonType'=>'submit',
                           'type'=>'primary',
                           'icon'=>'ok white',
                           'label'=> tc('Save'),
                       )); ?>
   	</div>
<?php } ?>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php if($ajax){ ?>
    </div> <!-- modal-body -->
<?php } ?>