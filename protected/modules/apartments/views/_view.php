<div class="apartment-description">
	<?php
		if($data->is_special_offer){
			?>
			<div class="big-special-offer">
				<?php
				echo '<h4>'.Yii::t('common', 'Special offer!').'</h4>';

				if($data->is_free_from != '0000-00-00' && $data->is_free_to != '0000-00-00'){
					echo '<p>';
					echo Yii::t('common','Is avaliable');
					if($data->is_free_from != '0000-00-00'){
						echo ' '.Yii::t('common', 'from');
						echo ' '.Booking::getDate($data->is_free_from);

					}
					if($data->is_free_to != '0000-00-00'){
						echo ' '.Yii::t('common', 'to');
						echo ' '.Booking::getDate($data->is_free_to);
					}
					echo '</p>';
				}
				?>
			</div>
			<?php
		}
	?>

		<div class="viewapartment-main-photo">
		<?php
			$img = $data->getMainThumb();
			if($img){
				echo '<img src="'.Yii::app()->baseUrl.'/uploads/apartments/'.$data->id.'/bigthumb/'.$img.'"
							alt="'.$data->getStrByLang('title').'"
							title="'.$data->getStrByLang('title').'" />';
			}
			else {
				echo '<img src="'.Yii::app()->baseUrl.'/images/default/no_photo_bigthumb.png"
							alt="'.$data->getStrByLang('title').'"
							title="'.$data->getStrByLang('title').'" />';
			}
		?>
	</div>

	<div class="viewapartment-description-top">
			<div>
				<strong>
				<?php
					echo utf8_ucfirst($data->objType->name) . ' ' . tt('type_view_'.$data->type);
                    if ($data->num_of_rooms){
						echo ',&nbsp;';
						echo Yii::t('module_apartments',
							'{n} bedroom|{n} bedrooms|{n} bedrooms', array($data->num_of_rooms));
					}
					if(isset($data->city) && isset($data->city->name)){
						echo ',&nbsp;';
						echo 'г.'.$data->city->name;
					}
				?>
				</strong>
			</div>
			<div class="apartment-id">
				<?php echo tt('Apartment ID').': '.$data->id; ?>
			</div>

		<p class="cost padding-bottom10">
			<?php echo tt('Price from').': '.$data->getPrettyPrice(); ?>
		</p>

		<?php
			if($data->floor || $data->floor_total || $data->square || $data->berths || ($data->windowTo && $data->windowTo->getTitle()) ){
				echo '<p>';
				$echo = array();
				if($data->floor && $data->floor_total){
					$echo[] = Yii::t('module_apartments', '{n} floor of {total} total', array($data->floor, '{total}' => $data->floor_total));
				} else {
					if($data->floor){
						$echo[] = $data->floor.' '.tc('Floor');
					}
					if($data->floor_total){
						$echo[] = tt('Total number of floors', 'apartments').': '.$data->floor_total;
					}
				}

				if($data->square){
					$echo[] = Yii::t('module_apartments', 'total square: {n} m<sup>2</sup>', $data->square);
				}
				if($data->berths){
					$echo[] = Yii::t('module_apartments', 'berths').': '.CHtml::encode($data->berths);
				}
				if($data->windowTo && $data->windowTo->getTitle()){
					$echo[] = tt('window to').' '.CHtml::encode($data->windowTo->getTitle());
				}
				echo implode(', ', $echo);
				unset($echo);

				echo '</p>';
			}
		?>

		<?php
			if((!Yii::app()->user->getState('isAdmin')) && ($data->owner_id != Yii::app()->user->getId()) && $data->type == 1){
				echo CHtml::link(tt('Booking'), array('/booking/main/bookingform', 'id' => $data->id), array('class' => 'btnsrch booking-button fancy'));
				// booking-button
			}
		?>
	</div>
	<div class="clear"></div>

	<?php if(param('useShowUserInfo')): ?>
		<?php $userInfo = 0;
		if (isset($data->user->phone) && $data->user->phone) : ?>
			<div>
				<strong><?php echo Yii::t('module_apartments', 'Owner phone')?></strong>:&nbsp;
				<span id="owner-phone">
					<a href="javascript: void(0);" onclick="generatePhone();"><?php echo tt('Show', 'apartments'); ?></a>
				</span>
			</div>
			<?php
				$userInfo = 1;
				Yii::app()->clientScript->registerScript('generate-phone', '
				function generatePhone(){
					$("span#owner-phone").html(\'<img src="'.Yii::app()->controller->createUrl('/apartments/main/generatephone', array('id' => $data->id)).'" style="vertical-align: bottom;"/>\');
				}

			', CClientScript::POS_END);
			?>
		<?php endif; ?>
		<?php
		$additionalInfo = 'additional_info_'.Yii::app()->language;
		if (isset($data->user->$additionalInfo) && !empty($data->user->$additionalInfo)) : ?>
			<div>
				<strong><?php
					$userInfo = 1;
					echo tt('Owner additional info', 'common')?></strong>:&nbsp;<?php echo CHtml::encode($data->user->$additionalInfo);
				?>
			</div>
		<?php endif;
		if($userInfo){
		 	echo '<br/>';
		}

		?>
	<?php endif; ?>



	<div class="apartment-description-item">
		<?php
			if ($data->images) {
				$this->widget('application.modules.gallery.FBGallery', array(
					'images' => $data->images,
					'pid' => $data->id,
					'userType' => $usertype,
				));
			}
		?>
	</div>
	<div class="viewapartment-description">
		<?php

			if($data->getStrByLang('description')){
				echo '<p><strong>'.tt('Description').':</strong> '.CHtml::encode($data->getStrByLang('description')).'</p>';
			}

			if($data->getStrByLang('description_near')){
				echo '<p><strong>'.tt('Near').':</strong> '.CHtml::encode($data->getStrByLang('description_near')).'</p>';
			}

			if($data->getStrByLang('address')){
                $adressFull = '';
                if(isset($data->city) && isset($data->city->name)){
                    $cityName = $data->city->name;
                    if($cityName) {
                        $adressFull = tc('city.').' '.$cityName;
                    }
                }
                $adress = CHtml::encode($data->getStrByLang('address'));
                if($adress){
                    $adressFull .= ', '.$adress;
                }
                if($adressFull){
					echo '<p><strong>'.tt('Address').':</strong> '.$adressFull.'</p>';
				}
			}
		?>

		<?php if(issetModule('bookingcalendar')) :?>
			<?php $this->renderPartial('//../modules/bookingcalendar/views/calendar', array('apartment' => $data)); ?>
		<?php endif; ?>

		<?php
			$prev = '';
			$column1 = 0;
			$column2 = 0;
			$column3 = 0;

			foreach($data->getFullInformation($data->id, $data->type) as $item){
				if($item['title']){
					if($prev != $item['style']){
						$column2 = 0;
						$column3 = 0;
						echo '<div class="clear"></div>';
					}
					$$item['style']++;
					$prev = $item['style'];
					echo '<div class="'.$item['style'].'">';
					echo '<span class="viewapartment-subheader">'.CHtml::encode($item['title']).'</span>';
					echo '<ul class="apartment-description-ul">';
					foreach($item['values'] as $key => $value){
						if($value){
							if (param('useReferenceLinkInView')) {
								echo '<li><span>'.CHtml::link(CHtml::encode($value), $this->createAbsoluteUrl('/service-'.$key)).'</span></li>';
							}
							else {
								echo '<li><span>'.CHtml::encode($value).'</span></li>';
							}
						}
					}
					echo '</ul>';
					echo '</div>';
					if(($item['style'] == 'column2' && $column2 == 2)||$item['style'] == 'column3' && $column3 == 3){
						echo '<div class="clear"></div>';
					}

				}
			}
		?>
		<div class="clear"></div>
	</div>

	<?php
		if(!Yii::app()->user->getState('isAdmin')) {
			if (issetModule('similarads') && param('useSliderSimilarAds') == 1) {
				Yii::import('application.modules.similarads.components.SimilarAdsWidget');
				$ads = new SimilarAdsWidget;
				$ads->viewSimilarAds($data);
			}
		}
	?>

	<?php

	if(($data->lat && $data->lng) || Yii::app()->user->getState('isAdmin')){
		if(param('useGoogleMap', 1)){
			?>
			<div class="row">
				<div class="row" id="gmap">
					<?php echo $this->actionGmap($data->id, $data); ?>
				</div>
			</div>
			<?php
		}
		if(param('useYandexMap', 1)){
			?>
			<div class="row">
				<div class="row" id="ymap">
					<?php echo $this->actionYmap($data->id, $data); ?>
				</div>
			</div>
			<?php
		}
	}
	?>
</div>
<br />
