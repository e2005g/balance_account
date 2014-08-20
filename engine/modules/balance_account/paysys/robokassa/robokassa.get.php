<?php

	require_once MODULE_PATH . "/admin/paysys.class.php";
	require_once MODULE_PATH . "/kg/adm.functions.php";

	include ('engine/api/api.class.php');

	/* чтение параметров */
	$out_summ = $_REQUEST["OutSum"];
	$inv_id = $_REQUEST["InvId"];
	$crc = $_REQUEST["SignatureValue"];

	$paysystems = $Paysys->select();
	$config_shop_data = unserialize( $paysystems[$_GET['payment']]['info'] );
	$pay_data = search_pay( $inv_id );

	/* проверка подписи */
	$crc = strtoupper($crc);
	$my_crc = strtoupper(md5("$out_summ:$inv_id:$config_shop_data[pass2]"));

	if ($my_crc != $crc) {
		echo 'Verifying the signature information about the payment failed!';
	 exit();
	}

	/* платёж принят */
	echo "OK$inv_id\n";

		/* внутренняя проверка платежа*/
		$msg = "";
		if( !$pay_data['payment_id'] )				$msg = "Платёж <b>id:{$inv_id}</b> не найден.";
		elseif( $pay_data['payment_datepay'] )			$msg = "Платёж <b>id:{$inv_id}</b> уже был оплачен.";
		elseif( $pay_data['payment_system']!=$_GET['payment'] )	$msg = "Платёж <b>id:{$inv_id}</b>. Ошибка платёжной системы.";
		elseif( $out_summ!=$pay_data['payment_money'] )	$msg = "Счёт <b>ID:{$LMI_PAYMENT_NO}</b>. Неверная сумма платежа.";
		else {

			$Paysys->pay_paybill($inv_id, $pay_data['payment_user'], $pay_data['payment_money'], $pay_data['payment_cont'], $config_shop_data['cu_one'], $paysystems[$_GET['payment']]['name'], $ba_config);
			$msg = "Платёж <b>#{$inv_id}</b> оплачен в системе {$paysystems[$_GET[payment]][name]}. На Ваш счёт зачислено {$pay_data['payment_money']} {$pay_data['payment_cont']}.";

		}

		$user_info = $dle_api->take_user_by_name( $pay_data['payment_user'], '*');
		$dle_api->send_pm_to_user ( $user_info['user_id'], "Пополнение баланса", $msg, "Платёжные системы");

?>