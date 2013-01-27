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

class Seo extends ParentModel {
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{seo}}';
	}

	public function rules() {
		return array(
			array($this->i18nRules('value'), 'required'),
			array($this->i18nRules('title').', '.$this->i18nRules('value'), 'safe', 'on' => 'search'),
		);
	}

    public function i18nFields(){
        return array(
            'value' => 'text not null',
        );
    }

    public function getName(){
        return $this->getStrByLang('name');
    }

    public function getValue(){
        return $this->getStrByLang('value');
    }
	
	public static function getSeoValue($name) {
		return Yii::app()->db->createCommand()    
			->select('value_'.Yii::app()->language.'')   
			->from('{{seo}}')  
			->where('name = "'.$name.'"')
			->queryScalar();  
	}

	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'name' => tt('Name', 'seo'),
			'value' => tt('Value', 'seo'),
			'date_updated' => tt('Update date', 'seo'),
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		//$criteria->compare('name', $this->name, true);
        $valueField = 'value_'.Yii::app()->language;
		$criteria->compare($valueField, $this->$valueField, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 'id DESC',
			),
			'pagination' => array(
				'pageSize' => param('adminPaginationPageSize', 20),
			),
		));
	}

	public function behaviors(){
		return array(
			'AutoTimestampBehavior' => array(
				'class' => 'zii.behaviors.CTimestampBehavior',
				'createAttribute' => 'date_updated',
				'updateAttribute' => 'date_updated',
			),
		);
	}
}