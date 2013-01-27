<?php
$addClass = '';

if ($item->is_special_offer) {
    $addClass = 'special_offer_highlight';
} elseif ($item->date_up_search != '0000-00-00 00:00:00'){
    $addClass = 'up_in_search';
}
?>
<div class="appartment_item  <?php echo $addClass; ?>">
    <div class="offer">
        <div class="offer-photo" align="left">
            <?php
                $img = $item->getMainThumb();
                if($img){
                    echo CHtml::link('<img src="'.Yii::app()->baseUrl.'/uploads/apartments/'.$item->id.'/mediumthumbs/'.$img.'"
                                alt="'.$item->getStrByLang('title').'"
                                title="'.$item->getStrByLang('title').'" />',
                        $item->getUrl());
                }
				else {
					echo CHtml::link('<img src="'.Yii::app()->baseUrl.'/images/default/no_photo_mediumthumb.png"
                                alt="'.$item->getStrByLang('title').'"
                                title="'.$item->getStrByLang('title').'" />',
                        $item->getUrl());
				}
            ?>
        </div>
        <div class="offer-text">
            <div class="apartment-title">
					<?php
						if($item->rating && !isset($booking)){
							$title = truncateText($item->getStrByLang('title'), 5);
						}
						else {
							$title = truncateText($item->getStrByLang('title'), 10);
						}
						echo CHtml::link($title,
						$item->getUrl(), array('class' => 'offer'));
					?>
			</div>
            <?php
                if($item->rating && !isset($booking)){
                    echo '<div class="ratingview">';
                    $this->widget('CStarRating',array(
                        'model'=>$item,
                        'attribute' => 'rating',
                        'readOnly'=>true,
                        'id' => 'rating_' . $item->id,
						'name'=>'rating'.$item->id,
                    ));
                    echo '</div>';
                }
            ?>
            <div class="clear"></div>
			<p class="cost"><?php echo $item->getPrettyPrice(); ?></p>
			<?php
				if( $item->floor || $item->floor_total || $item->square || $item->berths){
					echo '<p class="desc">';

					$echo = array();

					if($item->floor && $item->floor_total){
						$echo[] = Yii::t('module_apartments', '{n} floor of {total} total', array($item->floor, '{total}' => $item->floor_total));
					} else {
						if($item->floor){
							$echo[] = $item->floor.' '.tt('floor', 'common');
						}
						if($item->floor_total){
							$echo[] = tt('floors', 'common').': '.$item->floor_total;
						}
					}

					if($item->square){
						$echo[] = '<span class="nobr">'.Yii::t('module_apartments', 'total square: {n} m<sup>2</sup>', $item->square)."</span>";
					}
					if($item->berths){
						$echo[] = '<span class="nobr">'.Yii::t('module_apartments', 'berths').': '.CHtml::encode($item->berths)."</span>";
					}
					echo implode(', ', $echo);
					unset($echo);

					echo '</p>';
				}
            ?>
        </div>
    </div>
</div>