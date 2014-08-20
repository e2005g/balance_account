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

/* Страница: счёт для оплаты. */

if( !defined( 'KG_MODULE' )) {
    die( "The opening pages of the module is not allowed!" );
}

	require_once MODULE_PATH . "/user/pay.class.php";

/* Пополнение баланса отключено */
if( !$ba_config['pay_status'] and $member_id['user_group']!=1 )	 {
	echo $ba_lang['accesdesc'];
	die();
}

/* Удаляем счёт */
if( intval($_REQUEST['remove']) ) {

  $db->query( "DELETE FROM " . USERPREFIX . "_payments WHERE payment_user='$member_id[name]' and payment_id='$_REQUEST[remove]'" );
  die();
}

	/* Страница оплаты */
	if( $_REQUEST['key'] and $ba_config['pay_status']) {

		$bill = intval( $_REQUEST['key'] );
		$member_id['name'] = $db->safesql( $member_id['name'] );
		$errors = "";

			$sql_result = $db->query( "SELECT * FROM " . USERPREFIX . "_payments where payment_id ='$bill' and payment_user='$member_id[name]'" );
	   		$row = $db->get_row( $sql_result );

				$paysys = $Paysys->select(1);
				
				/* Сверка данных счета с пользователем */
				if( !$paysys[$row['payment_system']]['name'] and $row['payment_id'])	  $errors = $ba_lang['paybill_er3'];
				if( $row['payment_datepay'] )									                          $errors = $ba_lang['paybill_er2'];
				if( !$row['payment_id'] OR $row['payment_user']!=$member_id['name'] )		$errors = $ba_lang['paybill_er1'];
 
				if( $errors )	
          $pagecontent = "{$ba_lang['paybill_er']} <ul>{$errors}</ul> <a href='".dolink($_GET['page'], 'cabinet')."'>{$ba_lang['back']}</a>.";
				else
					$pagecontent = $Pay_Process->get_bill($row['payment_system'], $bill, $member_id['name'], $row['payment_money']);


  /* Оплата запрещена администратором */
	} elseif(!$ba_config['pay_status'])
    $pagecontent = $ba_lang['accesdesc'];
    
  /* Не указан ID платежа */
	else 
    $pagecontent = "{$ba_lang['paybill_er4']} <br /><a href='{".dolink($_GET['page'], 'cabinet')."'>{$ba_lang['back']}</a>.";

  echo $pagecontent;
?>