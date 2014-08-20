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

if(!defined('KG_MODULE')) {
  die("Hacking attempt!");
}

/* Навигация. */

if( $is_logged ) {

	echo "<div id=\"payb_navigation\">{$ba_lang['nav_start']}</div><SPLIT><div id=\"payb_main\">".$Pay_Process->menu( $member_id[$ba_config['user_balance_field']], $ba_config['money_cont'] )."</div>";

} else echo $ba_lang['needlogin'];
	

?>