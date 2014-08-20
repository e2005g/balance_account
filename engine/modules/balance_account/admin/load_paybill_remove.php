<?php
/*
=====================================================
 Баланс пользователя
-----------------------------------------------------
 evgeny.tc@gmail.com
-----------------------------------------------------
 Данный код защищен авторскими правами
=====================================================
*/

/* Удаление, оплата счетов */

require_once '../user/load_control.php';
require_once "../data/config.php";

require_once 'paysys.class.php';

require_once "../pay.api.php";

if( $is_logged AND $member_id['user_group']==1) {
	
	$payments = explode(",", $_REQUEST['keys']);

  /* удаляем счет */
	if( $_REQUEST['act']=='remove' ) {

		foreach($payments as $pay_key) {

			$payment = explode("|", $pay_key);
			if(trim($pay_key)) $db->query( "DELETE FROM " . USERPREFIX . "_payments WHERE payment_id='$payment[0]'" );
	
		}

  /* оплачиваем счет */
	} elseif($_REQUEST['act']=='pay' ) {

		$payment = explode("|", $_REQUEST['keys']);

			$db->query( "UPDATE " . USERPREFIX . "_payments SET payment_datepay='".strtotime(date('j F Y  G:i'))."' WHERE payment_id='$payment[0]'" );	

			if($payment[3]!=$ba_config['money_cont']) {
				$money_conv = $payment[2] * $payment[4];

				$pay_api->plus($payment[1], $money_conv, "Счёт #{$payment[0]} оплачен через систему {$payment[5]}. Сумма до конвертации {$payment[2]} {$payment[3]}.");
			} else	$pay_api->plus($payment[1], $payment[2], "Счёт #{$payment[0]} оплачен через систему {$payment[5]}.");

	}

}

?>