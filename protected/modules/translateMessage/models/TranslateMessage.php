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

class TranslateMessage extends ParentModel {
	private static $_cache;
    const DEFAULT_CATEGORY = 'common';

    const STATUS_NO_ERROR = 0;
    const STATUS_ERROR_NO_TRANSLATE = 1;

	private static $_statusArray;

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function behaviors() {
        return array(
            'ERememberFiltersBehavior' => array(
                'class' => 'application.components.behaviors.ERememberFiltersBehavior',
                'defaults'=>array(),
                'defaultStickOnClear'=>false
            ),
            'AutoTimestampBehavior' => array(
	            'class' => 'zii.behaviors.CTimestampBehavior',
	            'createAttribute' => null,
	            'updateAttribute' => 'date_updated',
	            'setUpdateOnCreate' => true,
            ),
        );
    }

	public function tableName() {
		return '{{translate_message}}';
	}

	public function rules() {
		return array(
			array('category, message', 'required'),
			array('translation', 'i18nRequired'),
			array('category', 'length', 'max'=>150),
			array('message', 'length', 'max'=>255),
			array('id, category, message, date_updated, status', 'safe', 'on'=>'search'),
            array('status', 'safe'),
			array($this->getI18nFieldSafe(), 'safe'),
		);
	}

    public function i18nFields(){
        return array(
            'translation' => 'text not null'
        );
    }

    public function getTranslation(){
        return $this->getStrByLang('translation');
    }

	public function setTranslation($value){
		$this->setStrByLang('translation', $value);
	}

	public function relations() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'category' => tt('category'),

			'message' => tt('String constant (defined in code)'),
			'language' => tc('Language'),
			'translation' => tt('Constant value (translation)'),
			'date_updated' => tc('Date Updated'),
			'status' => tc('Status'),
		);
	}

	public function search() {
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
        $criteria->compare('status',$this->status);
        $criteria->compare('category',$this->category);
        $criteria->compare('message',$this->message,true);
        $tmp = 'translation_'.Yii::app()->language;
		$criteria->compare($tmp, $this->$tmp, true);
		$criteria->compare('date_updated',$this->date_updated,true);

		return new CustomActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>param('adminPaginationPageSizeBig', 60),
			),
		));
	}

    public function beforeSave(){
        $this->status = self::STATUS_NO_ERROR;
        return parent::beforeSave();
    }

    public static function getCategoryFilter(){
        $sql = "SELECT category FROM {{translate_message}} GROUP BY category";
        $all = Yii::app()->db->createCommand($sql)->queryColumn();
        $filter = array();
        foreach($all as $category){
            $filter[$category] = $category;
        }
        return $filter;
    }

    public static function t($message, $category = self::DEFAULT_CATEGORY, $lang = ''){
        $lang = $lang ? $lang : Yii::app()->language;

        $translationField = 'translation_'.$lang;

//        if(!isset(self::$_cache[$category])){
//            self::$_cache[$category] = Yii::app()->cache->get($category);
//        }

        // Проверяем подгружена ли категория
        if(isset(self::$_cache[$category])){

            // Проверяем есть ли перевод для message
            if(isset(self::$_cache[$category][$message][$translationField])
                && self::$_cache[$category][$message][$translationField]) {

                return self::$_cache[$category][$message][$translationField];

                // Если перевода нет
            } else {

                if ( !isset(self::$_cache[$category][$message]['id']) ) {

                // Смотрим есть ли в базе запись
                $sql = "SELECT id, status FROM {{translate_message}} WHERE category=:category AND message=:message";
                $messageRow = Yii::app()->db->createCommand($sql)
                    ->bindValue(':category', $category, PDO::PARAM_STR)
                    ->bindValue(':message', $message, PDO::PARAM_STR)
                    ->queryScalar();

                if(!$messageRow['id']){
                    // Добавляем запись что есть message без перевода
                    $sql = "INSERT INTO {{translate_message}} (status, category, message)
                        VALUES (:status, :category, :message)";
                    Yii::app()->db->createCommand($sql)
                        ->bindValue(':status', self::STATUS_ERROR_NO_TRANSLATE, PDO::PARAM_INT)
                        ->bindValue(':category', $category, PDO::PARAM_STR)
                        ->bindValue(':message', $message, PDO::PARAM_STR)
                        ->execute();

                    self::$_cache[$category][$message]['id'] = Yii::app()->db->getLastInsertID();

                    } else {
                        self::$_cache[$category][$message]['id'] = $messageRow['id'];
                    }

                } else {

                    if ( self::$_cache[$category][$message]['status'] == self::STATUS_NO_ERROR ) {
                        // Обновляем запись что message без перевода
                        $sql = "UPDATE {{translate_message}} SET status=".self::STATUS_ERROR_NO_TRANSLATE
                            ." WHERE id=".self::$_cache[$category][$message]['id'];
                        Yii::app()->db->createCommand($sql)->execute();
                    }
                }

                self::$_cache[$category][$message]['status'] = self::STATUS_ERROR_NO_TRANSLATE;
            }

            // Если перевод не найден, отдаем %message%
            return "%$message%";

        }else{
            // подгружаем категорию с переводами

            $sql = "SELECT id, message, status, ".self::i18nGenFields('translation')."
                    FROM {{translate_message}}
                    WHERE category='{$category}'
                    ";
            $activeLangs = Lang::getActiveLangs();
            $categoryMes = Yii::app()->db->createCommand($sql)->queryAll();

            // заполняем $_cache в нужном нам формате
            if($categoryMes){
                foreach($categoryMes as $item){

                    self::$_cache[$category][$item['message']]['status'] = $item['status'];
                    self::$_cache[$category][$item['message']]['id'] = $item['id'];

                    foreach($activeLangs as $langTmp){
                        $translationFieldTmp = 'translation_'.$langTmp;
                        self::$_cache[$category][$item['message']][$translationFieldTmp] = $item[$translationFieldTmp];
                    }
                }
            } else {
                // Если в базе нет такой категории, записываем в кеш пустой массив
                // указав тем самым что подгрузка этой категории была
                self::$_cache[$category] = array();
            }

//            $dependency = new CDbCacheDependency("SELECT MAX(date_updated) FROM {{translate_message}} WHERE category='".addslashes($category)."' ");
//            Yii::app()->cache->set($category, self::$_cache[$category], 0, $dependency);

            return self::t($message, $category, $lang);
        }
    }

    public static function getStatusArray(){
        if(!isset(self::$_statusArray)){
            self::$_statusArray = array(
                self::STATUS_NO_ERROR => tt('Translated'),
                self::STATUS_ERROR_NO_TRANSLATE => tt('Not translated'),
            );
        }
        return self::$_statusArray;
    }

    public function getStatusHtml(){
        if(!isset(self::$_statusArray)){
            self::getStatusArray();
        }
        if($this->status == self::STATUS_NO_ERROR){
            $cssClass = 'status_green'; //'#009933';
        } else {
            $cssClass = 'status_red'; //'#FF3333';
        }
        return "<span class=\"{$cssClass}\">".self::$_statusArray[$this->status]."</span>";
    }
}