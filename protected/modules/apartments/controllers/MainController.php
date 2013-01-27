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

class MainController extends ModuleUserController {

	public $modelName = 'Apartment';

	public function actions() {
		return array(
			'captcha' => array(
				'class' => 'MathCCaptchaAction',
				'backColor' => 0xFFFFFF,
			),
		);
	}

	public function actionView($id) {
		// если админ - делаем редирект на просмотр в админку
		if(Yii::app()->user->getState('isAdmin')){
			$this->redirect(array('backend/main/view', 'id' => $id));
		}

		// Был ли отправлен комментарий? обрабатываем
		$comment = new Comment;
		if (isset($_POST['Comment'])) {
			$comment->attributes = $_POST['Comment'];

			$comment->apartment_id = $id;

			if(!Yii::app()->user->isGuest){
				$comment->username = Yii::app()->user->username;
				$comment->email = Yii::app()->user->email;
			}

            if ($comment->validate()) {
                if ($comment->save(false)) {
                    if ($comment->active == Comment::STATUS_PENDING){
                        Yii::app()->user->setFlash(
                            'newComment',
                            Yii::t('module_comments','Thank you for your comment. Your comment will be posted once it is approved.')
                        );
                    }
                }
            }
            else {
                $comment->unsetAttributes(array('verifyCode'));
            }
		}
		
		// "Толстый" запрос из-за JOIN'ов. Кешируем его.
		// Зависимость кеша - выбираем дату последней модификации из 4 таблиц
		$apartment = Apartment::model()
			->cache(param('cachingTime', 1209600), Apartment::getFullDependency($id))
			->with('windowTo', 'comments', 'images', 'objType', 'city')
			->findByPk($id);

        if (!$apartment)
            throw404();

		if( $apartment->owner_id != 1 && $apartment->owner_active == Apartment::STATUS_INACTIVE) {
			if (!(isset(Yii::app()->user->id ) && Yii::app()->user->id == $apartment->owner_id) && !Yii::app()->user->getState('isAdmin')) {
				Yii::app()->user->setFlash('notice', tt('apartments_main_index_propertyNotAvailable', 'apartments'));
				throw404();
			}
		}

		if(($apartment->active == Apartment::STATUS_INACTIVE || $apartment->active == Apartment::STATUS_MODERATION)
		&& !Yii::app()->user->getState('isAdmin') 
		&& !(isset(Yii::app()->user->id ) && Yii::app()->user->id == $apartment->owner_id)){
			Yii::app()->user->setFlash('notice', tt('apartments_main_index_propertyNotAvailable', 'apartments'));
			//$this->redirect(Yii::app()->homeUrl);
			throw404();
		}
		
		$dateFree = CDateTimeParser::parse($apartment->is_free_to, 'yyyy-MM-dd');
		if($dateFree && $dateFree < (time()-60*60*24)){
			$apartment->is_special_offer = 0;
			$apartment->update(array('is_special_offer'));
		}

		if($apartment === null){
			if($comment->apartment_id){
				$comment->delete();
			}
			throw new CHttpException(404,'The requested page does not exist.');
		}

		// попытка комментария к несуществующему объявлению?
		if($comment->apartment_id && $apartment->id != $comment->apartment_id){
			$comment->delete();
		}
		
		$ipAddress = Yii::app()->request->userHostAddress;
		$userAgent = Yii::app()->request->userAgent;	
		Apartment::setApartmentVisitCount($id, $ipAddress, $userAgent);
		
		$this->render('view', array(
			'model' => $apartment,
			'comment' => $comment,
			'statistics' => Apartment::getApartmentVisitCount($id),
		));
	
	}

	public function actionGmap($id, $model = null){
		if($model === null){
			$model = $this->loadModel($id);
		}
		$result = CustomGMap::actionGmap($id, $model, $this->renderPartial('backend/_marker', array('model' => $model), true));

		if($result){
			return $this->renderPartial('backend/_gmap', $result, true);
		}
		return '';
	}
	
	public function actionYmap($id, $model = null){
		if($model === null){
			$model = $this->loadModel($id);
		}
		$result = CustomYMap::init()->actionYmap($id, $model, $this->renderPartial('backend/_marker', array('model' => $model), true));

		if($result){
			//return $this->renderPartial('backend/_ymap', $result, true);
		}
		return '';
	}
	
	public function actionGeneratePhone($id = null) {
		if ($id && param('useShowUserInfo')) {
			$apartmentInfo = Apartment::model()->findByPk($id, array('select' => 'owner_id'));

			if ($apartmentInfo->owner_id)
				$userInfo = User::model()->findByPk($apartmentInfo->owner_id, array('select' => 'phone'));
			
			if ($userInfo->phone) {
				$image = imagecreate(240, 20);

				$bg = imagecolorallocate($image, 255, 255, 255);
				$textcolor = imagecolorallocate($image, 37, 75, 137);

				imagestring($image, 5, 0, 0, $userInfo->phone, $textcolor);

				header('Pragma: public');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Content-Transfer-Encoding: binary');
				header("Content-type: image/png");
				imagepng($image);
				echo $image;
				imagedestroy($image);
			}
		}
	}
}