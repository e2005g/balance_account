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

/* Страница: просмотр моих счетов, форма оплаты */

if( $is_logged ) {

	require_once MODULE_PATH . '/admin/paysys.class.php';
	require_once MODULE_PATH . "/user/pay.class.php";

	$page = intval( $_GET['pagin'] );
  $member_id['name'] = $db->safesql( $member_id['name'] );

	if(!$page) $page  = 1;
	$per_page = $ba_lang['paging_bills'];
	$start_from = ($page * $per_page) - $per_page;

	$result_count = $db->super_query( "SELECT COUNT(*) as count FROM " . USERPREFIX . "_payments where payment_user='{$member_id[name]}'" );
	$all_count = $result_count['count'];

  if( $all_count ) {
  
      $paysys_list = $Paysys->select();
      $answer = "";
      $db->query( "SELECT * FROM " . USERPREFIX . "_payments where payment_user='{$member_id[name]}' ORDER BY payment_id desc LIMIT {$start_from},{$per_page}" );

      while ( $row = $db->get_row() ) {

        $datepay = ( $row['payment_datepay'] ) ? "<font color='green'>".langdate( "j F Y  G:i", $row['payment_datepay'])."</font>" : "<font color='red'>{$ba_lang['bills_dontpay']}</font>";

        $paylink = ( !$row['payment_datepay'] ) ? "<a href=\"#\" onClick=\"showBill('{$row[payment_id]}')\">#{$row[payment_id]}</a>" : "#{$row[payment_id]}";
        $answer .= "<tr>
                      <td>{$paylink}</td>
                      <td>{$datepay}</td>
                      <td>{$row[payment_money]} {$row[payment_cont]}</td>
                      <td>{$paysys_list[$row[payment_system]][name]}</td>
                      <td>".langdate( "j F Y  G:i", $row['payment_datecreat'])."</td>
                   </tr>";
      }
      
  } else $answer = "<tr><td colspan='5'>{$ba_lang['bills_not']}</td></tr>";

  /* Paging */
	$npp_nav = ( $all_count > $per_page ) ? kgPaging( $all_count, $page, " <a href=\"#\" onClick=\"load_page_pay('bills', '{p}', false)\">{p}</a> ", $per_page ) : "";

	$content = file_get_contents( ROOT_DIR . "/templates/{$config[skin]}/balance_account/bills.tpl");
	$content = str_replace("{bills}", $answer, $content);	
	$content = str_replace("{paging}", $npp_nav, $content);

	echo "<div id=\"payb_navigation\"><a href=\"#\" onClick=\"load_page_pay('menu')\">{$ba_lang['nav_start']}</a> &raquo; {$ba_lang['nav_mybills']}</div><SPLIT><div id=\"payb_main\">{$content}</div>";

} else echo $ba_lang['needlogin'];

?>