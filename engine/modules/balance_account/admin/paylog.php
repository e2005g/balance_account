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

/* Счета пользователей */

if( !defined( 'KG_MODULE' ) ) {
    die( "Hacking attempt!" );
}

/* Вывод страницы */

	echoheader( "Баланс пользователя v.".$ba_config['version'], "Кошелёк пользователя с возможностью его пополнения, перевода, вывода средств.");
	$ADM_theme->start("<a href='{$PHP_SELF}?mod=balance_account'>Баланс пользователя v.{$ba_config[version]}</a> &raquo; Счета пользователей");

	echo "<body class=\"body\" onload=\"select_sort('select_all')\">";
	echo "<div id='list_paybills'>Выберите способ сортировки..</div>";

	echo "<table width=\"100%\" class=\"table table-normal\"><tr>
		<td width=\"50%\">
			<div align='left'>Сортировка: <span id='select_all'><a href=\"#\" onClick=\"select_sort('select_all')\">все</a></span>, <span id='select_ok'><a href=\"#\" onClick=\"select_sort('select_ok')\">оплаченные</a></span>, <span id='select_no'><a href=\"#\" onClick=\"select_sort('select_no')\">неоплаченные</a></span>.</div>
			<input name=\"selectsort\" id=\"selectsort\" value=\"select_all\" type=\"hidden\">
		</td>
		<td>
			<a href=\"#\" onClick=\"doact('remove')\">Удалить отмеченные</a>: нажмите на ключ, что бы добавить 
			<input name=\"selectfordo\" id=\"selectfordo\" value=\"\" type=\"hidden\">
			<input type=\"hidden\" name=\"user_hash\" value=\"{$dle_login_hash}\" />
		</td></tr></table>";

	$ADM_theme->end_copy();
	echofooter();
?>