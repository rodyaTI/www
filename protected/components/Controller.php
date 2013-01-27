<?php

/* * ********************************************************************************************
 *                            CMS Open Real Estate
 *                              -----------------
 * 	version				:	1.3.2
 * 	copyright			:	(c) 2012 Monoray
 * 	website				:	http://www.monoray.ru/
 * 	contact us			:	http://www.monoray.ru/contact
 *
 * This file is part of CMS Open Real Estate
 *
 * Open Real Estate is free software. This work is licensed under a GNU GPL.
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Open Real Estate is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * Without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * ********************************************************************************************* */

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController {

	public $layout = '//layouts/index';
	public $infoPages = array();
	public $menuTitle;
	public $menu = array();
	public $breadcrumbs = array();
	public $pageKeywords;
	public $pageDescription;
	public $adminTitle = '';
	public $aData;
    public $modelName;

    public $seoTitle;
    public $seoDescription;
    public $seoKeywords;

	/* advertising */
	public $advertPos1 = array();
	public $advertPos2 = array();
	public $advertPos3 = array();
	public $advertPos4 = array();
	public $advertPos5 = array();
	public $advertPos6 = array();

	protected function beforeAction($action) {
		if (!Yii::app()->user->getState('isAdmin')) {
			$currentController = Yii::app()->controller->id;
			$currentAction = Yii::app()->controller->action->id;

			if (!($currentController == 'site' && ($currentAction == 'login' || $currentAction == 'logout'))) {
				if (issetModule('service')){
					$serviceInfo = Service::model()->findByPk(Service::SERVICE_ID);
					if ($serviceInfo && $serviceInfo->is_offline == 1) {
						$allowIps = explode(',', $serviceInfo->allow_ip);
						$allowIps = array_map("trim", $allowIps);

						if (!in_array(Yii::app()->request->userHostAddress, $allowIps)) {
							$this->renderPartial('//../modules/service/views/index', array('page' => $serviceInfo->page), false, true);
							Yii::app()->end();
						}
					}
				}
			}
		}

		/* start  get page banners */
		if (issetModule('advertising') && !param('useBootstrap')) {
			$advert = new Advert;
			$advert->getAdvertContent();
		}
		/* end  get page banners */

		return parent::beforeAction($action);
    }

	function init() {
		if (!file_exists(ALREADY_INSTALL_FILE) && !(Yii::app()->controller->module && Yii::app()->controller->module->id == 'install')) {
			$this->redirect(array('/install'));
		}

        Yii::app()->user->setState('menu_active', '');

		$this->pageTitle = Seo::getSeoValue('siteName');
		Yii::app()->name = $this->pageTitle;
		$this->pageKeywords = Seo::getSeoValue('siteKeywords');
		$this->pageDescription = Seo::getSeoValue('siteDescription');

        // seo settings
        if (!param('useBootstrap')) {
            if (issetModule('seo') && file_exists(Yii::getPathOfAlias('application.modules.seo.models.Seopage').'.php')) {
                $tdk = Seopage::getSeoPageTDK(Yii::app()->language);
                if ($tdk && is_array($tdk)) {
                    $this->seoTitle = ($tdk['title']) ? $tdk['title'] : '';
                    $this->seoDescription = ($tdk['description']) ? $tdk['description'] : '';
                    $this->seoKeywords = ($tdk['keywords']) ? $tdk['keywords'] : '';
                }
            }
        }

		if(Yii::app()->getModule('menumanager')){
			if(!(Yii::app()->controller->module && Yii::app()->controller->module->id == 'install')){
				$this->infoPages = Menu::getMenuItems();
			}
		}

		$this->aData['userCpanelItems'] = array(
			array(
				'label' => tt('Add ad', 'common'),
				'url' => array('/userads/main/create'),
				'visible' => param('useUserads', 0) == 1
			),
			array(
				'label' => '|',
				'visible' => param('useUserads', 0) == 1
			),
			array('label' => tt('Contact us', 'common'), 'url' => array('/contactform/main/index')),
			array('label' => '|'),
			array(
				'label' => tt('Reserve apartment', 'common'),
				'url' => array('/booking/main/mainform'),
				'visible' => Yii::app()->user->getState('isAdmin') === null,
				'linkOptions' => array('class' => 'fancy'),
			),
			array('label' => '|', 'visible' => Yii::app()->user->getState('isAdmin') === null),
			array(
				'label' => Yii::t('common', 'Control panel'),
				'url' => array('/usercpanel/main/index'),
				'visible' => Yii::app()->user->getState('isAdmin') === null
			),
			array('label' => '|', 'visible' => Yii::app()->user->getState('isAdmin') === null && !Yii::app()->user->isGuest),
			array('label' => tt('Logout', 'common'), 'url' => array('/site/logout'), 'visible' => !Yii::app()->user->isGuest),
		);

		$this->aData['topMenuItems'] = $this->infoPages;
		parent::init();
	}

	public static function disableProfiler() {
		if (Yii::app()->getComponent('log')) {
			foreach (Yii::app()->getComponent('log')->routes as $route) {
				if (in_array(get_class($route), array('CProfileLogRoute', 'CWebLogRoute', 'YiiDebugToolbarRoute'))) {
					$route->enabled = false;
				}
			}
		}
	}
}