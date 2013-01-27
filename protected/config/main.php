<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.

require_once( dirname(__FILE__) . '/../helpers/common.php');
require_once( dirname(__FILE__) . '/../helpers/strings.php');

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'re.monoray.ru',

    'sourceLanguage' => 'en',
    'language' => 'ru',

	'preload'=>array(
		'log',
		'configuration', // preload configuration
	),

	// autoloading model and component classes
	'import'=>array(
		'ext.eoauth.*',
		'ext.eoauth.lib.*',
		'ext.lightopenid.*',
		'ext.eauth.*',
		'ext.eauth.services.*',
		'ext.eauth.custom_services.CustomGoogleService',
		'ext.eauth.custom_services.CustomVKService',
		'ext.eauth.custom_services.CustomFBService',
		'ext.eauth.custom_services.CustomTwitterService',

		'application.models.*',
		'application.components.*',

		'application.modules.configuration.components.*',
		'application.modules.notifier.components.Notifier',
		'application.modules.booking.models.*',
		'application.modules.lang.models.Lang',

		'application.modules.comments.models.Comment', // TODO
		'application.modules.windowto.models.WindowTo',
		'application.modules.apartments.models.*',
		'application.modules.news.models.*',
		'application.extensions.image.Image',
		'application.modules.selecttoslider.models.SelectToSlider',
		'application.modules.similarads.models.SimilarAds',
		'application.modules.menumanager.models.Menu',
		'application.modules.windowto.models.WindowTo',
		'application.modules.apartments.components.*',
		'application.modules.apartmentCity.models.ApartmentCity',
		'application.modules.apartmentObjType.models.ApartmentObjType',
		'application.modules.translateMessage.models.TranslateMessage',
		'application.components.behaviors.ERememberFiltersBehavior',
		'application.modules.seo.models.Seo',
		'application.modules.service.models.Service',
		'application.modules.socialauth.models.SocialauthModel',
        'application.modules.antispam.components.MathCCaptchaAction',
	),

	'modules'=>array(
		'news',
		'referencecategories',
		'referencevalues',
		'apartments',
		'apartmentObjType',
        'apartmentCity',
		'comments',
		'booking',
		'windowto',
		'contactform',
		'articles',
		'usercpanel',
		'users',
		'quicksearch',
		'configuration',
		'timesin',
		'timesout',
		'adminpass',
		'specialoffers',
		'install',
		'viewpdf',
		'selecttoslider',
		'similarads',
		'menumanager',
		'userads',
		'lang',
        'translateMessage',
		'seo',
		'service',
		'socialauth',
        'antispam',

		// uncomment the following to enable the Gii tool
		/*'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'admin1',
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
            'generatorPaths'=>array(
                'bootstrap.gii', // since 0.9.1
            ),
		),*/

	),

	'controllerMap' => array( 'photo_upload' => 'application.modules.editor.RedactorController', ),

	// application components
	'components'=>array(
		'loid' => array(
			'class' => 'application.extensions.lightopenid.loid',
		),
		'eauth' => array(
			// yii-eauth-1.1.8
			'class' => 'ext.eauth.EAuth',
			'popup' => true, // Use popup windows instead of redirect to site of provider
		),

		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),

		'configuration' => array(
			'class' => 'Configuration',
			'cachingTime' => 60*60*24*180, // caching configuration for 180 days
		),

		/*'clientScript' => array(
			'class' => 'ext.minify.EClientScript',
			'combineScriptFiles' => true, // By default this is set to false, set this to true if you'd like to combine the script files
			'combineCssFiles' => true, // By default this is set to false, set this to true if you'd like to combine the css files
			'optimizeScriptFiles' => false,	// @since: 1.1
			'optimizeCssFiles' => false,	// @since: 1.1
			'cssForIgnore' => array('bootstrap.min.css', 'jquery-ui-1.7.1.custom.css', 'jquery-ui.multiselect.css',
				'bootstrap-responsive.min.css'),
			'scriptsForIgnore' => array('jquery.js', 'jquery.min.js', 'jquery.ui.js', 'jquery-ui.min.js',
				'bootstrap.min.js', 'jquery-ui-i18n.min.js', 'jquery.jcarousel.min.js'),
		),*/

		'cache'=>array(
			'class'=>'system.caching.CFileCache',
		),

		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName' => false,
			'class'=>'application.components.CustomUrlManager',
			'rules'=>array(
                '/' => 'site/index',
				'sitemap.xml'=>'sitemap/main/viewxml',

                'property/<id:\d+>'=>'apartments/main/view',
                'news'=>'news/main/index',
                'news/<id:\d+>'=>'news/main/view',
                'faq'=>'articles/main/index',
                'faq/<id:\d+>'=>'articles/main/view',
                'contact-us'=>'contactform/main/index',
                'specialoffers'=>'specialoffers/main/index',
                'page/<id:\d+>'=>'menumanager/main/view',

                'service-<serviceId:\d+>' => 'quicksearch/main/mainsearch',

                '<controller:(quicksearch|specialoffers)>/main/index' => '<controller>/main/index',
                '<_m>/<_c>/<_a>' => '<_m>/<_c>/<_a>',
                '<_c>/<_a>' => '<_c>/<_a>',
                '<module:\w+>/backend/<controller:\w+>/<action:\w+>'=>'<module>/backend/<controller>/<action>', // CGridView ajax
			),
		),

        'db'=>require(dirname(__FILE__) . '/db.php'),

		'errorHandler'=>array(
            'errorAction'=>'site/error',
        ),
/*		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'ext.yii-debug-toolbar.YiiDebugToolbarRoute',
					'ipFilters'=>array('127.0.0.1'),
				),
			),
		),*/
        'messages'=>array(
            'class'=>'DbMessageSource',
            'forceTranslation'=>true,
        ),

        'bootstrap'=>array(
            'class'=>'ext.bootstrap.components.Bootstrap', // assuming you extracted bootstrap under extensions
        ),
	),

	'params'=>array(

	),
);