<div id="confirmDiv"></div>
<div class='gridview-control-line'>
	<?php
		echo CHtml::beginForm($this->createUrl($url), 'post', array('id'=>'itemsSelected-form'));
	?>
	<img alt="" src="<?php echo Yii::app()->request->baseUrl; ?>/images/arrow_ltr.png"/>
	<?php
		echo Yii::t('common', 'With selected').': ';
		echo CHtml::DropDownList('workWithItemsSelected', $model->WorkItemsSelected, $options).' ';

		echo CHtml::button(
			Yii::t('common', 'Do'),
			array(
				'class' => 'btn btn-primary',
				'onclick' => "
					$(\"#confirmDiv\").confirmModal({
						heading: '".tc('Request for confirmation')."',
						body: '".tc('You are sure?')."',
						confirmButton: '".tc('Yes')."',
						closeButton: '".tc('Cancel')."',
						callback: function () {
							$('#itemsSelected-form input[name=\"itemsSelected[]\"]').remove();
							$('#".$id." input[name=\"itemsSelected[]\"]:checked').each(function(){
								$('#itemsSelected-form').append('<input type=\"hidden\" name=\"itemsSelected[]\" value=\"' + $(this).val() + '\" />');
							});
							$.ajax({
								type: 'post',
								url: '".$this->createUrl($url)."',
								data: $('#itemsSelected-form').serialize(),
								success: function (html) {
									$.fn.yiiGridView.update('".$id."');
								},
							});
						}
					});	
					return false;
				",
			)
		);
	echo CHtml::endForm(); ?>
</div>