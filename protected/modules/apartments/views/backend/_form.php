<?php
Yii::app()->clientScript->registerCssFile( Yii::app()->clientScript->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css' );

$urls = array(
	Apartment::TYPE_RENT => $this->createUrl('/apartments/backend/main/'.$this->action->id,
		array('id' => $model->isNewRecord? '': $model->id, 'type' => Apartment::TYPE_RENT)),
	Apartment::TYPE_SALE => $this->createUrl('/apartments/backend/main/'.$this->action->id,
			array('id' => $model->isNewRecord? '': $model->id, 'type' => Apartment::TYPE_SALE)),
);

Yii::app()->clientScript->registerScript('redirectType', "
    $(document).ready(function() {
		$('#ap_type').live('change', function() {
			var types = ".CJavaScript::encode($urls).";
		    var type = $('#ap_type :selected').val();
		    location.href=types[type];
        });
    });
	",
    CClientScript::POS_HEAD);
?>

<div class="form">

<?php
	$ajaxValidation = false;
	if(!$model->isNewRecord){
		$htmlOptions = array('enctype' => 'multipart/form-data');
	}
	else{
        $htmlOptions = array();
	}

    /** @var $form BootActiveForm */
	$form = $this->beginWidget('CustomForm', array(
		'id'=>$this->modelName.'-form',
		'enableAjaxValidation'=>$ajaxValidation,
		'htmlOptions'=> $htmlOptions,
	));
	?>

    <?php if(!$model->isNewRecord){ ?>
        <p>
            <strong><?php echo tt('Apartment ID', 'apartments'); ?></strong>: <?php echo $model->id; ?>
        </p>
    <?php } ?>

    <p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->labelEx($model,'active'); ?>
	<?php echo $form->dropDownList($model, 'active', array(
		'1' => tt('Active', 'apartments'),
		'0' => tt('Inactive', 'apartments'),
	), array('class' => 'width150')); ?>
	<?php echo $form->error($model,'active'); ?>

	<?php
	$this->renderPartial('__form', array(
		'form' => $form,
		'model' => $model,
		'categories' => $categories,
	));
	?>
	<?php $this->endWidget(); ?><!-- form -->


	<div class="clear">&nbsp;</div>
	<?php if(!$model->isNewRecord){ ?>
	<div id="photo-gallery">
	<?php
		$this->widget('application.modules.gallery.FBGallery', array(
				'pid' => $model->id,
				'userType' => 'admin',
			));
	?>
	</div>
	<?php } ?>


	<div class="clear">&nbsp;</div>
	<?php if(!$model->isNewRecord){
		if (param('useGoogleMap', 1)){?>
			<div id="gmap">
				<?php echo $this->actionGmap($model->id, $model); ?>
			</div>
	<?php } elseif (param('useYandexMap', 1)) { ?>
			<div id="ymap">
				<?php echo $this->actionYmap($model->id, $model); ?>
			</div>
	<?php }
	}?>

</div>

<?php
	Yii::app()->clientScript->registerScript('show-special', '
		//special-calendar
		if(!$("#Apartment_is_special_offer").is(":checked")){
			$(".special-calendar").hide();
		}
		$("#Apartment_is_special_offer").bind("change", function(){
			if($(this).is(":checked")){
				$(".special-calendar").show();
			} else {
				$(".special-calendar").hide();
			}
		});
	', CClientScript::POS_READY);
?>