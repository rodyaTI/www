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
	public $modelName = 'News';

    public function actionProduct(){

        //NewsProduct::getProductNews();
        Yii::app()->user->setState('menu_active', 'news.product');

        $model = NewsProduct::model();
      		$result = $model->getAllWithPagination();

      		$this->render('news_product', array(
      			'items' => $result['items'],
      			'pages' => $result['pages'],
      		));
    }
}