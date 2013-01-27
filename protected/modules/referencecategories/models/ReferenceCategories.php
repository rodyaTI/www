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

class ReferenceCategories extends ParentModel{

	public static function model($className=__CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return '{{apartment_reference_categories}}';
	}

	public function rules(){
        return array(
			array('style', 'required'),
			array('title', 'i18nRequired'),
			array('style', 'in', 'range' => array('column1', 'column2', 'column3')),
			array('sorter', 'numerical', 'integerOnly'=>true),
			array('title', 'i18nLength', 'max'=>255),
			array($this->getI18nFieldSafe(), 'safe'),
        );
	}

    public function i18nFields(){
        return array(
            'title' => 'varchar(255) not null',
        );
    }

	public function relations(){
		Yii::app()->getModule('referencevalues');
		return array(
			'values' => array(self::HAS_MANY, 'ReferenceValues', 'reference_category_id'),
		);
	}

	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'title' => tt('Reference name'),
			'sorter' => 'Sorter',
			'date_updated' => 'Date Updated',
			'style' => tt('Display style'),
		);
	}

	public function search(){
		$criteria=new CDbCriteria;

		$criteria->compare('title_'.Yii::app()->language,$this->{'title_'.Yii::app()->language},true);
		$criteria->order = 'sorter ASC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>param('adminPaginationPageSize', 20),
			),
		));
	}

	public function behaviors(){
		return array(
			'AutoTimestampBehavior' => array(
				'class' => 'zii.behaviors.CTimestampBehavior',
				'createAttribute' => null,
				'updateAttribute' => 'date_updated',
			),
		);
	}

	public function beforeSave(){
		if($this->isNewRecord){
			$maxSorter = Yii::app()->db->createCommand()
				->select('MAX(sorter) as maxSorter')
				->from($this->tableName())
				->queryScalar();
			$this->sorter = $maxSorter+1;
		}

		$this->date_updated = new CDbExpression('NOW()');

		return parent::beforeSave();
	}

	public function catTitle(){
		$tmp = 'title_'.Yii::app()->language;
		return $this->$tmp;
	}

	public function beforeDelete(){
		$sql = 'DELETE FROM {{apartment_reference_values}} WHERE reference_category_id="'.$this->id.'";';
		Yii::app()->db->createCommand($sql)->execute();

		$sql = 'DELETE FROM {{apartment_reference}} WHERE reference_id="'.$this->id.'"';
		Yii::app()->db->createCommand($sql)->execute();

		return parent::beforeDelete();
	}

	public function getStyles(){
		return array(
			'column1' => tt('1 column'),
			'column2' => tt('2 columns'),
			'column3' => tt('3 columns'),
		);
	}
}