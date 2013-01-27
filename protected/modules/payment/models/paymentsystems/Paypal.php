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

class Paypal extends PaymentSystem {

    public $email;
    public $mode;

    public static function workWithCurrency(){
        return array("USD","EUR","GBP","YEN","CAD");
    }

//    public function init(){
//        $this->name = 'paypal';
//        return parent::init();
//    }

    public function rules(){
        return array(
            array('email', 'required'),
            array('email', 'email'),
            array('mode', 'safe'),
        );
    }

    public function attributeLabels(){
        return array(
            'email' => tt('PayPal email', 'payment'),
        );
    }

    public function processRequest(){
        $return = array(
            'id' => 0,
        );

        $invId = $_REQUEST["item_number"];

        /********
        запрашиваем подтверждение транзакции
         ********/
        $postdata="";
        foreach ($_POST as $key=>$value) {
            $postdata .= $key . "=" . urlencode($value) . "&";
        }
        $postdata .= "cmd=_notify-validate";

        $curl = curl_init("https://www.paypal.com/cgi-bin/webscr");
        curl_setopt ($curl, CURLOPT_HEADER, 0);
        curl_setopt ($curl, CURLOPT_POST, 1);
        curl_setopt ($curl, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, 1);
        $response = curl_exec ($curl);
        curl_close ($curl);

        if ($response != "VERIFIED") die("You should not do that ...");

        /********
        проверяем получателя платежа и тип транзакции, и выходим, если не наш аккаунт
        в $paypalemail - наш  primary e-mail, поэтому проверяем receiver_email
         ********/
        if ($_POST['receiver_email'] != $this->email
            || $_POST["txn_type"] != "web_accept")
            die("You should not be here ...");


        $return['id'] = $invId;

        if($_REQUEST['payment_status'] == "Completed"){
            $return['result'] = 'success';
        } elseif($_REQUEST['payment_status'] == "Pending") {
            $return['result'] = 'pending';
            $return['pending_reason'] = $_REQUEST['pending_reason'];
        } else {
            $return['result'] = 'fail';
        }

        return $return;
    }

    public function echoSuccess(){
        if($_REQUEST["payment"] == 'result'){
            echo("OK". $_REQUEST["InvId"]."\n");
            Yii::app()->end();
        }
    }

    public function processPayment(Payments $payment){

        $workWithCurrency = self::workWithCurrency();
        if(!in_array($payment->currency_charcode, $workWithCurrency)){
            $currency = $workWithCurrency[0];
            $amount = round(Currency::convert($payment->amount, $payment->currency_charcode, $currency), 0);
        } else {
            $amount = $payment->amount;
            $currency = $payment->currency_charcode;
        }

        $payUrl = $this->mode == Paysystem::MODE_TEST ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';

        $form = '
        <h3>'.$payment->paidservice->name.'</h3>
        <p><strong>'.tc('Cost of service').': '.$payment->paidservice->priceAndCurrency.'</strong></p>
        <p><strong id="notice_mess"></strong></p>
        <form method="post" action= "'.$payUrl.'" id="paypal_form">
        <input type="hidden" name="cmd" value="_xclick">
        <input type="hidden" name="business" value="'.$this->email.'">
        <input type="hidden" name="item_name" value="'.CHtml::encode($payment->paidservice->name).'">
        <input type="hidden" name="item_number" value="'.$payment->id.'">
        <input type="hidden" name="amount" value="'.$amount.'">
        <input type="hidden" name="currency_code" value="'.$currency.'">
        <input type="hidden" name="no_shipping" value="1">
        <input type="hidden" name="notify_url" value="'.self::getUrlResult().'">
        <input type="hidden" name="return" value="'.self::getUrlSuccess().'">
        <input type="hidden" name="cancel_return" value="'.self::getUrlFail().'">
        <input type="hidden" name="rm" value="2">
        <input type="submit" id="submit_paypal_form" value="'.tt('Pay Now', 'payment').'">
        </form>

        <script type="text/javascript">
        $(document).ready(function(){
            $("#notice_mess").html("'.tt('Please_wait_payment', 'payment').'");
            $("#submit_paypal_form").attr("disabled", "disabled");
            $("#paypal_form").submit();
        });
        </script>
        ';

        return $form;
    }

    public function printInfo(){
        ?>
    <br />
    <ul>
        <li><?php
            echo Yii::t('module_payment','Result URL: ').self::getUrlResult();
            ?>
        </li>
        <li><?php
            echo Yii::t('module_payment','Success URL: ').self::getUrlSuccess();
            ?>
        </li>
        <li><?php
            echo Yii::t('module_payment','Fail URL: ').self::getUrlFail();
            ?>
        </li>
    </ul>
    <?php
    }

    public static function getUrlResult(){
        return Yii::app()->controller->createAbsoluteUrl('/payment/main/income',
            array(
                'sys' => 'paypal',
                'payment' => 'result',
            ));
    }

    public static function getUrlSuccess(){
        return Yii::app()->controller->createAbsoluteUrl('/payment/main/income',
            array(
                'sys' => 'paypal',
                'payment' => 'success',
            ));
    }

    public static function getUrlFail(){
        return Yii::app()->controller->createAbsoluteUrl('/payment/main/income',
            array(
                'sys' => 'paypal',
                'payment' => 'fail',
            ));
    }
}