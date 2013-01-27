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

class ApartmentObjType extends ParentModel {
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}


	public function tableName() 	{
		return '{{apartment_obj_type}}';
	}

	public function rules()	{
		return array(
   			array('name', 'i18nRequired'),
			array('sorter', 'numerical', 'integerOnly'=>true),
   			array('name', 'i18nLength', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
   			array('id, sorter, date_updated', 'safe', 'on'=>'search'),
			array($this->getI18nFieldSafe(), 'safe'),
		);
	}

    public function i18nFields(){
       return array(
           'name' => 'varchar(255) not null',
       );
    }

    public function getName(){
        return $this->getStrByLang('name');
    }

	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'name' => tt('Name'),
			'sorter' => 'Sorter',
			'date_updated' => 'Date Updated',
		);
	}

    public function search(){
        $criteria=new CDbCriteria;

        $criteria->compare('name_'.Yii::app()->language, $this->{'name_'.Yii::app()->language}, true);
        $criteria->order = 'sorter ASC';

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>param('adminPaginationPageSize', 20),
            ),
        ));
    }

    public function beforeSave(){
        if($this->isNewRecord){
            $maxSorter = Yii::app()->db->createCommand()
                ->select('MAX(sorter) as maxSorter')
                ->from($this->tableName())
                ->queryScalar();
            $this->sorter = $maxSorter+1;
        }

        return parent::beforeSave();
    }

    public function beforeDelete(){
        if($this->model()->count() <= 1){
			echo 0;
            return false;
        }

        $db = Yii::app()->db;

        $sql = "SELECT id FROM ".$this->tableName()." WHERE id != ".$this->id." ORDER BY sorter ASC";
        $type_id = (int) $db->createCommand($sql)->queryScalar();

        $sql = "UPDATE {{apartment}} SET obj_type_id={$type_id}, active=0 WHERE obj_type_id=".$this->id;
        $db->createCommand($sql)->execute();

        return parent::beforeDelete();
    }
}