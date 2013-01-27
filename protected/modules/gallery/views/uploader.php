 	<div id="fbguploader" class="fbguploader">
		<div class="uploaderTitle"><?php echo Yii::t('common', 'Upload images'); ?></div>

		<?php if($max != '-1'):?>
			<div class="maxFiles">Max:</div><div id="limitFiles" class="limitFiles"><?php echo $max ;?></div>
			<hr />
		<?php endif;?>

		<div class="form">
			<?php echo CHtml::beginForm($this->uploaderConfig['action'], 'post', array('enctype'=>'multipart/form-data'));?>   
			<?php $this->widget('CMultiFileUpload', 
						array(
							'name'=>'uploader',
							'max'=>$max,
							'accept'=>$this->uploaderConfig['accept'],
							'duplicate'=>  tc('Duplicate of image'),
							'denied'=>  tc('Incorrect image type'),
							'remove'=>'<img src="'.$this->assetUrl.$this->uploaderConfig['remove'].'" height="16" width="16" alt="x" />',
							'selected'=>'ai ales o poze',
							'htmlOptions'=>array('size'=>'40'),
						)
				);?>
			<div class="rowold">
				<?php if(Yii::app()->user->getState('isAdmin')) {
					$this->widget('bootstrap.widgets.BootButton',
						array('buttonType'=>'submit',
							'type'=>'primary',
							'icon'=>'ok white',
							'label'=> Yii::t('common', $this->uploaderConfig['submit']),
							'htmlOptions'=>array('style'=>'margin-top: 8px;'),
						));
				} else {
					echo CHtml::submitButton(Yii::t('common', $this->uploaderConfig['submit']));
				}?>
			</div>
			<?php echo CHtml::endForm(); ?>
		</div>
	</div>
<div class="clear"></div>


