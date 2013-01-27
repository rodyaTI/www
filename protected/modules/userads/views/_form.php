<?php

$urls = array(
	Apartment::TYPE_RENT => $this->createUrl('/userads/main/'.$this->action->id,
		array('id' => $model->isNewRecord? '': $model->id, 'type' => Apartment::TYPE_RENT)),
	Apartment::TYPE_SALE => $this->createUrl('/userads/main/'.$this->action->id,
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
	if(!$model->isNewRecord){
		$htmlOptions = array('enctype' => 'multipart/form-data');
		$ajaxValidation = true;
	}
	else{
		$htmlOptions = array();
		$ajaxValidation = false;
	}

	$form=$this->beginWidget('CActiveForm', array(
		'id'=>'Apartment-form',
		'enableAjaxValidation'=>$ajaxValidation,
		'htmlOptions'=> $htmlOptions,
	));
	
	$this->renderPartial('//../modules/apartments/views/backend/__form',array(
			'model'=>$model,
			'categories' => $categories,
			'form' => $form,
	));
	
	$this->endWidget(); 
	?><!-- form -->
	
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
				<?php echo $this->actionGmap($model->id); ?>
			</div>
	<?php } elseif (param('useYandexMap', 1)) { ?>
			<div id="ymap">
				<?php echo $this->actionYmap($model->id); ?>
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