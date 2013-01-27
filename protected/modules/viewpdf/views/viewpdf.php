<div>
	<img src="<?php echo Yii::app()->getBaseUrl(true); ?>/images/pages/logo-open-re.png" />

	<h1>
		<?php echo $model->getStrByLang('title'); ?>
	</h1>

	<div>
		<div>
			<?php
			if ($model->is_special_offer) {
				?>
				<div>
					<?php
					echo '<h2>' . Yii::t('common', 'Special offer!') . '</h2>';

					if ($model->is_free_from != '0000-00-00' && $model->is_free_to != '0000-00-00') {
						echo '<p>';
						echo '<strong>'.Yii::t('common', 'Is avaliable').'</strong>';
						if ($model->is_free_from != '0000-00-00') {
							echo ' ' . Yii::t('common', 'from');
							echo ' ' . Booking::getDate($model->is_free_from);
						}
						if ($model->is_free_to != '0000-00-00') {
							echo ' ' . Yii::t('common', 'to');
							echo ' ' . Booking::getDate($model->is_free_to);
						}
						echo '</p>';
					}
					?>
				</div>
				<?php
			}
			?>
			<br />
			<?php echo '<strong>'.tt('Type', 'apartments').'</strong>: '.Apartment::getNameByType($model->type); ?>
			<div></div>
			<?php
			$img = $model->getMainThumb();
				echo '<table cellpadding="0" cellspacing="0" border="0"><tr>';
					echo '<td>';
						if ($img) {
							echo '<img src="' . Yii::app()->getBaseUrl(true)  . '/uploads/apartments/' . $model->id . '/bigthumb/' . $img . '"
										alt="' . $model->getStrByLang('title') . '"
										title="' . $model->getStrByLang('title') . '" />';
						}
						else {
							echo '<img src="'.Yii::app()->getBaseUrl(true) .'/images/default/no_photo_bigthumb.png"
										alt="'.$model->getStrByLang('title').'"
										title="'.$model->getStrByLang('title').'" />';
						}
					echo '</td>';
					echo '<td align="right">';
						$this->widget('application.extensions.qrcode.QRCodeGenerator', array(
							'data' => Yii::app()->controller->createAbsoluteUrl('/apartments/main/view', array(
								'id' => $model->id)
							),
							'subfolderVar' => false,
							'matrixPointSize' => 3,
							'fileUrl'=> Yii::app()->getBaseUrl(true) . '/uploads',
						));
					echo '</td>';
				echo '</tr></table>';
			?>
			<div></div>
			<div>
				<?php if(param('useShowUserInfo')) {
					if (isset($model->user->phone) && $model->user->phone) {
						echo '<strong>'.tt('Owner phone', 'apartments').'</strong>: '.CHtml::encode($model->user->phone);
						echo '<div></div>';
					}
					$additionalInfo = 'additional_info_'.Yii::app()->language;
					if (isset($model->user->$additionalInfo) && !empty($model->user->$additionalInfo)) {
						echo '<strong>'.tc('Owner additional info').'</strong>: '.CHtml::encode($model->user->$additionalInfo);
						echo '<div></div>';
					}
				} ?>
				<?php
					echo '<strong>'.tt('Apartment ID', 'apartments') . '</strong>: ' . $model->id;
					echo '<div></div>';

					echo '<strong>';
						echo utf8_ucfirst($model->objType->name) . ' ' . Yii::t('module_apartments', 'type_view_'.$model->type);
                        if ($model->num_of_rooms){
							echo ',&nbsp;';
							echo Yii::t('module_apartments',
								'{n} bedroom|{n} bedrooms|{n} bedrooms', array($model->num_of_rooms));
						}
						if(isset($model->city) && isset($model->city->name)){
							echo ',&nbsp;';
							echo 'г.'.$model->city->name;
						}
					echo '</strong>';
				?>
				<?php
				if ($model->floor || $model->floor_total || $model->square || $model->berths || ($model->windowTo && $model->windowTo->getTitle())) {
					echo '<div></div>';
					$echo = array();
					if ($model->floor && $model->floor_total) {
						$echo[] = Yii::t('module_apartments', '{n} floor of {total} total', array($model->floor, '{total}' => $model->floor_total));
					} else {
						if($model->floor){
							$echo[] = $model->floor.' '.tc('Floor');
						}
						if($model->floor_total){
							$echo[] = tt('Total number of floors', 'apartments').': '.$model->floor_total;
						}
					}

					if ($model->square) {
						$echo[] = Yii::t('module_apartments', 'total square: {n} m<sup>2</sup>', $model->square);
					}
					if ($model->berths) {
						$echo[] = Yii::t('module_apartments', 'berths') . ': ' . CHtml::encode($model->berths);
					}
					if ($model->windowTo && $model->windowTo->getTitle()) {
						$echo[] = tt('window to', 'apartments') . ' ' . CHtml::encode($model->windowTo->getTitle());
					}
					echo implode(', ', $echo);
					unset($echo);
				}
				?>
				<div></div>
				<?php echo '<strong>'.tt('Price from', 'apartments') . '</strong>: ' . $model->getPrettyPrice(); ?>
			</div>
			<?php
			$imgsOrder = '';
			if($model->images){
				$imgsOrder = unserialize($model->images->imgsOrder);
			}
			if (is_array($imgsOrder)) {
				$countArr = count($imgsOrder);
				$i = 1;

				if ($countArr) {
					echo '<div><hr /><div></div></div><div>';
					echo '<table cellpadding="0" cellspacing="0" border="0" style="width:360px;">';
						foreach ($imgsOrder as $key => $value) {
							$index = $i % 4;
							$k = $i + 1;
							$indexNext = ($i + 1) % 4;
							;

							if ($index == 0 || $i == 1) {
								echo '<tr>';
							}
							echo '<td align="left" style="width:120px; height: 100px;"><img src="' . Yii::app()->getBaseUrl(true) . '/uploads/apartments/' . $model->id . '/thumbs/' . $key . '" alt="" title="" /></td>';
							if ($indexNext == 0 || $countArr == $i) {
								echo '</tr>';
							}

							$i++;
						}
					echo '</table>';
					echo '<hr /></div>';
				}
			}
			?>
			<div>
				<?php
				if ($model->getStrByLang('description')) {
					echo '<div></div>';
					echo '<strong>' . tt('Description', 'apartments') . '</strong>: ' . CHtml::encode($model->getStrByLang('description'));
				}

				if ($model->getStrByLang('description_near')) {
					echo '<div></div>';
					echo '<strong>' . tt('Near', 'apartments') . '</strong>: ' . CHtml::encode($model->getStrByLang('description_near'));
				}

				if ($model->getStrByLang('address')) {
					$adressFull = '';
					if(isset($model->city) && isset($model->city->name)){
						$cityName = $model->city->name;
						if($cityName) {
							$adressFull = 'г. '.$cityName;
						}
					}
					$adress = CHtml::encode($model->getStrByLang('address'));
					if($adress){
						$adressFull .= ', '.$adress;
					}
					if($adressFull){
						echo '<p><strong>'.tt('Address', 'apartments').':</strong> '.$adressFull.'</p>';
					}
				}
				?>
			</div>
			<div>
				<hr />
			</div>
			<div>
				<?php
				$prev = '';
				$column1 = 0;
				$column2 = 0;
				$column3 = 0;
				foreach ($model->getFullInformation($model->id, $model->type) as $item) {
					if ($item['title']) {
						if ($prev != $item['style']) {
							$column2 = 0;
							$column3 = 0;
							echo '<div></div>';
						}
						$$item['style']++;
						$prev = $item['style'];
						echo '<div>';
						echo '<span><strong>' . CHtml::encode($item['title']) . '</strong></span>';
						echo '<ul>';
						foreach ($item['values'] as $value) {
							if ($value) {
								echo '<li><span>' . CHtml::encode($value) . '</span></li>';
							}
						}
						echo '</ul>';
						echo '</div>';
						if (($item['style'] == 'column2' && $column2 == 2) || $item['style'] == 'column3' && $column3 == 3) {
							echo '<div></div>';
						}
					}
				}
				?>
			</div>
		</div>
	</div>
	<div>
		<p>&copy;&nbsp;<?php echo CHtml::encode(Yii::app()->name).', '.date('Y'); ?></p>
		<p style="text-align:center;">Powered by <a href="http://monoray.ru/products/6-open-real-estate">Open Real Estate</a></p>
	</div>
</div>
