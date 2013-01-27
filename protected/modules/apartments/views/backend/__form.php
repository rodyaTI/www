<?php
if($model->is_free_from == '0000-00-00'){
	$model->is_free_from = '';
}
if($model->is_free_to == '0000-00-00'){
	$model->is_free_to = '';
}
?>

<?php echo $form->errorSummary($model); ?>

<div class="rowold">
	<?php echo $form->labelEx($model,'type'); ?>
	<?php echo $form->dropDownList($model,'type',Apartment::getTypesArray(), array('class' => 'width150', 'id'=>'ap_type')); ?>
	<?php echo $form->error($model,'type'); ?>
</div>

<div class="rowold">
	<?php echo $form->labelEx($model,'city_id'); ?>
	<?php echo $form->dropDownList($model,'city_id',Apartment::getCityArray(), array('class' => 'width150')); ?>
	<?php echo $form->error($model,'city_id'); ?>
</div>

<div class="rowold">
	<?php echo $form->labelEx($model,'obj_type_id'); ?>
	<?php echo $form->dropDownList($model,'obj_type_id', Apartment::getObjTypesArray(), array('class' => 'width150')); ?>
	<?php echo $form->error($model,'obj_type_id'); ?>
</div>

<?php if(Yii::app()->user->getState('isAdmin'))  { ?>
    <div class="rowold">
        <?php echo $form->checkboxRow($model,'is_special_offer'); ?>
    </div>
<?php } ?>

<div class="special-calendar">
	<?php echo $form->labelEx($model,'is_free_from', array('class' => 'noblock')); ?> /
	<?php echo $form->labelEx($model,'is_free_to', array('class' => 'noblock')); ?><br />
	<?php
		$this->widget('application.extensions.FJuiDatePicker', array(
			'model'=>$model,
			'attribute'=>'is_free_from',
			'range' => 'eval_period',
			'language' => Yii::app()->language,

			'options'=>array(
				'showAnim'=>'fold',
				'dateFormat'=>'yy-mm-dd',
				'minDate'=>'new Date()',
			),
			'htmlOptions'=>array(
				'class' => 'width70 eval_period'
			),
		));
	?>
	/
	<?php
		$this->widget('application.extensions.FJuiDatePicker', array(
			'model'=>$model,
			'attribute'=>'is_free_to',
			'range' => 'eval_period',
			'language' => Yii::app()->language,

			'options'=>array(
				'showAnim'=>'fold',
				'dateFormat'=>'yy-mm-dd',
				'minDate'=>'new Date()',
			),
			'htmlOptions'=>array(
				'class' => 'width70 eval_period'
			),
		));
	?>
	<?php echo $form->error($model,'is_free_from'); ?>
	<?php echo $form->error($model,'is_free_to'); ?>
</div>


<?php
if (!isset($element)) { $element = 0; }

if(issetModule('bookingcalendar')) {
	$this->renderPartial('//../modules/bookingcalendar/views/_form', array('apartment' => $model, 'element' => $element));
}
?>

<div class="rowold">
	<?php echo $form->labelEx($model,'price'); ?>
	<?php echo $form->textField($model,'price', array('class' => 'width50')); ?>
	<span><?php echo param('siteCurrency', 'руб.'); ?></span>
	<?php echo $form->dropDownList($model,'price_type', Apartment::getPriceArray($model->type), array('class' => 'width150')); ?>
	<?php echo $form->error($model,'price'); ?>
</div>

<div class="rowold">
	<?php echo $form->labelEx($model,'num_of_rooms'); ?>
	<?php echo $form->dropDownList($model,'num_of_rooms',
			array_merge(
				array(0 => ''),
				range(1, param('moduleApartments_maxRooms', 8))
			), array('class' => 'width50')); ?>
	<?php echo $form->error($model,'num_of_rooms'); ?>
</div>

<div class="clear5"></div>

<div class="rowold">
	<?php echo $form->labelEx($model,'floor', array('class' => 'noblock')); ?> /
	<?php echo $form->labelEx($model,'floor_total', array('class' => 'noblock')); ?><br />
	<?php echo $form->dropDownList($model,'floor',
			array_merge(
				array('0' => ''),
				range(1, param('moduleApartments_maxFloor', 30))
			), array('class' => 'width50')); ?> /
	<?php echo $form->dropDownList($model,'floor_total',
			array_merge(
				array('0' => ''),
				range(1, param('moduleApartments_maxFloor', 30))
			), array('class' => 'width50')); ?>
	<?php echo $form->error($model,'floor'); ?>
	<?php echo $form->error($model,'floor_total'); ?>
</div>

<div class="rowold">
	<?php echo $form->labelEx($model,'square'); ?>
	<?php echo $form->textField($model,'square', array('size' => 10)); ?>
	<?php echo $form->error($model,'square'); ?>
</div>

<div class="rowold">
	<?php echo $form->labelEx($model,'window_to'); ?>
	<?php echo $form->dropDownList($model,'window_to', WindowTo::getWindowTo(), array('class' => 'width150')); ?>
	<?php echo $form->error($model,'window_to'); ?>
</div>

<div class="rowold">
	<?php echo $form->labelEx($model,'berths'); ?>
	<?php echo $form->textField($model,'berths',array('class' => 'width150','maxlength'=>255)); ?>
	<?php echo $form->error($model,'berths'); ?>
</div>

<div class="apartment-description-item">
	<?php
		if($categories){
			$prev = '';
			$column1 = 0;
			$column2 = 0;
			$column3 = 0;

			$count = 0;
			foreach($categories as $catId => $category){
				if(isset($category['values']) && $category['values'] && isset($category['title'])){

					if($prev != $category['style']){
						$column2 = 0;
						$column3 = 0;
						echo '<div class="clear">&nbsp;</div>';
					}
					$$category['style']++;
					$prev = $category['style'];
					echo '<div class="'.$category['style'].'">';
					echo '<span class="viewapartment-subheader">'.$category['title'].'</span>';
					echo '<ul class="no-disk">';
					foreach($category['values'] as $valId => $value){
						if($value){
								$checked = $value['selected'] ? 'checked="checked"' : '';
								echo '<li><input type="checkbox" id="category['.$catId.']['.$valId.']" name="category['.$catId.']['.$valId.']" '.$checked.'/>
									<label for="category['.$catId.']['.$valId.']" />'.$value['title'].'</label></li>';
						}
					}
					echo '</ul>';
					echo '</div>';
					if(($category['style'] == 'column2' && $column2 == 2)||$category['style'] == 'column3' && $column3 == 3){
						echo '<div class="clear"></div>';
					}
				}

			}
		}
	?>
	<div class="clear"></div>
</div>

<?php
$this->widget('application.modules.lang.components.langFieldWidget', array(
		'model' => $model,
		'field' => 'title',
        'type' => 'string'
	));
?>

<div class="clear">&nbsp;</div>

<?php
$this->widget('application.modules.lang.components.langFieldWidget', array(
		'model' => $model,
		'field' => 'description',
        'type' => 'text'
	));
?>

<div class="clear">&nbsp;</div>

<?php
$this->widget('application.modules.lang.components.langFieldWidget', array(
		'model' => $model,
		'field' => 'description_near',
        'type' => 'text'
	));
?>

<div class="clear">&nbsp;</div>

<?php
$this->widget('application.modules.lang.components.langFieldWidget', array(
		'model' => $model,
		'field' => 'address',
        'type' => 'string'
	));
?>
<div class="clear">&nbsp;</div>
<?php if(Yii::app()->user->getState('isAdmin')) {
	$this->widget('bootstrap.widgets.BootButton',
		array('buttonType'=>'submit',
			'type'=>'primary',
			'icon'=>'ok white',
			'label'=> $model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Save'),
			'htmlOptions' => array('onclick' => "$('#Apartment-form').submit(); return false;")
		));
} else {
	echo '<div class="row buttons">';
	echo CHtml::button($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Save'), array(
		'onclick' => "$('#Apartment-form').submit(); return false;", 'class' =>'big_button',
	));
	echo '</div>';
}


