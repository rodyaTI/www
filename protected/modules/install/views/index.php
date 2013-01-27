<?php Yii::app()->clientScript->registerCoreScript( 'jquery.ui' ); ?>
<?php Yii::app()->clientScript->registerCssFile( Yii::app()->request->baseUrl.'/css/ui/jquery-ui-1.8.16.custom.css', 'screen' ); ?>

<?php $this->widget('licenseWidget', array('autoOpen'=>$is_first ? true : false)); ?>

<div>
    <h2><?php echo tFile::getT('module_install', 'Installation in 1 step'); ?></h2>
</div>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'install-form',
	'enableAjaxValidation'=>true,
)); ?>

    <p class="note"><?php echo tFile::getT('module_install', 'Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary($model); ?>

    <div class="install_box">
        <?php echo CHtml::activeCheckBox($model,'agreeLicense'); ?>
        <?php echo CHtml::activeLabel($model,'agreeLicense', array('style'=>'display:inline;')); ?>
        <?php echo $form->error($model,'agreeLicense'); ?>
    </div>

    <div class="install_box">
        <h2><?php echo tFile::getT('module_install', 'Database settings'); ?></h2>
        <div class="row">
            <?php echo $form->labelEx($model,'dbUser'); ?>
            <?php echo $form->textField($model,'dbUser'); ?>
            <?php echo $form->error($model,'dbUser'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model,'dbPass'); ?>
            <?php echo $form->textField($model,'dbPass'); ?>
            <?php echo $form->error($model,'dbPass'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model,'dbHost'); ?>
            <?php echo $form->textField($model,'dbHost'); ?>
            <?php echo $form->error($model,'dbHost'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model,'dbPort'); ?>
            <?php echo $form->textField($model,'dbPort'); ?>
            <?php echo $form->error($model,'dbPort'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model,'dbName'); ?>
            <?php echo $form->textField($model,'dbName'); ?>
            <?php echo $form->error($model,'dbName'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model,'dbPrefix'); ?>
            <?php echo $form->textField($model,'dbPrefix'); ?>
            <?php echo $form->error($model,'dbPrefix'); ?>
        </div>
    </div>

    <div class="install_box">
        <h2><?php echo tFile::getT('module_install', 'Administrator settings'); ?></h2>
        <div class="row">
            <?php echo $form->labelEx($model,'adminEmail'); ?>
            <?php echo $form->textField($model,'adminEmail'); ?>
            <?php echo $form->error($model,'adminEmail'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model,'adminName'); ?>
            <?php echo $form->textField($model,'adminName'); ?>
            <?php echo $form->error($model,'adminName'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model,'adminPass'); ?>
            <?php echo $form->textField($model,'adminPass'); ?>
            <?php echo $form->error($model,'adminPass'); ?>
        </div>
    </div>

	<div class="row buttons">
		<?php echo CHtml::submitButton(tFile::getT('module_install', 'Install')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>