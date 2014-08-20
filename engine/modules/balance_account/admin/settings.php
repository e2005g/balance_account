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

/* Настройки модуля */

if( !defined( 'KG_MODULE' ) ) {
    die( "Hacking attempt!" );
}

  /* Сохранить изменения */
	if( isset($_POST['save_setting']) ) {
		if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {       
			die( "Hacking attempt! User not found {$_REQUEST['user_hash']}" );   
		} 

		$save_con = $_POST['save_con'];
		$save_con['plugins'] = $ba_config['plugins'];
    $save_con['version'] = '0.4';

		foreach($_POST['acess_outmoney'] as $group_id)  $acess_outmoney .= $group_id.",";
								$save_con['acess_outmoney'] = $acess_outmoney;

		foreach($_POST['acces_tran'] as $group_id)	$acces_tran .= $group_id.",";
								$save_con['acess_tran'] = $acces_tran;

		foreach($_POST['captcha'] as $page_id)		$acces_captcha .= $page_id.",";
								$save_con['captcha'] = $acces_captcha;

		if($_POST['mback_com_type']=="percent")	$com_money = intval($_POST['mback_com_money']);
		else					$com_money = number_format( str_replace(",", ".", $_POST['mback_com_money']) , 2, '.', '');

		$save_con['mback_com'] = $com_money.":".$_POST['mback_com_type'];

		save_setting("config", $save_con, "ba_config");

		msg( "info", "Действие выполнено.", "Настройки успешно сохранены", "$PHP_SELF?mod=balance_account&section=settings" );
	}

/* Вывод страницы */

	$mback_com = explode(":", $ba_config['mback_com']);

	echoheader( "Баланс пользователя v.".$ba_config['version'], "Кошелёк пользователя с возможностью его пополнения, перевода, вывода средств.");
	$ADM_theme->start("<a href='{$PHP_SELF}?mod=balance_account'>Баланс пользователя v.{$ba_config[version]}</a> &raquo; Настройки модуля");

	$ADM_theme->select("Включить пополнение баланса:", "Разрешить пользователям пополнять свой баланс с помощью активных платёжных систем.", makeCheckBox("save_con[pay_status]", $ba_config['pay_status']) );
	$ADM_theme->select("Включить вывод средств:", "Пользователь сможет создавать заявки на вывод средств.", "<table width=\"100%\"><tr><td>".makeCheckBox("save_con[mback_status]", $ba_config['mback_status'] )."</td><td><select name=\"acess_outmoney[]\"  multiple>".get_groups(explode( ',', $ba_config['acess_outmoney'] ), explode( ',', $row['grouplevel'] ) )."</select></td></tr></table>" );
	$ADM_theme->select("Включить перевод средств между пользователями:", "Пользователь сможет передать деньги со своего счёта на счёт другого пользователя.", "<table width=\"100%\"><tr><td>".makeCheckBox("save_con[mback_tran]", $ba_config['mback_tran'])."</td><td><select name=\"acces_tran[]\" class=\"edit bk\" multiple>".get_groups(explode( ',', $ba_config['acess_tran'] ), explode( ',', $row['grouplevel'] ) )."</select></td></tr></table>" );

	$ADM_theme->select("Валюта баланса пользователя:", "Наименование валюты, в которой хранится баланс пользователей.", "<input name=\"save_con[money_cont]\" class=\"edit bk\" type=\"text\" value=\"{$ba_config['money_cont']}\" size=\"10\">" );
	$ADM_theme->select("Включить каптчу:", "Для данных действий будет необходимо ввести код с картинки.", "<select name=\"captcha[]\" class=\"edit bk\" multiple>".get_selects(explode( ',', $ba_config['captcha'] ), array('0'=>" ",'pay'=>"Пополнение баланса",'tran'=>"Перевод средств",'outmoney'=>"Вывод средств") )."</select>" );

	$ADM_theme->select("Комиссия сервиса:", "Данную комиссию будет удерживать сайт  с каждого вывода.", "<input name=\"mback_com_money\" class=\"edit bk\" type=\"text\" value=\"{$mback_com[0]}\" size=\"10\"> ".makeDropDown(array ("percent" => "%", "fixed" => $ba_config['money_cont'] ), "mback_com_type", "{$mback_com[1]}") );
	$ADM_theme->select("Минимальная сумма вывода:", "Минимальная сумма для заказа вывода средств с баланса пользователя.", "<input name=\"save_con[mback_min]\" class=\"edit bk\" type=\"text\" value=\"{$ba_config['mback_min']}\" size=\"10\">" );
	$ADM_theme->select("Поле в БД с балансом пользователя:", "Поле в БД, таблица _users с балансом пользователя.", "<input name=\"save_con[user_balance_field]\" class=\"edit bk\" type=\"text\" value=\"{$ba_config['user_balance_field']}\" size=\"20\">" );
	$ADM_theme->select("Ключ доступа платёжной системы:", "Введите произвольный нобор букв и цифр, ключ используется для формировании result url.<br />Никому не сообщайте этот ключ.", "<input name=\"save_con[paycode]\" class=\"edit bk\" type=\"text\" value=\"{$ba_config['paycode']}\" size=\"20\">" );

	$ADM_theme->select("", "", "<input class=\"btn btn-green\" style=\"margin:7px;\" name=\"save_setting\" type=\"submit\" value=\"Сохранить\"><input type=\"hidden\" name=\"user_hash\" value=\"{$dle_login_hash}\" />");

	echo "<table width=\"100%\" class=\"table table-normal\">".$ADM_theme->ec_select."</table>";

	$ADM_theme->end_copy();
	echofooter();
?>