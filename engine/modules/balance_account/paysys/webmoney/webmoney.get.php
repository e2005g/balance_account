<?php

	require_once MODULE_PATH . "/admin/paysys.class.php";
	require_once MODULE_PATH . "/kg/adm.functions.php";

	include ('engine/api/api.class.php');

		/* получаем результат платежа*/
		$LMI_PAYMENT_NO	= $_POST['LMI_PAYMENT_NO'];		// ID счёта
		$LMI_PAYMENT_AMOUNT = $_POST['LMI_PAYMENT_AMOUNT']; 	// Стоимость
		$LMI_PREREQUEST = intval( $_POST['LMI_PREREQUEST'] ); 	// Предварительный запрос (если = 1)
		$LMI_PAYEE_PURSE = $_POST['LMI_PAYEE_PURSE']; 		// Кошель продавца
		$LMI_MODE = intval( $_POST['LMI_MODE'] ); 		// Флаг тестового режима оплаты
		$LMI_SECRET_KEY = $_POST['LMI_SECRET_KEY']; 		// Секретный ключ
		$LMI_SYS_INVS_NO = $_POST['LMI_SYS_INVS_NO']; 		// Внутренний номер счета в системе WebMoney Transfer
		$LMI_SYS_TRANS_NO = $_POST['LMI_SYS_TRANS_NO']; 	// Внутренний номер платежа в системе WebMoney Transfer
		$LMI_SYS_TRANS_DATE = $_POST['LMI_SYS_TRANS_DATE']; 	// Дата и время выполнения платежа
		$LMI_PAYER_PURSE = $_POST['LMI_PAYER_PURSE']; 		// Кошелек покупателя
		$LMI_PAYER_WM = $_POST['LMI_PAYER_WM']; 		// WMId покупателя
		$LMI_HASH = $_POST['LMI_HASH']; 			// MD5 Hash

    if( !$LMI_PAYMENT_NO ) die("ID счёта не получен!");

		/* данные счёта */
		$paysystems = $Paysys->select();
		$config_shop_data = unserialize( $paysystems[$_GET['payment']]['info'] );

		$pay_data = $Paysys->search_pay( $LMI_PAYMENT_NO );

		$TestHash = strtoupper( md5( $LMI_PAYEE_PURSE.$pay_data['payment_money'].$LMI_PAYMENT_NO.$LMI_MODE.$LMI_SYS_INVS_NO.$LMI_SYS_TRANS_NO.$LMI_SYS_TRANS_DATE.$config_shop_data['wm_secretkey'].$LMI_PAYER_PURSE.$LMI_PAYER_WM ) );

/*предпроверка*/
IF($LMI_PREREQUEST==1) {

	$msg = "";

		if( !$pay_data['payment_id'] )					$msg = "Счёт <b>ID:{$LMI_PAYMENT_NO}</b> не найден.";
		elseif( $pay_data['payment_datepay'] )				$msg = "Счёт <b>ID:{$LMI_PAYMENT_NO}</b> уже был оплачен.";
		elseif( $pay_data['payment_system']!=$_GET['payment'] )		$msg = "Счёт <b>ID:{$LMI_PAYMENT_NO}</b>. Ошибка платёжной системы.";
		elseif( trim($LMI_PAYEE_PURSE)!=$config_shop_data['wm_purse'] )	$msg = "Счёт <b>ID:{$LMI_PAYMENT_NO}</b>. Неверный кошелёк.";
		elseif( trim($LMI_PAYMENT_AMOUNT)!=$pay_data['payment_money'] )	$msg = "Счёт <b>ID:{$LMI_PAYMENT_NO}</b>. Неверная сумма платежа.";
		else {
			$msg = "YES"; 
		}

	echo $msg; 

  exit();

} else {

	/* оплата */

	if($LMI_HASH!=$TestHash) exit;

	$Paysys->pay_paybill($LMI_PAYMENT_NO, $pay_data['payment_user'], $pay_data['payment_money'], $pay_data['payment_cont'], $config_shop_data['cu_one'], $paysystems[$_GET['payment']]['name'], $ba_config);

	$msg = "Платёж <b>#{$LMI_PAYMENT_NO}</b> оплачен в системе {$paysystems[$_GET[payment]][name]}. На Ваш счёт зачислено {$pay_data['payment_money']} {$pay_data['payment_cont']}.";

	$user_info = $dle_api->take_user_by_name( $pay_data['payment_user'], '*');
	$dle_api->send_pm_to_user( $user_info['user_id'], "Пополнение баланса", $msg, "Платёжные системы");

}
?>