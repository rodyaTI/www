<?php $baseUrl = Yii::app()->request->baseUrl; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="language" content="en"/>

    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <meta name="description" content="<?php echo CHtml::encode($this->pageDescription); ?>"/>
    <meta name="keywords" content="<?php echo CHtml::encode($this->pageKeywords); ?>"/>

    <link media="screen" type="text/css" href="<?php echo $baseUrl; ?>/css/admin-styles.css" rel="stylesheet"/>

    <!--[if IE]> <link href="<?php echo $baseUrl; ?>/css/ie.css" rel="stylesheet" type="text/css"> <![endif]-->
    <link rel="icon" href="<?php echo $baseUrl; ?>/favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="<?php echo $baseUrl; ?>/favicon.ico" type="image/x-icon"/>
</head>

<body id="top">
<div id="fb-root"></div>

<?php
    $this->widget('bootstrap.widgets.BootNavbar', array(
        'fixed' => 'top',
        'brand' => '<img alt="'. CHtml::encode($this->pageDescription). '" src="'. Yii::app()->request->baseUrl .'/images/pages/logo-open-re-admin.png" id="logo">',
        'brandUrl' => $baseUrl.'/',
        'collapse' => false, // requires bootstrap-responsive.css
        'items' => array(
            array(
                'class' => 'bootstrap.widgets.BootMenu',
                'items' => array(
                    array('label' => tc('Administrator panel'), 'url' => '#', 'active' => true),
                    array('label' => tc('Menu'), 'url' => '#', 'items' => $this->infoPages),
                ),
            ),
            //'<form class="navbar-search pull-left" action=""><input type="text" class="search-query span2" placeholder="Search"></form>',
            array(
                'class' => 'bootstrap.widgets.BootMenu',
                'htmlOptions' => array('class' => 'pull-right'),
                'items' => array(
                    array('label' => tc('Log out'), 'url' => $baseUrl . '/site/logout'),
                ),
            ),
        ),
    ));

    $countApartmentModeration = Apartment::getCountModeration();
    $bageListings = ( $countApartmentModeration > 0 ) ? "&nbsp<span class=\"badge\">{$countApartmentModeration}</span>" : '';

    $countCommentPending = Comment::getCountPending();
    $bageComments = ( $countCommentPending > 0 ) ? "&nbsp<span class=\"badge\">{$countCommentPending}</span>" : '';
?>

<div class="bootnavbar-delimiter"></div>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span3">
            <div class="well sidebar-nav">
                <?php $this->widget('bootstrap.widgets.BootMenu', array(
                'type' => 'list',
                'encodeLabel' => false,
                'items' => array(
                    array('label' => tc('Listings')),
                    array('label' => tc('Listings') . $bageListings, 'icon' => 'icon-list-alt', 'url' => $baseUrl . '/apartments/backend/main/admin', 'active' => isActive('apartments')),
                    array('label' => tc('New Listing'), 'icon' => 'icon-plus-sign', 'url' => $baseUrl . '/apartments/backend/main/create', 'active' => isActive('apartments.create')),
                    array('label' => tc('Comments') . $bageComments, 'icon' => 'icon-list-alt', 'url' => $baseUrl . '/comments/backend/main/admin', 'active' => isActive('comments')),

					array('label' => tc('Users')),
					array('label' => tc('Users'), 'icon' => 'icon-list-alt', 'url' => $baseUrl . '/users/backend/main/admin', 'active' => isActive('users')),
                    //array('label' => tt('Add user', 'users'), 'icon' => 'icon-plus-sign', 'url' => $baseUrl . '/users/backend/main/create', 'active' => isActive('users.create')),

                    array('label' => tc('Content')),
                    array('label' => tc('News'), 'icon' => 'icon-file', 'url' => $baseUrl . '/news/backend/main/admin', 'active' => isActive('news')),
                    array('label' => tc('Manage Menu'), 'icon' => 'icon-file', 'url' => $baseUrl . '/menumanager/backend/main/admin', 'active' => isActive('menumanager')),
                    array('label' => tc('Manage FAQ'), 'icon' => 'icon-file', 'url' => $baseUrl . '/articles/backend/main/admin', 'active' => isActive('articles')),

                    array('label' => tc('References')),
                    array('label' => tc('Categories of references'), 'icon' => 'icon-asterisk', 'url' => $baseUrl . '/referencecategories/backend/main/admin', 'active' => isActive('referencecategories')),
                    array('label' => tc('Values of references'), 'icon' => 'icon-asterisk', 'url' => $baseUrl . '/referencevalues/backend/main/admin', 'active' => isActive('referencevalues')),
                    array('label' => tc('Reference (window to..)'), 'icon' => 'icon-asterisk', 'url' => $baseUrl . '/windowto/backend/main/admin', 'active' => isActive('windowto')),
                    array('label' => tc('References "Check-in"'), 'icon' => 'icon-asterisk', 'url' => $baseUrl . '/timesin/backend/main/admin', 'active' => isActive('timesin')),
                    array('label' => tc('References "Check-out"'), 'icon' => 'icon-asterisk', 'url' => $baseUrl . '/timesout/backend/main/admin', 'active' => isActive('timesout')),
                    array('label' => tc('References "Property Type"'), 'icon' => 'icon-asterisk', 'url' => $baseUrl . '/apartmentObjType/backend/main/admin', 'active' => isActive('apartmentObjType')),
                    array('label' => tc('References "City"'), 'icon' => 'icon-asterisk', 'url' => $baseUrl . '/apartmentCity/backend/main/admin', 'active' => isActive('apartmentCity')),

                    array('label' => tc('Settings')),
                    array('label' => tc('Settings'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/configuration/backend/main/admin', 'active' => isActive('configuration')),
                    array('label' => tc('Change admin password'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/adminpass/backend/main/index', 'active' => isActive('adminpass')),
					array('label' => tc('Seo settings'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/seo/backend/main/admin', 'active' => isActive('seo')),
					array('label' => tc('Service site'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/service/backend/main/admin', 'active' => isActive('service'), 'visible' => issetModule('service')),
					array('label' => tc('Social settings'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/socialauth/backend/main/admin', 'active' => isActive('socialauth'), 'visible' => issetModule('socialauth')),
                    array('label' => tc('Translation management'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/translateMessage/backend/main/admin', 'active' => isActive('translateMessage')),

                    //array('label' => tc('Modules')),
                    //array('label' => tc('Slider control'), 'icon' => 'icon-circle-arrow-right', 'url' => $baseUrl . '/slider/backend/main/admin', 'active' => isActive('slider')),
                    //array('label' => tc('Import / Export'), 'icon' => 'icon-circle-arrow-right', 'url' => $baseUrl . '/iecsv/backend/main/admin', 'active' => isActive('iecsv')),
					//array('label' => tc('Advertising module'), 'icon' => 'icon-circle-arrow-right', 'url' => $baseUrl . '/advertising/backend/advert/admin', 'active' => isActive('advertising')),

                    array('label' => tc('Other')),
   	                array('label' => tc('Product news'), 'icon' => 'icon-home', 'url' => $baseUrl . '/news/backend/main/product', 'active' => isActive('news.product')),
                ),
            )); ?>
            </div>
            <!--/.well -->
        </div>
        <!--/span-->
        <div class="span9">
            <?php echo $content; ?>
        </div>
        <!--/span-->
    </div><!--/row-->

    <hr>

    <footer>
        <p>&copy;&nbsp;<?php echo CHtml::encode(Yii::app()->name).', '.date('Y'); ?></p>
    </footer>

    <div id="loading" style="display:none;"><?php echo Yii::t('common', 'Loading content...'); ?></div>
<?php
    Yii::app()->clientScript->registerCoreScript('jquery');
    Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/jquery.dropdownPlain.js', CClientScript::POS_END);
    Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/adminCommon.js', CClientScript::POS_END);
	Yii::app()->clientScript->registerScript('loading', '
        $("#loading").bind("ajaxSend", function(){
            $(this).show();
        }).bind("ajaxComplete", function(){
            $(this).hide();
        });
    ', CClientScript::POS_READY);

    Yii::app()->clientScript->registerScript('focusSubmit', '
        function focusSubmit(elem) {
            elem.keypress(function(e) {
                if(e.which == 13) {
                    $(this).blur();
                    $("#btnleft").focus().click();
                }
            });
        }
    ', CClientScript::POS_END);

    $this->widget('application.modules.fancybox.EFancyBox', array(
            'target' => 'a.fancy',
            'config' => array(
                'ajax' => array('data' => "isFancy=true"),
            ),
        )
    );

    Yii::app()->clientScript->registerScript('fancybox', '
	$("a.fancy").fancybox({
		"ajax":{
			"data": {"isFancy":"true"}
		}
	});', CClientScript::POS_READY);
?>
</div><!--/.fluid-container-->

</body>
</html>