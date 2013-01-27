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

class ViewallonmapWidget extends CWidget {
	public $usePagination = 1;
	public $criteria = null;
	public $count = null;

	public function run() {
		Yii::app()->getModule('apartments');
		if(param('useYandexMap', 1)) {
		    echo $this->render('application.modules.apartments.views.backend._ymap', '', true);
            CustomYMap::init()->createMap();
		} else
		    $result = CustomGMap::creatMap();

		$model = new Apartment;

		$criteria = new CDbCriteria;
		$lang = Yii::app()->language;
		$criteria->select = 'lat, lng, id, address_'.$lang.', title_'.$lang.', address_'.$lang;
		
		$ownerActiveCond = '';
		if (param('useUserads'))
			$ownerActiveCond = ' AND owner_active = '.Apartment::STATUS_ACTIVE.' ';
		$criteria->condition = 'lat <> "" AND lat<>"0" AND active='.Apartment::STATUS_ACTIVE.' AND (owner_id=1 OR owner_id>1 '.$ownerActiveCond.')';


		$cachingTime = param('shortCachingTime', 3600*4);
		$dependency = new CDbCacheDependency('SELECT MAX(date_updated) FROM {{apartment}}');

		$apartments = Apartment::model()->cache($cachingTime, $dependency)->with('images')->findAll($criteria);
		if(param('useYandexMap', 1)) {
			$lats = array();
			$lngs = array();
			foreach($apartments as $apartment){
				$lats[]	=	$apartment->lat;
				$lngs[]	=	$apartment->lng;
				$result = CustomYMap::init()->addMarker(
					$apartment->lat, $apartment->lng,
					$this->render('application.modules.apartments.views.backend._marker', array('model' => $apartment), true), 
					true, $apartment
				);
		    }

			if($lats && $lngs){
                CustomYMap::init()->setBounds(min($lats),max($lats),min($lngs),max($lngs));
                CustomYMap::init()->setClusterer();
			}
			else {
				$minLat = param('module_apartments_ymapsCenterX') - param('module_apartments_ymapsSpanX')/2;
				$maxLat = param('module_apartments_ymapsCenterX') + param('module_apartments_ymapsSpanX')/2;

				$minLng = param('module_apartments_ymapsCenterY') - param('module_apartments_ymapsSpanY')/2;
                $maxLng = param('module_apartments_ymapsCenterY') + param('module_apartments_ymapsSpanY')/2;

                CustomYMap::init()->setBounds($minLng,$maxLng,$minLat,$maxLat);
			}
            CustomYMap::init()->changeZoom(-1);
            CustomYMap::init()->processScripts();
		} elseif (param('useGoogleMap', 1)) {
		    foreach($apartments as $apartment){
				$result = CustomGMap::addMarker($result, $apartment,
					$this->render('application.modules.apartments.views.backend._marker', array('model' => $apartment), true)
				);
		    }
			//CustomGMap::centerMarkers($result);
		    $out['script'] = '';
		    $out['gMap'] = $result['gMap'];

		    echo $this->render('application.modules.apartments.views.backend._gmap', $out, true);

		}
	}
}