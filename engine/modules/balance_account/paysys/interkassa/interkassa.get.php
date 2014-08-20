<?php

$dataSet = array();
foreach($_POST as $key=>$val) $dataSet[$key] = trim($val);

if(empty($dataSet['ik_sign'])){
   die ('Error: Do not pass parameters.');
} else {

	require_once MODULE_PATH . "/admin/paysys.class.php";
	require_once MODULE_PATH . "/kg/adm.functions.php";

	include ('engine/api/api.class.php');

	$paysystems = $Paysys->select();
	$config_shop_data = unserialize( $paysystems[$_GET['payment']]['info'] );

	$pay_data = $Paysys->search_pay($dataSet['ik_pm_no']);

    unset($dataSet['ik_sign']);
    ksort($dataSet, SORT_STRING); 
    array_push($dataSet, $config_shop_data['secret_key']);
    $signString = implode(':', $dataSet);
    $sign = base64_encode(md5($signString, true));

	if($_POST['ik_sign'] == $sign) {
		
		$msg = "";
		if( !$pay_data['payment_id'] )				$msg = "Платёж key: <b>{$pay_data['payment_id']}</b> не найден.";
		elseif( $pay_data['payment_datepay'] )			$msg = "Платёж key: <b>{$pay_data['payment_id']}</b> уже был оплачен.";
		elseif( $pay_data['payment_system']!=$_GET['payment'] )	$msg = "Платёж key: <b>{$pay_data['payment_id']}</b>. Ошибка платёжной системы.";
		elseif( $dataSet['ik_inv_st']!="success" )			$msg = "Платёж key: <b>{$pay_data['payment_id']}</b>. Оплата не прошла.";
		else {

			$Paysys->pay_paybill($dataSet['ik_pm_no'], $pay_data['payment_user'], $pay_data['payment_money'], $pay_data['payment_cont'], $config_shop_data['cu_one'], $paysystems[$_GET['payment']]['name'], $ba_config);
			$msg = "Платёж <b>#{$dataSet['ik_pm_no']}</b> оплачен в системе {$paysystems[$_GET[payment]][name]}. На Ваш счёт зачислено {$pay_data['payment_money']} {$pay_data['payment_cont']}.";

      $user_info = $dle_api->take_user_by_name( $pay_data['payment_user'], '*');
      $dle_api->send_pm_to_user ( $user_info['user_id'], "Пополнение баланса", $msg, "Платёжные системы");
      
      echo "OK";
		}

	} else {
		echo 'Проверка контрольной подписи данных о платеже провалена!';
	}

}

?>