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

/* Список счетов, ajax*/

if( $is_logged AND $member_id['user_group']==1) {

  require_once 'paysys.class.php';
  require_once ROOT_DIR . '/language/Russian/adminpanel.lng';

	$type = $_REQUEST['type'];
	$page = intval( $_REQUEST['paging'] );
	
	switch( $type ) {
		case "select_ok": $sort = "where payment_datepay!='0'"; break;
		case "select_no": $sort = "where payment_datepay='0'"; break;		
		default: $sort = "";
	}

	if(!$page) $page  = 1;
	$per_page = 10;
	$start_from = ($page * $per_page) - $per_page;

	$result_count = $db->super_query( "SELECT COUNT(*) as count FROM " . USERPREFIX . "_payments {$sort}" );
	$all_count = $result_count['count'];

	$paysys_list = $Paysys->select();

	$db->query( "SELECT * FROM " . USERPREFIX . "_payments {$sort} ORDER BY payment_id desc LIMIT {$start_from},{$per_page}" );
	while ( $row = $db->get_row() ) {

		$info_paymentsys = unserialize( $paysys_list[$row['payment_system']]['info'] );

		$datepay = ( $row['payment_datepay'] ) ? "<font color='green'>".langdate( "j F Y  G:i", $row['payment_datepay'])."</font>" : "<a href=\"#\" title=\"Оплатить!\" onClick=\"doact('pay', '{$row[payment_id]}|{$row[payment_user]}|{$row[payment_money]}|{$row[payment_cont]}|{$info_paymentsys[cu_one]}|{$paysys_list[$row[payment_system]][name]}')\"><font color='red'>Не оплачено</font></a>";

		$answer .= "<tr>
				  <td align='center'><span id=\"selectbill_{$row[payment_id]}\" class=\"\"><a href=\"#\" onClick=\"add_for('{$row[payment_id]}')\">{$row[payment_id]}</a></span></td>
					<td>{$datepay}</td>
					<td><a href='{$PHP_SELF}?mod=balance_account&section=deal&user_name=".urlencode($row['payment_user'])."'>{$row[payment_user]}</a></td>
					<td>{$row[payment_money]} {$row[payment_cont]}</td>
					<td>{$paysys_list[$row[payment_system]][name]}</td>
					<td>".langdate( "j F Y  G:i", $row['payment_datecreat'])."</td>
				</tr></tr>";
	}

	if( !$answer )	$answer = "<tr><td colspan='6' style='margin:10px;'>Список платежей пуст.</td></tr>";

  /* Paging */
  $answer .= ( $all_count > $per_page ) ? "<tr><td colspan='3' style='margin:10px;'>".kgPaging( $all_count, $page, " <a href=\"#\" onClick=\"load_list('{p}')\">{p}</a> ", $per_page )."</td></tr>": "";

 /* Вывод страницы */
 echo "<div id='list_paybills'>
  <table width='100%' class='table table-normal'><tr class='thead'>
		<td align='center'><b>Ключ<b></td>
		<td><b>Оплачено<b></td>
		<td><b>Пользователь<b></td>
		<td><b>Сумма<b></td>
		<td><b>Платёжная система<b></td>
		<td><b>Платёж создан<b></td>
	</tr>{$answer}</table></div>";

}
?>