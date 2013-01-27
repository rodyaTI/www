<div class="<?php echo $divClass; ?>">
	<span class="search"><div class="<?php echo $textClass; ?>"><?php echo tt('Search in section', 'common'); ?>:</div></span>
	<span class="search">
		<?php
			$data = SearchForm::apTypes();

			echo CHtml::dropDownList(
					'apType',
					isset($this->apType) ? CHtml::encode($this->apType) : '',
					$data['propertyType'],
					array('class' => $fieldClass, 'onchange' => 'setCurrencyName(this.value)')
				);

			if (issetModule('selecttoslider') && param('usePriceSlider') == 1) {
				Yii::app()->clientScript->registerScript('set-currency-name', '
					function setCurrencyName(id){
						var currencyName = '.CJavaScript::encode($data['currencyName']).';

						if (propertyType) {
							$.each(propertyType, function(key, value) {
								$("#price-search-"+key).hide();
								$("#price-currency-"+key).html("");
								$("#price-currency-"+key).hide();
							});

							$("#price-search-"+id).show();
							$("#price-currency-"+id).html(currencyName[id]);
							$("#price-currency-"+id).show();
						}
					}
				', CClientScript::POS_END);
			}
			else {
				Yii::app()->clientScript->registerScript('set-currency-name', '
					function setCurrencyName(id){
						var currencyName = '.CJavaScript::encode($data['currencyName']).';
						var currencyTitle = '.CJavaScript::encode($data['currencyTitle']).';

						$("#price-currency").html(currencyName[id]);
						$("#currency-title").html(currencyTitle[id]);
					}
				', CClientScript::POS_END);
			}

			Yii::app()->clientScript->registerScript('currency-name-init', '
				setCurrencyName($("select[name=\'apType\']").val());
				focusSubmit($("select#apType"));
			', CClientScript::POS_READY);
		?>
	</span>
</div>