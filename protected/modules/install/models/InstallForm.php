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

class InstallForm extends CFormModel {

    public $agreeLicense;

    public $dbHost = 'localhost';
    public $dbPort = '3306';
    public $dbUser = 'root';
    public $dbPass;
	public $dbName;
	public $dbPrefix = 'ore_';

	public $adminName;
	public $adminPass;
	public $adminEmail;

    public function init() {
        return parent::init();
        Yii::import('application.modules.install.components.tFile');
    }

    private static $_app;

	public function rules()	{
		return array(
			array('dbUser, dbHost, dbName, adminName, adminPass, adminEmail', 'required'),
			array('agreeLicense', 'required', 'requiredValue' => true, 'message'=> tFile::getT('module_install', 'You should agree with "The license agreement"')),
			array('adminEmail', 'email'),
			array('dbUser, dbPass, dbName', 'length', 'max' => 30),
			array('dbHost', 'length', 'max' => 50),
			array('adminPass', 'length', 'max' => 20, 'min' => 6),
            array('dbPort', 'length', 'max' => 5),
			array('dbPrefix, dbPort', 'safe'),
		);
	}

	public function attributeLabels() {
		return array(
            'agreeLicense' => tFile::getT('module_install', 'I agree with').' ' . CHtml::link(tFile::getT('module_install', 'License agreement'), '#',
                                                            array('onclick'=>'$("#licensewidget").dialog("open"); return false;')),
            'dbHost' => tFile::getT('module_install', 'Database server'),
            'dbPort' => tFile::getT('module_install', 'Database port'),
            'dbUser' => tFile::getT('module_install', 'Database user name'),
            'dbPass' => tFile::getT('module_install', 'Database user password'),
            'dbName' => tFile::getT('module_install', 'Database name'),
            'dbPrefix' => tFile::getT('module_install', 'Prefix for tables'),
            'adminName' => tFile::getT('module_install', 'Administrator name'),
            'adminPass' => tFile::getT('module_install', 'Administrator password'),
            'adminEmail' => tFile::getT('module_install', 'Administrator email'),
		);
	}
}