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

class Booking extends CFormModel {
	public $apartment_id;
	public $user_id;
	public $date_start;
	public $date_end;
	public $time_in;
	public $time_out;
	public $status;
	public $sum_rur;
	
	public $username;
	public $comment;
	public $useremail;
	public $useremailSearch;
	public $tostatus;
	public $ownerEmail;
	
	public $phone;
	public $email;
	public $dateCreated;
	public $password;
	
	public $time_inVal;
	public $time_outVal;
	
	public $activatekey;
	public $activateLink;

	public static function getYiiDateFormat() {
		$return = 'MM/dd/yyyy';
		if (Yii::app()->language == 'ru') {
			$return = 'dd.MM.yyyy';
		}
		return $return;
	}

	public function rules() {
		return array(
			array('date_start, date_end, time_in, time_out, ' . (Yii::app()->user->isGuest ? 'useremail, phone, username' : ''), 'required', 'on' => 'bookingform'),
			array('status, time_in, time_out, sum_rur', 'numerical', 'integerOnly' => true),
			array('useremail, username, comment', 'safe'),
			array('useremail', 'email'),
			array('date_start, date_end', 'date', 'format' => self::getYiiDateFormat(), 'on' => 'bookingform'),
			array('date_start, date_end', 'myDateValidator', 'on' => 'bookingform'),
			array('useremail', 'myUserEmailValidator', 'on' => 'bookingform'),
			array('useremail, username', 'length', 'max' => 128),
			//array('phone', 'required', 'on' => 'bookingform'),
			array('date_start, date_end, date_created, status, useremailSearch, apartment_id, id', 'safe', 'on' => 'search'),

			array('sum_rur', 'mySumValidator', 'on' => 'view'),
		);
	}

	public function mySumValidator($param){
		if($param == 'sum_rur'){
			if($this->sum_rur != intval($this->sum_rur) || !$this->sum_rur){
				$this->addError('sum_rur', Yii::t('module_booking', 'Incorrect booking price.'));
			}
		}
	}

	public function myUserEmailValidator() {
		if (Yii::app()->user->isGuest) {
			$model = User::model()->findByAttributes(array('email' => $this->useremail));
			if ($model) {
				$this->addError('useremail',
					Yii::t('module_booking', 'User with such e-mail already registered. Please <a title="Login" href="{n}">login</a> and try again.',
						Yii::app()->createUrl('/site/login')));
			}
		}
	}

	public function myDateValidator($param) {
		$dateStart = CDateTimeParser::parse($this->date_start, self::getYiiDateFormat()); // format to unix timestamp
		$dateEnd = CDateTimeParser::parse($this->date_end, self::getYiiDateFormat()); // format to unix timestamp

		if ($param == 'date_start' && $dateStart < CDateTimeParser::parse(date('Y-m-d'), 'yyyy-MM-dd')) {
			$this->addError('date_start', tt('Wrong check-in date', 'booking'));
		}
		if ($param == 'date_end' && $dateEnd <= $dateStart) {
			$this->addError('date_end', tt('Wrong check-out date', 'booking'));
		}
		
		if(issetModule('bookingcalendar')) {
			$result = Yii::app()->db->createCommand()
				->select('id')
				->from('{{booking_calendar}}')
				->where('apartment_id = "'.$this->apartment_id.'" AND status = "'.Bookingcalendar::STATUS_BUSY.'" AND 
						UNIX_TIMESTAMP(date_start) > "'.$dateStart.'" AND UNIX_TIMESTAMP(date_end) < "'.$dateEnd.'"')
				->queryScalar();

			if ($param == 'date_start' && $result) {
				$this->addError('date', tt('You chose dates in the range of which there are busy days', 'bookingcalendar'));
			}
		}
	}

	public function attributeLabels() {
		return array(
			'date_start' => tt('Check-in date', 'booking'),
			'date_end' => tt('Check-out date', 'booking'),
			'email' => Yii::t('common', 'E-mail'),
			'time_in' => tt('Check-in time', 'booking'),
			'time_out' => tt('Check-out time', 'booking'),
			'comment' => tt('Comment', 'booking'),
			'username' => tt('Your name', 'booking'),
			'status' => tt('Status', 'booking'),
			'useremail' => Yii::t('common', 'E-mail'),
			'useremailSearch' => tt('User e-mail', 'booking'),
			'sum' => tt('Booking price', 'booking'),
			'date_created' => tt('Creation date', 'booking'),
			'dateCreated' => tt('Creation date', 'booking'),
			'apartment_id' => tt('Apartment ID', 'booking'),
			'sum_rur' => tt('Booking price (RUR)', 'booking'),
			'sum_usd' => tt('Booking price (USD)', 'booking'),
			'id' => tt('ID', 'apartments'),
			'phone' => Yii::t('common', 'Your phone number'),
		);
	}

	public static function getDate($mysqlDate, $full = 0) {
		if (!$full) {
			$date = CDateTimeParser::parse($mysqlDate, 'yyyy-MM-dd');
		}
		else {
			$date = CDateTimeParser::parse($mysqlDate, 'yyyy-MM-dd hh:mm:ss');
		}
		return Yii::app()->dateFormatter->format(self::getYiiDateFormat(), $date);
	}

	public static function getJsDateFormat() {
		$dateFormat = 'dd.mm.yy';
		if (Yii::app()->language == 'en') {
			$dateFormat = 'mm/dd/yy';
		}
		return $dateFormat;
	}
}