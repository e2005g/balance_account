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

/* Запрос вывода средств */

if( !defined( 'KG_MODULE' ) ) {
    die( "Hacking attempt!" );
}

	require_once MODULE_PATH . "/pay.api.php";

	/* Удаляем запрос */
	if( $_GET['type']=="r" and $_GET['h']==$dle_login_hash) {
		
		$row = $db->super_query( "SELECT * FROM " . USERPREFIX . "_payments_outmoney WHERE om_id='".intval($_GET['id'])."'" );

			if( count($row) ) {
				$pay_api->plus($row['om_user'], $row['om_back'], "Администрация отказала Вам в выводе средств.");
				$db->query( "DELETE FROM " . USERPREFIX . "_payments_outmoney WHERE om_id='".intval($_GET['id'])."'" );

				header('Location: '.$PHP_SELF.'?mod=balance_account&section=moneyback'); 
			}	

	} 

	/* Одобрено */
	if( $_GET['type']=="ok" and $_GET['h']==$dle_login_hash) {

		$row = $db->super_query( "SELECT * FROM " . USERPREFIX . "_payments_outmoney WHERE om_id='".intval($_GET['id'])."' LIMIT 1" );

			if( count($row) ) {
				
				$db->query( "UPDATE " . USERPREFIX . "_payments_outmoney SET om_date_pay='".strtotime(date('j F Y  G:i'))."' WHERE om_id='".intval($_GET['id'])."'" );	

				msg( "info", "Запрос одобрен!", "Запрос пользователя одобрен. <p><table width=\"300\">
              <tr><td align=\"right\">Пользователь:</td><td width=\"5\"></td><td><a href='{$ba_link[user]}{$row[om_user]}' target='_blank'>{$row[om_user]}</a></td></tr>
              <tr><td align=\"right\">Сумма:</td><td width=\"5\"></td><td>{$row[om_money]} {$row[om_cont]}</td></tr>
              <tr><td align=\"right\">Реквезиты:</td><td width=\"5\"></td><td>{$row[om_desc]}</td></tr>
                </table></p>", "$PHP_SELF?mod=balance_account&section=moneyback" );
			}

	}

	/* Список запросов */

	echoheader( "Баланс пользователя v.".$ba_config['version'], "Кошелёк пользователя с возможностью его пополнения, перевода, вывода средств.");
	$ADM_theme->start("<a href='{$PHP_SELF}?mod=balance_account'>Баланс пользователя v.{$ba_config[version]}</a> &raquo; Вывод средств");

	$db->query( "SELECT * FROM " . USERPREFIX . "_payments_outmoney where om_date_pay=0 ORDER BY om_id" );
	
	while ( $row = $db->get_row() ) {

		$content .= "<tr>
                    <td>".langdate( "j F Y  G:i", $row['om_date_creat'])."</td>
                    <td><a href='{$PHP_SELF}?mod=balance_account&section=deal&user_name=".urlencode($row['om_user'])."'>{$row[om_user]}</a></td>
                    <td>{$row[om_money]} {$row[om_cont]}</td>
                    <td>{$row[om_desc]}</td>
                    <td align=\"center\"><a href=\"{$PHP_SELF}?mod=balance_account&section=moneyback&type=ok&h={$dle_login_hash}&id={$row[om_id]}\">[одобрить]</a> <a href=\"{$PHP_SELF}?mod=balance_account&section=moneyback&type=r&h={$dle_login_hash}&id={$row[om_id]}\">[отказать]</a></td>
                   </tr>";
	}
	
	if( !$content ) $content = "<tr><td colspan=\"5\"><p style=\"margin:10px;\">Запросов на вывод средств нет.</p></td></tr>";

    /* Вывод страницы */
    echo "<div id='list_paybills'>
    <table width='100%'  class='table table-normal'><tr class='thead'>
      <td width='80'><b>Дата<b></td>
      <td width='100'><b>Пользователь<b></td>
      <td width='120'><b>Сумма<b></td>
      <td width='130'><b>Примечание<b></td>
      <td width='10' align=\"center\"><b>Оплатить<b></td>
    </tr>{$content}</table></div>";

	$ADM_theme->end_copy();
	echofooter();
?>