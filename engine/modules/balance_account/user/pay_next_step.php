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

  /* Ajax: вывод */

if( $is_logged ) {

	$step = $parse->process( trim($db->safesql($_GET['step'])));
	$paysys = $parse->process( trim($db->safesql( $_GET['paysys'] )));		
	$unit =  @number_format( str_replace(",", ".", $_GET['unit']) , 2, '.', '');
	$captcha_code = $parse->process( trim($_GET['code']) );
  $member_id['name'] = $db->safesql( $member_id['name'] );

	if( !$ba_config['pay_status'] ) {
		echo "<div id=\"payb_navigation\"><a href=\"#\" onClick=\"load_page_pay('menu')\">{$ba_lang['nav_start']}</a> &raquo; {$ba_lang['nav_pay']}</div><SPLIT><div id=\"payb_main\">Данная функция отключена администрацией.</div>";
		exit();
	}

		switch( $step ) {
			case "select":
				echo "<div id=\"payb_navigation\"><a href=\"#\" onClick=\"load_page_pay('menu')\">{$ba_lang['nav_start']}</a> &raquo; {$ba_lang['nav_pay']}</div><SPLIT>";
        echo "<div id=\"payb_main\">".$Pay_Process->select_payments()."</div>";
			break;
			case "unit":
				echo "<div id=\"payb_navigation\"><a href=\"#\" onClick=\"load_page_pay('menu')\">{$ba_lang['nav_start']}</a> &raquo; <a href=\"#\" onClick=\"kgba_select_payment( 'select', '' )\"><u>{$ba_lang['nav_pay']}</u></a> &raquo; {$ba_lang['nav_dosumma']}</div><SPLIT>";
        echo "<div id=\"payb_main\">".$Pay_Process->setunit($paysys)."</div>";
			break;
			case "end":
				echo "<div id=\"payb_navigation\"><a href=\"#\" onClick=\"load_page_pay('menu')\">{$ba_lang['nav_start']}</a> &raquo; {$ba_lang['nav_creatbill']}</div><SPLIT>";
        echo "<div id=\"payb_main\">".$Pay_Process->creat_payment($paysys, $unit, $member_id['name'], $captcha_code)."</div>";
			break;
		}

} else echo $ba_lang['needlogin'];

?>