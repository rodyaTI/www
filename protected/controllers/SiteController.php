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

class SiteController extends Controller {
	public $cityActive;

	public function actions() {
		return array(
			'captcha' => array(
				'class' => 'MathCCaptchaAction',
				'backColor' => 0xFFFFFF,
			),
		);
	}

	public function accessRules(){
        return array(
            array('allow',
                'users'=>array('*'),
            ),
            array('allow',
                'actions'=>array('viewreferences'),
                'expression' => 'Yii::app()->user->getState("isAdmin")',
            ),
        );
    }

	public function init(){
		parent::init();
		$this->cityActive = SearchForm::cityInit();
	}

	public function actionIndex() {
		//$dependency = new CDbCacheDependency('SELECT date_updated FROM {{menu}} WHERE id = "1"');
		$page = Menu::model()->/*cache(param('cachingTime', 1209600), $dependency)->*/findByPk(1);

        if(isset($_POST['is_ajax'])){
            $this->renderPartial('index', array('page' => $page), false, true);
        }else{
            $this->render('index', array('page' => $page));
        }
    }

	public function actionError() {
		if ($error = Yii::app()->errorHandler->error) {
			if (Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	public function actionLogin() {
		$model = new LoginForm;

		if (Yii::app()->request->getQuery('soc_error_save'))
			Yii::app()->user->setFlash('error', tt('Error saving data. Please try again later.', 'socialauth'));
		if (Yii::app()->request->getQuery('deactivate'))
			showMessage(tc('Login'), tt('Your account not active. Administrator deactivate your account.', 'socialauth'), null, true);

		$service = Yii::app()->request->getQuery('service');
		if (isset($service)) {
			$authIdentity = Yii::app()->eauth->getIdentity($service);
			$authIdentity->redirectUrl = Yii::app()->user->returnUrl;
			$authIdentity->cancelUrl = $this->createAbsoluteUrl('site/login');

			if ($authIdentity->authenticate()) {
				$identity = new EAuthUserIdentity($authIdentity);

				// успешная авторизация
				if ($identity->authenticate()) {
					//Yii::app()->user->login($identity);

					$uid = $identity->id;
					$firstName = $identity->firstName;
					$email = $identity->email;
					$service = $identity->serviceName;
					$mobilePhone = $identity->mobilePhone;
					$homePhone = $identity->homePhone;
					$isNewUser = false;

					$existId = User::getIdByUid($uid, $service);

					if (!$existId) {
						$isNewUser = true;
						$email = (!$email) ? User::getRandomEmail() : $email;
						$phone = '';
						if ($mobilePhone)
							$phone = $mobilePhone;
						elseif ($homePhone)
							$phone = $homePhone;

						$user = $this->createUser($email, $firstName, $phone, '', true);

						if (!$user && isset($user['id'])) {
							$authIdentity->redirect(Yii::app()->createAbsoluteUrl('/site/login').'?soc_error_save=1');
						}

						$success = User::setSocialUid($user['id'], $uid, $service);

						if (!$success) {
							User::model()->findByPk($user['id'])->delete();
							$authIdentity->redirect(Yii::app()->createAbsoluteUrl('/site/login').'?soc_error_save=1');
						}

						$existId = User::getIdByUid($uid, $service);
					}

					if ($existId) {
						$result = $model->loginSocial($existId);
						if ($result){
	//						Yii::app()->user->clearState('id');
	//						Yii::app()->user->clearState('first_name');
	//						Yii::app()->user->clearState('nickname');
							if ($result === 'deactivate')
								$authIdentity->redirect(Yii::app()->createAbsoluteUrl('/site/login').'?deactivate=1');
							if ($isNewUser)
								$authIdentity->redirect(Yii::app()->createAbsoluteUrl('/usercpanel/main/index').'?soc_success=1');
							else
								$authIdentity->redirect(Yii::app()->createAbsoluteUrl('/usercpanel/main/index'));
						}
					}
					// специальное перенаправления для корректного закрытия всплывающего окна
					$authIdentity->redirect();
				}
				else {
					// закрытие всплывающего окна и перенаправление на cancelUrl
					$authIdentity->cancel();
				}
			}

			// авторизация не удалась, перенаправляем на страницу входа
			$this->redirect(array('site/login'));
		}

		if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		if (isset($_POST['LoginForm'])) {
			$model->attributes = $_POST['LoginForm'];
			if ($model->validate() && $model->login()){
				if(Yii::app()->user->getState('isAdmin')){
                    NewsProduct::getProductNews();
					$this->redirect(array('/apartments/backend/main/admin'));
					Yii::app()->end();
				}

				if(Yii::app()->user->isGuest){
					$this->redirect(Yii::app()->user->returnUrl);
				}
				else{
					if(!Yii::app()->user->getState('returnedUrl')){
						$this->redirect(array('/usercpanel/main/index'));
					}
					else{
						$this->redirect(Yii::app()->user->getState('returnedUrl'));
					}
				}
			}
		}
		$this->render('login', array('model' => $model));
	}

	public function actionLogout() {
		Yii::app()->user->logout();

		if (isset(Yii::app()->request->cookies['itemsSelectedImport']))
		    unset(Yii::app()->request->cookies['itemsSelectedImport']);

		if (isset(Yii::app()->request->cookies['itemsSelectedExport']))
		    unset(Yii::app()->request->cookies['itemsSelectedExport']);

		if (isset(Yii::app()->session['importAds']))
			unset(Yii::app()->session['importAds']);

		$this->redirect(Yii::app()->homeUrl);
	}

	public function actionViewreferences(){
		$this->layout = '//layouts/admin';
		$this->render('view_reference');
	}

	public function actionRecover() {
		$modelRecover = new RecoverForm;

		if (isset($_POST['ajax']) && $_POST['ajax'] === 'recover-form') {
			echo CActiveForm::validate($modelRecover);
			Yii::app()->end();
		}

		if (isset($_POST['RecoverForm'])) {
			$modelRecover->attributes = $_POST['RecoverForm'];

			if ($modelRecover->validate()){
				//$model = new User;
				//$model->attributes = $_POST['RecoverForm'];
				$model = User::model()->findByAttributes(array('email' => $modelRecover->email));

				if($model !== null ){
					$password = $model->randomString();

					// set salt pass
					$model->setPassword($password);
					// set new password in db
					$model->update(array('password', 'salt'));

					$model->password = $password;

					// send email
					$notifier = new Notifier;
					$notifier->raiseEvent('onRecoveryPassword', $model, $model->id);

					showMessage(Yii::t('common', 'Recover password'), Yii::t('common', 'New password is saved and send to {email}.', array('{email}' => $modelRecover->email)));
				} else {
					showMessage(Yii::t('common', 'Recover password'), Yii::t('common', 'User does not exist'));
				}
			}
		}
		$this->render('recover', array('model' => $modelRecover));
	}

	public function actionRegister() {
		if (Yii::app()->user->isGuest && param('useUserads')) {
			$model = new User('register');

			if(isset($_POST['User'])) {
				$model->attributes = $_POST['User'];
				if($model->validate()) {
					$activateKey = $this->generateActivateKey();
					$user = $this->createUser($model->email, $model->username, $model->phone, $activateKey);

					if ($user) {
						$model->id = $user['id'];
						$model->password = $user['password'];
						$model->email = $user['email'];
						$model->username = $user['username'];
						$model->activatekey = $user['activateKey'];
						$model->activateLink = $user['activateLink'];

						$notifier = new Notifier;
						$notifier->raiseEvent('onRegistrationUser', $model, $model->id);
						showMessage(Yii::t('common', 'Registration'), Yii::t('common', 'You were successfully registered. The letter for account activation has been sent on {useremail}', array('{useremail}' => $user['email'])));
					}
					else {
						showMessage(Yii::t('common', 'Registration'), Yii::t('common', 'Error. Repeat attempt later'));
					}
				}
                else {
                    $model->unsetAttributes(array('verifyCode'));
                }
			}
			$this->render('register', array('model'=>$model));
		} else {
			$this->redirect('index');
		}
	}

	public function generateActivateKey() {
		return md5(uniqid());
	}

	public function createUser($email, $username = '', $phone = '', $activateKey = '', $isActive = false) {
		$model = new User;
		$model->email = $email;
		if ($username)
			$model->username = $username;
		if ($phone)
			$model->phone = $phone;
		if ($isActive)
 			$model->active = 1;
		if ($activateKey)
 			$model->activatekey = $activateKey;

		$password = $model->randomString();
		$model->setPassword($password);

		$return = array();

		if($model->save()){
			$return = array(
				'email' => $model->email,
				'username' => $model->username,
				'password' => $password,
				'id' => $model->id,
				'active' => $model->active,
				'activateKey' => $activateKey,
				'activateLink' => Yii::app()->createAbsoluteUrl('/site/activation?key='.$activateKey),
			);
		}
		return $return;
	}

	public function actionActivation() {
		$key = Yii::app()->request->getParam('key');
		if ($key) {
			$user = User::model()->find('activatekey = :activatekey',
				array(':activatekey' => $key));

			if(!empty($user)) {
				if($user->active == '1') {
					showMessage(Yii::t('common', 'Activate account' ), Yii::t('common', 'Your status account already is active'));
				}
				else {
					$user->active = '1';
					//$user->activatekey = '';
					//$user->save();
					$user->update(array('active'));
					showMessage(Yii::t('common', 'Activate account' ), Yii::t('common', 'Your account successfully activated'));
				}
			} else {
				throw new CHttpException(403, Yii::t('common', 'User not exists'));
			}
		}
		else
			$this->redirect('/site/index');
	}
}