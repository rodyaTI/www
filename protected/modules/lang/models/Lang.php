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

class Lang extends ParentModel
{
    private static $ISOlangs = array ( 'sq' => 'Albanian', 'ar' => 'Arabic', 'az' => 'Azeri', 'bn' => 'Bengali', 'bg' => 'Bulgarian', 'hr' => 'Croatian', 'cs' => 'Czech', 'da' => 'Danish', 'nl' => 'Dutch', 'en' => 'English', 'et' => 'Estonian', 'fa' => 'Farsi', 'fi' => 'Finnish', 'fr' => 'French', 'de' => 'German', 'ha' => 'Hausa', 'hi' => 'Hindi', 'hu' => 'Hungarian', 'is' => 'Icelandic', 'id' => 'Indonesian', 'it' => 'Italian', 'kk' => 'Kazakh', 'ky' => 'Kyrgyz', 'la' => 'Latin', 'lv' => 'Latvian', 'lt' => 'Lithuanian', 'mk' => 'Macedonian', 'mn' => 'Mongolian', 'ne' => 'Nepali', 'no' => 'Norwegian', 'ps' => 'Pashto', 'pl' => 'Polish', 'pt' => 'Portuguese', 'ro' => 'Romanian', 'ru' => 'Russian', 'sr' => 'Serbian', 'sk' => 'Slovak', 'sl' => 'Slovene', 'so' => 'Somali', 'es' => 'Spanish', 'sw' => 'Swahili', 'sv' => 'Swedish', 'tl' => 'Tagalog', 'tr' => 'Turkish', 'uk' => 'Ukrainian', 'ur' => 'Urdu', 'uz' => 'Uzbek', 'vi' => 'Vietnamese', 'cy' => 'Welsh', );

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{lang}}';
    }

    public function rules() {
        return array(
            array('name_iso,'.$this->i18nRules('name'), 'required'),
            array('active, sorter', 'numerical', 'integerOnly'=>true),
            array('name_iso', 'length', 'max'=>2),
            array($this->i18nRules('name'), 'length', 'max'=>100),
            array('id, name_iso, active, sorter, date_updated, '.$this->i18nRules('name'), 'safe', 'on'=>'search'),
        );
    }

    public function i18nFields(){
        return array(
            'name' => 'varchar(100) not null'
        );
    }

    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'name_iso' => 'Name Iso',
            'name_ru' => 'Name Ru',
            'active' => 'Active',
            'date_updated' => 'Date Updated',
        );
    }

    public function beforeSave(){
        if($this->isNewRecord){
            $maxSorter = Yii::app()->db->createCommand()
                ->select('MAX(sorter) as maxSorter')
                ->from($this->tableName())
                ->queryScalar();
            $this->sorter = $maxSorter+1;

            $this->addLang($this->name_iso);
        }

        return parent::beforeSave();
    }

    public function search()
    {
        $criteria=new CDbCriteria;

        $tmp = 'name_'.Yii::app()->language;
        $criteria->compare($tmp, $this->$tmp, true);
        $criteria->order = 'sorter ASC';

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public static function getISOlangArray(){
        $tmp = self::$ISOlangs;
        return $tmp;
    }

    public static function getISOname($lang){
        return isset(self::$ISOlangs[$lang]) ? self::$ISOlangs[$lang] : '';
    }

    public static function getActiveLangs($full = false){
        return array(
            Yii::app()->language => Yii::app()->language
        );
    }

    public static function getDefaultLang(){
        return Yii::app()->language;
    }
}