<div class="form">
    <?php
        $form=$this->beginWidget('CustomForm', array(
            'id'=>$this->modelName.'-form',
            'enableAjaxValidation'=>false,
        ));
        $model->password = '';
        $model->password_repeat = '';
	?>

	<p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary($model); ?>

	<div class="rowold">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username',array('size'=>20,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div class="rowold">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>20,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>

	<div class="rowold">
		<?php echo $form->labelEx($model,'phone'); ?>
		<?php echo $form->textField($model,'phone',array('size'=>20,'maxlength'=>15)); ?>
		<?php echo $form->error($model,'phone'); ?>
	</div>
	
	<div class="clear">&nbsp;</div>
	<?php 
		$this->widget('application.modules.lang.components.langFieldWidget', array(
				'model' => $model,
				'field' => 'additional_info',
				'type' => 'text'
			));
		?>
	<div class="clear">&nbsp;</div>
	<?php
		if($model->isNewRecord){
	?>
	<div class="rowold">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password',array('size'=>20,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>

	<div class="rowold">
		<?php echo $form->labelEx($model,'password_repeat'); ?>
		<?php echo $form->passwordField($model,'password_repeat',array('size'=>20,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'password_repeat'); ?>
	</div>
	<?php
		}
	?>

	<div class="rowold buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->