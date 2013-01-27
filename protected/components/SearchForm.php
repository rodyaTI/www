<?php
/**********************************************************************************************
*                            CMS Open Real Estate
*                              -----------------
*	version				:	1.3.2
*	copyright			:	(c) 2012 Monoray
*	website				:	http://www.monoray.ru/
*	contact us			:	http://www.monoray.ru/contact
*
* This file is part of CMS Open Real Estate
*
* Open Real Estate is free software. This work is licensed under a GNU GPL.
* http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
* Open Real Estate is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* Without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
***********************************************************************************************/

class SearchForm {
    static function cityInit(){
        $cityActive = array();
        if (file_exists(ALREADY_INSTALL_FILE)) {
            Yii::import('application.modules.apartmentCity.models.ApartmentCity');
            $cityActive = ApartmentCity::getActiveCity();
            if($cityActive === null){
                $cityActive = array();
            }
        }
        return $cityActive;
    }

	static function apTypes(){
		$result = Apartment::getApTypes();

		$types = array(0 => Yii::t('common', 'Please select'));
		if(in_array(Apartment::PRICE_PER_DAY, $result)){
			$types[Apartment::PRICE_PER_DAY] = tc('rent by the day');
		}

		if(in_array(Apartment::PRICE_PER_HOUR, $result)){
			$types[Apartment::PRICE_PER_HOUR] = tc('rent by the hour');
		}

		if(in_array(Apartment::PRICE_PER_MONTH, $result)){
			$types[Apartment::PRICE_PER_MONTH] = tc('rent by the month');
		}

		if(in_array(Apartment::PRICE_PER_WEEK, $result)){
			$types[Apartment::PRICE_PER_WEEK] = tc('rent by the week');
		}

		if(in_array(Apartment::PRICE_SALE, $result)){
			$types[Apartment::PRICE_SALE] = tc('sale');
		}

		$return['propertyType'] = $types;

 		$return['currencyName'] = array(param('siteCurrency', 'руб.'), param('siteCurrency', 'руб.'), param('siteCurrency', 'руб.'), param('siteCurrency', 'руб.'), param('siteCurrency', 'руб.'), param('siteCurrency', 'руб.'));

		if (issetModule('selecttoslider') && param('usePriceSlider') == 1) {
			$return['currencyTitle'] = array(Yii::t('common', 'Price range').':', Yii::t('common', 'Price range').':', Yii::t('common', 'Price range').':', Yii::t('common', 'Price range').':', Yii::t('common', 'Price range').':', Yii::t('common', 'Price range').':');
		}
		else {
			$return['currencyTitle'] = array(Yii::t('common', 'Payment to'), Yii::t('common', 'Payment to'), Yii::t('common', 'Fee up to'), Yii::t('common', 'Fee up to'), Yii::t('common', 'Fee up to'), Yii::t('common', 'Fee up to'));
		}

		return $return;
	}

}