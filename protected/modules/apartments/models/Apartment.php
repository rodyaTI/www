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

class Apartment extends ParentModel {
	public $title;

	public $ownerEmail;

    const TYPE_RENT = 1;
    const TYPE_SALE = 2;
    const TYPE_DEFAULT = 1;

    private static $_type_arr;
	private static $_apartment_arr;

    const PRICE_SALE = 1;
    const PRICE_PER_HOUR = 2;
    const PRICE_PER_DAY = 3;
    const PRICE_PER_WEEK = 4;
    const PRICE_PER_MONTH = 5;

	const STATUS_INACTIVE = 0;
	const STATUS_ACTIVE = 1;
	const STATUS_MODERATION = 2;

    private static $_price_arr;

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{apartment}}';
	}

	public function behaviors(){
		return array(
			'AutoTimestampBehavior' => array(
				'class' => 'zii.behaviors.CTimestampBehavior',
				'createAttribute' => 'date_created',
				'updateAttribute' => 'date_updated',
			),
		);
	}

	public function rules() {
		return array(
			array('price', 'required'),
			array('title', 'i18nRequired'),
			array('price, floor, floor_total, square, window_to, type, price_type, obj_type_id, city_id', 'numerical', 'integerOnly' => true),
			array('price ', 'numerical', 'min' => 1),
			array('berths', 'length', 'max' => 255),
			array('title', 'i18nLength', 'max' => 255),
			array('lat, lng', 'length', 'max' => 25),
			array('id', 'safe', 'on' => 'search'),
			array('floor', 'myFloorValidator'),
			array('owner_active, num_of_rooms, is_special_offer, is_free_from, is_free_to, active', 'safe'),
			array($this->getI18nFieldSafe(), 'safe'),
			array('city_id, owner_active, active, type, ownerEmail', 'safe', 'on' => 'search'),
		);
	}

    public function i18nFields(){
        return array(
            'title' => 'text not null',
            'address' => 'varchar(255) not null',
            'description' => 'text not null',
            'description_near' => 'text not null'
        );
    }

    public function currencyFields(){
        return array('price');
    }

	public function myFloorValidator($attribute,$params){
		if($this->floor && $this->floor_total){
			if($this->floor > $this->floor_total)
			$this->addError('floor', tt('validateFloorMoreTotal', 'apartments'));
		}
	}

	public function relations() {
        Yii::import('application.modules.apartmentObjType.models.ApartmentObjType');
        Yii::import('application.modules.apartmentCity.models.ApartmentCity');
		$relations = array(
			'objType' => array(self::BELONGS_TO, 'ApartmentObjType', 'obj_type_id'),

			'city' => array(self::BELONGS_TO, 'ApartmentCity', 'city_id'),

			'windowTo' => array(self::BELONGS_TO, 'WindowTo', 'window_to'),

			'images' => array(self::HAS_ONE, 'Galleries', 'pid'/*, 'select' => 'imgsOrder'*/),

			'comments' => array(self::HAS_MANY, 'Comment', 'apartment_id',
				'on' => 'comments.active = '.Comment::STATUS_APPROVED,
				'order' => 'comments.id DESC',
			),
			'commentCount' => array(self::STAT, 'Comment', 'apartment_id',
				'condition' => 'active=' . Comment::STATUS_APPROVED),

			'user' => array(self::BELONGS_TO, 'User', 'owner_id'),
		);

		if(issetModule('bookingcalendar')) {
			$bookingCalendar = new Bookingcalendar; // for publish assets
			$relations['bookingCalendar'] = array(self::HAS_MANY, 'Bookingcalendar', 'apartment_id');
		}

		return $relations;
	}

	public function getUrl() {
		//$tmp = 'title_'.Yii::app()->language;
		return Yii::app()->createUrl('/apartments/main/view', array(
			'id' => $this->id,
			//'title' => $this->$tmp,
		));
	}

    public static function getUrlById($id){
        return Yii::app()->createUrl('/apartments/main/view', array(
      			'id' => $id,
      		));
    }

	public function attributeLabels() {
		return array(
			'id' => tt('ID', 'apartments'),
            'type' => tt('Type', 'apartments'),
			'price' => tt('Price', 'apartments'),
			'num_of_rooms' => tt('Number of rooms', 'apartments'),
			'floor' => tt('Floor', 'apartments'),
			'floor_total' => tt('Total number of floors', 'apartments'),
			'square' => tt('Square', 'apartments'),
			'window_to' => tt('Window to', 'apartments'),
			'title' => tt('Apartment title', 'apartments'),
			'description' => tt('Description', 'apartments'),
			'description_near' => tt('What is near?', 'apartments'),
			'metro_station' => tt('Metro station', 'apartments'),
			'address' => tt('Address', 'apartments'),
			'special_offer' => tt('Special offer', 'apartments'),
			'berths' => tt('Number of berths', 'apartments'),
			'active' => tt('Status', 'apartments'),
			'is_free_from' => tt('Is free from', 'apartments'),
			'is_free_to' => tt('to', 'apartments'),
			'is_special_offer' => tt('Special offer', 'apartments'),
            'obj_type_id' => tt('Object type', 'apartments'),
            'city_id' => tt('City', 'apartments'),
			'city' => tt('City', 'apartments'),
			'owner_active' => tt('Status (owner)'),
			'ownerEmail' => tt('Owner'),
		);
	}

	public function search() {

		$criteria = new CDbCriteria;
		$tmp = 'title_'.Yii::app()->language;

		$criteria->compare($this->getTableAlias().'.id', $this->id);
		$criteria->compare($this->getTableAlias().'.active', $this->active, true);
		$criteria->compare($this->getTableAlias().'.owner_active', $this->owner_active, true);
		$criteria->compare('city_id', $this->city_id);
		$criteria->compare('type', $this->type);

		$criteria->compare($tmp, $this->$tmp, true);

		$criteria->with = array('user');

		if (issetModule('userads') && param('useModuleUserAds', 1)) {

			if ($this->ownerEmail) {
				$criteriaOwner = new CDbCriteria;
				$criteriaOwner->addCondition('email LIKE "%'.$this->ownerEmail.'%"');
				$userInfo = User::model()->find($criteriaOwner);
				if ($userInfo && count($userInfo) > 0 && isset($userInfo->id)) {
					$criteria->compare('owner_id', $userInfo->id, true);
				}
			}
		}
		$criteria->order = $this->getTableAlias().'.sorter DESC';
		$criteria->with = array('city');

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			//'sort'=>array('defaultOrder'=>'sorter'),
			'pagination'=>array(
				'pageSize'=>param('adminPaginationPageSize', 20),
			),
		));
	}

	public function getPriceFrom(){
	    return $this->price;
	}

	public function getCurrency(){
        $currency = param('siteCurrency', 'руб.').' '.self::getPriceName($this->price_type);
        return $currency;
	}

	public static function getFullInformation($apartmentId, $type = Apartment::TYPE_DEFAULT){

        $addWhere = '';
        $addWhere .= (Apartment::TYPE_RENT == $type) ? ' AND reference_values.for_rent=1' : '';
        $addWhere .= (Apartment::TYPE_SALE == $type) ? ' AND reference_values.for_sale=1' : '';

		$sql = '
			SELECT	style,
					reference_categories.title_'.Yii::app()->language.' as category_title,
					reference_values.title_'.Yii::app()->language.' as value,
					reference_categories.id as ref_id,
					reference_values.id as ref_value_id
			FROM	{{apartment_reference}} reference,
					{{apartment_reference_categories}} reference_categories,
					{{apartment_reference_values}} reference_values
			WHERE	reference.apartment_id = "'.intval($apartmentId).'"
					AND reference.reference_id = reference_categories.id
					AND reference.reference_value_id = reference_values.id
					'.$addWhere.'
			ORDER BY reference_categories.sorter, reference_values.sorter';

		// Таблица apartment_reference меняется только при измении объявления (т.е. таблицы apartment)
		// Достаточно зависимости от apartment вместо apartment_reference
		$dependency = new CDbCacheDependency('
			SELECT MAX(val) FROM
				(SELECT MAX(date_updated) as val FROM {{apartment_reference_values}}
				UNION
				SELECT MAX(date_updated) as val FROM {{apartment_reference_categories}}
				UNION
				SELECT MAX(date_updated) as val FROM {{apartment}} WHERE id = "'.intval($apartmentId).'") as t
		');

		$results = Yii::app()->db->cache(param('cachingTime', 1209600), $dependency)->createCommand($sql)->queryAll();

		$return = array();
		foreach($results as $result){
			if(!isset($return[$result['ref_id']])){
				$return[$result['ref_id']]['title'] = $result['category_title'];
				$return[$result['ref_id']]['style'] = $result['style'];
			}
			$return[$result['ref_id']]['values'][$result['ref_value_id']] = $result['value'];
		}
		return $return;
	}

	public static function getCategories($id = null, $type = Apartment::TYPE_DEFAULT){
        $addWhere = '';
        $addWhere .= (Apartment::TYPE_RENT == $type) ? ' AND reference_values.for_rent=1' : '';
        $addWhere .= (Apartment::TYPE_SALE == $type) ? ' AND reference_values.for_sale=1' : '';

		$sql = '
			SELECT	style,
					reference_values.title_'.Yii::app()->language.' as value_title,
					reference_categories.title_'.Yii::app()->language.' as category_title,
					reference_category_id, reference_values.id
			FROM	{{apartment_reference_values}} reference_values,
					{{apartment_reference_categories}} reference_categories
			WHERE	reference_category_id = reference_categories.id
			'.$addWhere.'
			ORDER BY reference_categories.sorter, reference_values.sorter';

		$dependency = new CDbCacheDependency('
			SELECT MAX(val) FROM
				(SELECT MAX(date_updated) as val FROM {{apartment_reference_values}}
				UNION
				SELECT MAX(date_updated) as val FROM {{apartment_reference_categories}}) as t
		');

		$results = Yii::app()->db->cache(param('cachingTime', 1209600), $dependency)->createCommand($sql)->queryAll();

		$return = array();
		$selected = array();

		if($id){
			$selected = Apartment::getFullInformation($id, $type);
		}
		if($results){
			foreach($results as $result){
				$return[$result['reference_category_id']]['title'] = $result['category_title'];
				$return[$result['reference_category_id']]['style'] = $result['style'];
				$return[$result['reference_category_id']]['values'][$result['id']]['title'] = $result['value_title'];
				if(isset($selected[$result['reference_category_id']]['values'][$result['id']] )){
					$return[$result['reference_category_id']]['values'][$result['id']]['selected'] = true;
				}
				else{
					$return[$result['reference_category_id']]['values'][$result['id']]['selected'] = false;
				}
			}
		}
		return $return;
	}

	public function getMainThumb(){
		if($this->images && $this->images->imgsOrder){
			$images = unserialize($this->images->imgsOrder);
			reset($images);
			return key($images);
		}
		return null;
	}

	public function getAllImages(){
		if($this->images && $this->images->imgsOrder){
			$images = unserialize($this->images->imgsOrder);
			return array_keys($images);
		}
		return null;
	}

	public function saveCategories(){
		if(isset($_POST['category'])){
			$sql = 'DELETE FROM {{apartment_reference}} WHERE apartment_id="'.$this->id.'"';
			Yii::app()->db->createCommand($sql)->execute();

			foreach($_POST['category'] as $catId => $value){
				foreach($value as $valId => $val){
					$sql = 'INSERT INTO {{apartment_reference}} (reference_id, reference_value_id, apartment_id)
						VALUES (:refId, :refValId, :apId) ';
					$command = Yii::app()->db->createCommand($sql);
					$command->bindValue(":refId", $catId, PDO::PARAM_INT);
					$command->bindValue(":refValId", $valId, PDO::PARAM_INT);
					$command->bindValue(":apId", $this->id, PDO::PARAM_INT);
					$command->execute();
				}
			}
		}
	}

	public function beforeSave(){
		if(!$this->square){
			$this->square = 0;
		}

		if($this->isNewRecord){
			$this->owner_id = Yii::app()->user->id;

			// if admin
			$userInfo = User::model()->findByPk($this->owner_id, array('select' => 'isAdmin'));
			if ($userInfo && $userInfo->isAdmin == 1) {
				$this->owner_active = self::STATUS_ACTIVE;
			}

			$maxSorter = Yii::app()->db->createCommand()
				->select('MAX(sorter) as maxSorter')
				->from($this->tableName())
				->queryScalar();
			$this->sorter = $maxSorter+1;
		}

		return parent::beforeSave();
	}

	public function afterSave(){
		if($this->scenario == 'savecat'){
			$this->saveCategories();
        }

        if($this->scenario != 'update_status'){
            // generate pdf
            Yii::import('application.modules.viewpdf.models.Viewpdf');
            Yii::app()->controller->widget('application.modules.viewpdf.components.viewPdfComponent',
                array('id' => $this->id, 'fromAdmin' => true));
        }

		return parent::afterSave();
	}

	public function beforeDelete(){

		$sql = 'DELETE FROM {{apartment_reference}} WHERE apartment_id="'.$this->id.'"';
		Yii::app()->db->createCommand($sql)->execute();

		$sql = 'DELETE FROM {{apartment_comments}} WHERE apartment_id="'.$this->id.'"';
		Yii::app()->db->createCommand($sql)->execute();

		$dir = Yii::getPathOfAlias('webroot.uploads.apartments') . '/'.$this->id;
		rrmdir($dir);

		$sql = 'DELETE FROM {{galleries}} WHERE pid="'.$this->id.'"';
		Yii::app()->db->createCommand($sql)->execute();

		$sql = 'DELETE FROM {{apartment_comments}} WHERE apartment_id="'.$this->id.'"';
		Yii::app()->db->createCommand($sql)->execute();

		// delete pdf file for apartment
		Yii::import('application.modules.viewpdf.models.Viewpdf');
		Yii::import('application.modules.viewpdf.components.viewPdfComponent');

		$viewPdf = new viewPdfComponent();
		$filePdf = $viewPdf->pdfCachePath.'/'.$viewPdf->filePrefix . $this->id . '.pdf';

		if (file_exists($filePdf)) {
			unlink($filePdf);
		}

		return parent::beforeDelete();
	}

	public function isValidApartment($id){
		$sql = 'SELECT id FROM {{apartment}} WHERE id = :id';
		$command = Yii::app()->db->createCommand($sql);
		return $command->queryScalar(array(':id' => $id));
	}

	public static function getFullDependency($id){
		return new CDbCacheDependency('
			SELECT MAX(val) FROM
				(SELECT MAX(date_updated) as val FROM {{apartment_comments}} WHERE apartment_id = "'.intval($id).'"
				UNION
				SELECT MAX(date_updated) as val FROM {{apartment}} WHERE id = "'.intval($id).'"
				UNION
				SELECT MAX(date_updated) as val FROM {{apartment_window_to}}
				UNION
				SELECT MAX(date_updated) as val FROM {{galleries}}) as t
		');
	}

	public static function getImagesDependency(){
		return new CDbCacheDependency('
			SELECT MAX(val) FROM
				(SELECT MAX(date_updated) as val FROM {{apartment}}
				UNION
				SELECT date_updated as val FROM {{galleries}}) as t
		');
	}

	public static function getDependency(){
		return new CDbCacheDependency('SELECT MAX(date_updated) FROM {{apartment}}');
	}

	public static function getExistsRooms(){
		$sql = 'SELECT DISTINCT num_of_rooms FROM {{apartment}} WHERE active='.self::STATUS_ACTIVE.' AND owner_active = '.self::STATUS_ACTIVE.' AND num_of_rooms > 0 ORDER BY num_of_rooms';
		return Yii::app()->db->cache(param('cachingTime', 1209600), self::getDependency())->createCommand($sql)->queryColumn();
	}

    public static function getObjTypesArray($with_all = false){
        Yii::import('application.modules.apartmentObjType.models.ApartmentObjType');
        $objTypes = array();
        $objTypeModel = ApartmentObjType::model()->findAll(array(
            'order'=>'sorter'
        ));
        foreach($objTypeModel as $type){
            $objTypes[$type->id] = $type->name;
        }
        if($with_all){
            $objTypes[0] = tt('All object', 'apartments');
        }
        return $objTypes;
    }

    public static function getCityArray($with_all = false){
        Yii::import('application.modules.apartmentCity.models.ApartmentCity');
        $cityArr = array();
        $cityModel = ApartmentCity::model()->findAll(array(
            'order'=>'sorter'
        ));
        foreach($cityModel as $city){
            $cityArr[$city->id] = $city->name;
        }
        if($with_all){
            $cityArr[0] = tt('All city', 'apartments');
        }
        return $cityArr;
    }

    public static function getTypesArray($withAll = false){
        $types = array();

		if($withAll){
            $types[0] = tt('All', 'apartments');
        }

		$types[self::TYPE_RENT] = tt('Rent', 'apartments');
		$types[self::TYPE_SALE] = tt('Sale', 'apartments');
        return $types;
    }

	public static function getI18nTypesArray(){
        $types = array();

		$default = Lang::getDefaultLang();
		$admin = Lang::getDefaultLang();
		$current = Yii::app()->language;

		$types[self::TYPE_RENT]['current'] = tt('Rent', 'apartments');
		$types[self::TYPE_SALE]['current'] = tt('Sale', 'apartments');

		if ($current != $default) {
			$types[self::TYPE_RENT]['default'] = tt('Rent', 'apartments', $default);
			$types[self::TYPE_SALE]['default'] = tt('Sale', 'apartments', $default);
		} else {
			$types[self::TYPE_RENT]['default'] = $types[self::TYPE_RENT]['current'];
			$types[self::TYPE_SALE]['default'] = $types[self::TYPE_SALE]['current'];
		}

		if ($current != $admin) {
			$types[self::TYPE_RENT]['admin'] = tt('Rent', 'apartments', $default);
			$types[self::TYPE_SALE]['admin'] = tt('Sale', 'apartments', $default);
		} else {
			$types[self::TYPE_RENT]['admin'] = $types[self::TYPE_RENT]['current'];
			$types[self::TYPE_SALE]['admin'] = $types[self::TYPE_SALE]['current'];
		}

        return $types;
    }

	public static function getTypesWantArray() {
		$types = array();

		$types[self::TYPE_RENT] = Yii::t('common', 'rent apartment');
		$types[self::TYPE_SALE] = Yii::t('common', 'buy apartment');

        return $types;
	}

    public static function getNameByType($type){
        if(!isset(self::$_type_arr)){
            self::$_type_arr = self::getTypesArray();
        }
        return self::$_type_arr[$type];
    }

    public static function getPriceArray($type, $all = false, $with_all = false){
        if($all){
            return array(
                self::PRICE_SALE => tt('Sale price', 'apartments'),
                self::PRICE_PER_HOUR => tt('Price per hour', 'apartments'),
                self::PRICE_PER_DAY => tt('Price per day', 'apartments'),
                self::PRICE_PER_WEEK => tt('Price per week', 'apartments'),
                self::PRICE_PER_MONTH => tt('Price per month', 'apartments'),
            );
        }

        if($type == self::TYPE_SALE){
            $price = array(
                self::PRICE_SALE => tt('Sale price', 'apartments'),
            );
        }elseif($type == self::TYPE_RENT){
            $price = array(
                self::PRICE_PER_HOUR => tt('Price per hour', 'apartments'),
                self::PRICE_PER_DAY => tt('Price per day', 'apartments'),
                self::PRICE_PER_WEEK => tt('Price per week', 'apartments'),
                self::PRICE_PER_MONTH => tt('Price per month', 'apartments'),
            );
        }

        if($with_all){
            $price[0] = tt('All');
        }
        return $price;
    }

    public static function getPriceName($price_type){
        if(!isset(self::$_price_arr)){
            self::$_price_arr = self::getPriceArray(NULL, true);
        }
        return self::$_price_arr[$price_type];
    }

	public function getPrettyPrice(){
		$price = $this->getPriceFrom();

		if(!param('usePrettyPrice', 1) || Yii::app()->language != 'ru'){
			return $price . ' ' . $this->getCurrency();
		}

		if (substr($price, -6) == "000000")
			$priceStr = substr_replace ($price, ' '.tt('million', 'apartments'), -6);
		elseif (substr($price, -5) == "00000" && strlen($price) >= 7) {
			$priceStr = substr_replace ($price, '.', -6, 0);
			$priceStr = substr_replace ($priceStr, ' '.tt('million', 'apartments'), -5);
		} elseif (substr($price, -3) == "000")
			$priceStr = substr_replace ($price, ' '.tt('thousand', 'apartments'), -3);
		elseif (substr($price, -2) == "00" && strlen($price) >= 4) {
			$priceStr = substr_replace ($price, '.', -3, 0);
			$priceStr = substr_replace ($priceStr, ' '.tt('thousand', 'apartments'), -2);
		} else {
            $priceStr = $price.' '.$this->getCurrency();
            return $priceStr;
        }

		$priceStr .= param('siteCurrency', 'руб.').' '.self::getPriceName($this->price_type);
        return $priceStr;
    }

	public static function getApTypes(){
		$ownerActiveCond = '';
		if (param('useUserads'))
			$ownerActiveCond = ' AND owner_active = '.self::STATUS_ACTIVE.' ';

		$sql = 'SELECT DISTINCT price_type FROM {{apartment}} WHERE active = '.self::STATUS_ACTIVE.' '.$ownerActiveCond.'';
		return Yii::app()->db->cache(param('cachingTime', 1209600), self::getDependency())->createCommand($sql)->queryColumn();
	}

	public static function getSquareMinMax(){
		$ownerActiveCond = '';
		if (param('useUserads'))
			$ownerActiveCond = ' AND owner_active = '.self::STATUS_ACTIVE.' ';

		$sql = 'SELECT MIN(square) as square_min, MAX(square) as square_max FROM {{apartment}} WHERE active = '.self::STATUS_ACTIVE.' '.$ownerActiveCond.'';
		return Yii::app()->db->cache(param('cachingTime', 1209600), self::getDependency())->createCommand($sql)->queryRow();
	}

	public static function getPriceMinMax($price_type = 1, $all = false){
		$ownerActiveCond = '';
		if (param('useUserads'))
			$ownerActiveCond = ' AND owner_active = '.self::STATUS_ACTIVE.' ';

		if ($all)
			$sql = 'SELECT MIN(price) as price_min, MAX(price) as price_max FROM {{apartment}} WHERE active = '.self::STATUS_ACTIVE.' '.$ownerActiveCond.'';
		else
			$sql = 'SELECT MIN(price) as price_min, MAX(price) as price_max FROM {{apartment}} WHERE price_type = "'.$price_type.'" AND active = '.self::STATUS_ACTIVE.' '.$ownerActiveCond.'';

		return Yii::app()->db->cache(param('cachingTime', 1209600), self::getDependency())->createCommand($sql)->queryRow();
	}

	public static function getModerationStatusArray($withAll = false){
		$status = array();
		if($withAll){
            $status[''] = tt('All', 'common');
        }

		$status[0] = tt('Inactive', 'common');
		$status[1] = tt('Active', 'common');
		$status[2] = tt('Awaiting moderation', 'common');

		return $status;
    }

	public static function getRel($id, $lang){
		$model = self::model()->resetScope()->findByPk($id);

		$title = 'title_'.$lang;
		$model->title = $model->$title;

		return $model;
	}

    public function getAddress(){
        return $this->getStrByLang('address');
    }

	public static function getApartmentsStatusArray($withAll = false) {
		$status = array();
		if($withAll){
            $status[''] = Yii::t('common', 'All');
        }

		$status[0] = Yii::t('common', 'Inactive');
		$status[1] = Yii::t('common', 'Active');

		return $status;
	}

	public static function getApartmentsStatus($status){
        if(!isset(self::$_apartment_arr)){
            self::$_apartment_arr = self::getApartmentsStatusArray();
        }
        return self::$_apartment_arr[$status];
	}

	public static function setApartmentVisitCount($id = '', $ipAddress = '', $userAgent = '') {
		if ($id) {
			Yii::app()->db->createCommand()->insert('{{apartment_statistics}}', array(
				'apartment_id'=> $id,
				'date_created' => new CDbExpression('NOW()'),
				'ip_address'=> $ipAddress,
				'browser'=> $userAgent,
			));
		}
	}

	public static function getApartmentVisitCount($id) {
		if ($id) {
			$statistics = array();

			$statistics['all'] = Yii::app()->db->createCommand()
					->select(array(new CDbExpression("COUNT(id) AS countAll")))
					->from('{{apartment_statistics}}')
					->where('apartment_id = "'.$id.'"')
					->queryScalar();

			$statistics['today'] = Yii::app()->db->createCommand()
					->select(array(new CDbExpression("COUNT(id) AS countToday")))
					->from('{{apartment_statistics}}')
					->where('apartment_id = "'.$id.'" AND date(date_created)=date(now())')
					->queryScalar();

			return $statistics;
		}
		return false;
	}

    public static function getCountModeration(){
        $sql = "SELECT COUNT(id) FROM {{apartment}} WHERE active=".self::STATUS_MODERATION;
        return (int) Yii::app()->db->createCommand($sql)->queryScalar();
    }

    public static function getFirstImgName($id){
        $sql = "  SELECT imgsOrder FROM {{galleries}} WHERE pid=".intval($id);

        $imgsOrder = Yii::app()->db->createCommand($sql)->queryScalar();

        $adImgs = $imgsOrder ? unserialize($imgsOrder) : '';

        if(is_array($adImgs) && count($adImgs) > 0){
            reset($adImgs);
            return key($adImgs);
        }

        return NULL;
    }
}