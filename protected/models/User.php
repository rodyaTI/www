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

class User extends ParentModel {
	private static $_saltAddon = 'openre';
	public $password_repeat;
	public $old_password;
	public $verifyCode;
	public $activateLink;

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{users}}';
	}

    public function relations() {
        $relation = array();
        return $relation;
    }

	public function rules() {
		return array(
			array('username, password, salt, email', 'length', 'max' => 128),
			array('phone', 'length', 'max' => 15),
			array('email, phone, username', 'required', 'on' => 'usercpanel'),
			array($this->i18nRules('additional_info'), 'safe', 'on' => 'usercpanel'),
			//array('username', 'safe', 'on' => 'usercpanel'),
			array('password, password_repeat', 'required', 'on' => 'changePass, changeAdminPass'),
			array('password', 'compare', 'on' => 'changePass, backend, changeAdminPass',
				'message' => tt('Passwords are not equivalent! Try again.', 'usercpanel')),
			array('password_repeat', 'safe'),
			array('password', 'length', 'min' => 6, 'on' => 'changePass, backend, changeAdminPass',
				'tooShort' => tt('Password too short! Minimum allowed length is 6 chars.', 'usercpanel')
			),

			array('username, email, password, password_repeat, phone', 'required', 'on' => 'backend'),
			array($this->i18nRules('additional_info'), 'safe', 'on' => 'backend'),
			//array('username', 'safe', 'on' => 'backend'),
			array('email, phone, username', 'required', 'on' => 'update'),
			array($this->i18nRules('additional_info'), 'safe', 'on' => 'update'),
			array('email', 'email'),
			array('email', 'unique'),

			array('old_password', 'required', 'on' => 'changeAdminPass'),

			array('username, email, verifyCode, phone', 'required', 'on' => 'register'),
			array('active', 'safe'),
		);
	}

	public function i18nFields(){
        return array(
            'additional_info' => 'text not null',
        );
    }

	public function attributeLabels() {
		$return = array(
			'id' => 'Id',
			'username' => tt('Your name', 'usercpanel'),
			'password' => 'Password',
			'password_repeat' => tt('Repeat password','usercpanel'),
			'old_password' => tt('Current administrator password', 'adminpass'),
			'email' => 'Email',
			'phone' => Yii::t('common', 'Your phone number'),
			'Login (email)' => Yii::t('common', 'Login (email)'),
			'verifyCode' => Yii::t('common', 'Verify Code'),
			'additional_info' => tt('Additional info', 'usercpanel'),
		);
		if($this->scenario == 'changePass' || $this->scenario == 'changeAdminPass'){
			$return['password'] = tt('Enter new password', 'usercpanel');
		}
		if($this->scenario == 'usercpanel'){
			$return['email'] = tt('Your e-mail', 'usercpanel');
		}
		if($this->scenario == 'backend' || $this->scenario == 'update'){
			$return['email'] = tt('E-mail', 'users');
			$return['username'] = tt('User name', 'users');
			$return['password'] = tt('Password', 'users');
			$return['phone'] = Yii::t('common', 'Phone number');
		}

		return $return;
	}

	/**
	 * Checks if the given password is correct.
	 * @param string the password to be validated
	 * @return boolean whether the password is valid
	 */
	public function validatePassword($password) {
		return self::hashPassword($password, $this->salt) === $this->password;
	}

	/**
	 * Generates the password hash.
	 * @param string password
	 * @param string salt
	 * @return string hash
	 */
	public static function hashPassword($password, $salt) {
		return md5($salt . $password . $salt . self::$_saltAddon);
	}

	/**
	 * Generates a salt that can be used to generate a password hash.
	 * @return string the salt
	 */
	public static function generateSalt() {
		return uniqid('', true);
	}

	public function setPassword($password = null){
		$this->salt = self::generateSalt();
		if($password == null){
			$password = $this->password;
		}
		$this->password = md5($this->salt . $password . $this->salt . self::$_saltAddon);
	}

	public function randomString($length = 10){
		$chars = array_merge(range(0,9), range('a','z'), range('A','Z'));
		shuffle($chars);
		return implode('', array_slice($chars, 0, $length));
	}

	public function search(){
		$criteria=new CDbCriteria;

		$criteria->compare('username',$this->username,true);
		$criteria->compare('email',$this->email,true);
        $criteria->compare('phone',$this->phone,true);

		if ($this->active != 'all')
		    $criteria->compare('active', $this->active);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function afterDelete(){
		// need to save rating
		//$sql = 'DELETE FROM {{apartment_comments}} WHERE email="'.$this->email.'"';
		//Yii::app()->db->createCommand($sql)->execute();

		$sql = 'DELETE FROM {{users_social}} WHERE user_id="'.$this->id.'"';
		Yii::app()->db->createCommand($sql)->execute();

		$sql = 'UPDATE {{apartment}} SET owner_id=1, owner_active=:active, active=:inactive WHERE owner_id=:userId';
		Yii::app()->db->createCommand($sql)->execute(array(
			':active' => Apartment::STATUS_ACTIVE,
			':inactive' => Apartment::STATUS_INACTIVE,
			':userId' => $this->id,
		));

		return parent::afterDelete();
	}

	public function beforeSave() {
//		foreach (Lang::getActiveLangs() as $key => $item) {
//			$additionalInfo = 'additional_info_'.$item;
//			if (isset($this->$additionalInfo) && !empty($this->$additionalInfo)) {
//				$this->$additionalInfo = nl2br($this->$additionalInfo);
//			}
//		}
		return parent::beforeSave();
	}

	public function getAdditionalInfo(){
        return $this->getStrByLang('additional_info');
    }

	public static function getRandomEmail(){
		$email = self::getRandomWord(8)."@null.io";
		return $email;
	}

	public static function getIdByUid($uid = false, $service = false) {
		$id = false;
		if ($uid) {
			$serviceCond = '';
			if ($service) { $serviceCond = ' AND service = "'.$service.'" '; }
			$id = Yii::app()->db->createCommand()
						->select('user_id')
						->from('{{users_social}}')
						->where('uid = "'.$uid.'" '.$serviceCond.'')
						->queryScalar();
		}
		return $id;
	}

	public static function setSocialUid($user_id, $uid, $service = '') {
		if ($user_id && $uid) {
			Yii::app()->db->createCommand()
					->insert('{{users_social}}', array(
						'user_id' => $user_id,
						'uid' => $uid,
						'service' => $service
					));
			return true;
		}
		return false;
	}

	public static function getRandomWord($size = 0){
		$word = md5(microtime(true));
		if (!$size)
			return $word;
		$subword = substr($word, $size*-1);
		return $subword;
	}

	static function getAdminName(){
		$sql = 'SELECT username FROM {{users}} WHERE isAdmin=1 LIMIT 1';
		return Yii::app()->db->createCommand($sql)->queryScalar();
	}
}