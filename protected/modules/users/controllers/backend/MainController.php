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

class MainController extends ModuleAdminController{
	public $modelName = 'User';
	public $scenario = 'backend';

	public function actionCreate(){
		$model=new $this->modelName;
		if($this->scenario){
			$model->scenario = $this->scenario;
		}

		if(isset($_POST[$this->modelName])){
			$model->attributes=$_POST[$this->modelName];
			if($model->validate()){
				$model->setPassword();
				$model->save(false);
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('create',array_merge(
				array('model'=>$model),
				$this->params
		));
	}

	public function actionUpdate($id){
		$this->scenario = 'update';
		parent::actionUpdate($id);
	}
	
	public function actionView($id){
		if ($id == 1) {
			$this->redirect(array('admin'));
		}
		
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}
}