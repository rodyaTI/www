<div class="form">

<?php $form=$this->beginWidget('CustomForm', array(
	'id'=>'News-form',
	'enableClientValidation'=>false,
)); ?>
	<p class="note">
		<?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?>
	</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="rowold padding-bottom10">
		<?php echo $form->checkBox($model,'is_offline'); ?>
		<?php echo $form->labelEx($model,'is_offline', array('class' => 'noblock')); ?>
		<?php echo $form->error($model,'is_offline'); ?>
	</div>
		
	<div class="rowold">
		<?php echo $form->labelEx($model,'allow_ip'); ?>
		<?php echo '<div class="padding-bottom10"><sub>'.tt("Through_comma").'</sub></div>';?>
		<?php echo $form->textField($model,'allow_ip', array('size' => 100)); ?>
		<?php echo $form->error($model,'allow_ip'); ?>
	</div>
	
    <div class="rowold">
		<?php echo $form->labelEx($model,'page'); ?>
		<?php
			$this->widget('application.modules.editor.EImperaviRedactorWidget',array(
				'model'=>$model,
				'attribute'=>'page',
				'htmlOptions' => array('class' => 'editor_textarea', 'style'=>'width: 940px;'),
				'options'=>array(
					'toolbar'=>'custom', /*original, classic, mini, */
					'lang' => Yii::app()->language,
					'focus' => false,
				),
			));
		?>
		<?php echo $form->error($model,'page'); ?>
	</div>
		
	<div class="rowold buttons">
        <?php $this->widget('bootstrap.widgets.BootButton',
                    array('buttonType'=>'submit',
                        'type'=>'primary',
                        'icon'=>'ok white',
                        'label'=> tc('Save'),
					)
		); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

