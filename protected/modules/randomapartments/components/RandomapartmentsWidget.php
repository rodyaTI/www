<?php
/**********************************************************************************************
*                            CMS Open Real Estate
*                              -----------------
*	version				:	1.2.0
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

class RandomapartmentsWidget extends CWidget {
	public $usePagination = 1;
	public $criteria = null;
	public $count = null;
	public $widgetTitle = null;

	public function getViewPath($checkTheme=false){
		return Yii::getPathOfAlias('application.modules.apartments.views');
	}

	public function run() {
		Yii::import('application.modules.apartments.helpers.apartmentsHelper');

		$dependency = new CDbCacheDependency('SELECT MAX(date_updated) FROM {{apartment}}');
		$sql = 'SELECT id FROM {{apartment}}';
		$results = Yii::app()->db->cache(param('cachingTime', 1209600), $dependency)->createCommand($sql)->queryColumn();
		shuffle($results);

		$this->criteria = new CDbCriteria;
		$this->criteria->addInCondition('t.id', array_slice($results, 0, param('module_apartments_widgetApartmentsItemsPerPageRandom', 6)));

		$result = apartmentsHelper::getApartments(param('module_apartments_widgetApartmentsItemsPerPageRandom', 6), $this->usePagination, 0, $this->criteria);
		
		if($this->count){
			$result['count'] = $this->count;
		}

		$this->render('widgetApartments_list', $result);
	}
}