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

/* Лог движения средств */

if( !defined( 'KG_MODULE' ) ) {
    die( "Hacking attempt!" );
}

/* Очистка лога */
if( isset($_POST['log_remove']) ) {
		if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {       
			die( "Hacking attempt! User not found {$_REQUEST['user_hash']}" );   
		} 

  $ids = $_POST['log_remove_list'];
  
  foreach($ids as $id_d) {
  
    $id_d = intval( $id_d );
    $db->query( "DELETE FROM " . USERPREFIX . "_payments_log WHERE log_id = '$id_d'" );

  }

  msg( "info", "Действие выполнено.", "Отмеченные записи удалены из лога", "$PHP_SELF?mod=balance_account&section=log" );
}

  /* Вывод страницы */
  echoheader( "Баланс пользователя v.".$ba_config['version'], "Лог движения средств");
	$ADM_theme->start("<a href='{$PHP_SELF}?mod=balance_account'>Баланс пользователя v.{$ba_config[version]}</a> &raquo; Лог движения средств");

	$page = ($_GET['paging']) ? $_GET['paging']: 1;
	
	if($_GET['sort']=="plus") {
      $sort = "where log_do='plus'";
      $option_sort = "<option value=\"{$PHP_SELF}?mod=balance_account&section=log\">Все действия</option><option selected>Положительные</option><option value=\"{$PHP_SELF}?mod=balance_account&section=log&sort=minus\">Отрицательные</option>";
	} elseif($_GET['sort']=="minus") {
      $sort = "where log_do='minus'";
      $option_sort = "<option value=\"{$PHP_SELF}?mod=balance_account&section=log\">Все действия</option><option value=\"{$PHP_SELF}?mod=balance_account&section=log&sort=plus\">Положительные</option><option selected>Отрицательны</option>";     
	} else {
      $sort = "";
       $option_sort = "<option selected>Все действия</option><option value=\"{$PHP_SELF}?mod=balance_account&section=log&sort=plus\">Положительные</option><option value=\"{$PHP_SELF}?mod=balance_account&section=log&sort=minus\">Отрицательны</option>";          
	}
	
	$per_page = 10;
	$start_from = ($page * $per_page) - $per_page;
  $answer = "";

	$query_count = "SELECT COUNT(*) as count FROM " . USERPREFIX . "_payments_log ".$sort;
	$result_count = $db->super_query( $query_count );
	$all_count = $result_count['count'];

	$db->query( "SELECT * FROM " . USERPREFIX . "_payments_log {$sort} ORDER BY log_id desc LIMIT {$start_from},{$per_page}" );
	while ( $row = $db->get_row() ) {

    $act_do = ( $row['log_do']=="plus" ) ? "<font color=green>+{$row[log_money]} {$row[log_cont]}</font>": "<font color=red>-{$row[log_money]} {$row[log_cont]}</font>";

		$answer .= "<tr>
					<td>".langdate( "j F Y  G:i", $row['log_date'])."</td>
					<td align='center'><a href='{$PHP_SELF}?mod=balance_account&section=deal&user_name=".urlencode($row['log_user'])."' title='Досье на {$row[log_user]}'>{$row[log_user]}</a></td>
					<td>{$act_do}</td>
					<td>{$row[log_desc]}</td>
					<td align=\"center\"><input name=\"log_remove_list[]\" value=\"{$row[log_id]}\" type=\"checkbox\"></td>
				</tr>";
	}

	if( !$answer )	$answer = "<tr><td colspan='5' style='margin:10px;'>Лог пуст.</td></tr>";

  /* Paging */
  if( $all_count ) $answer .= ( $all_count > $per_page ) ? "<tr><td colspan='4' style='margin:10px;'>".kgPaging( $all_count, $page, " <a href=\"{$PHP_SELF}?mod=balance_account&section=log&paging={p}\">{p}</a> ", $per_page )."</td><td align=\"center\"><input class=\"btn btn-red\" name=\"log_remove\" type=\"submit\" value=\"Удалить\"></td></tr>": "<tr><td colspan=\"4\"></td><td align=\"center\"><input class=\"btn btn-red\" name=\"log_remove\" type=\"submit\" value=\"Удалить\"></td></tr>";
 
echo <<<HTML
<script language='JavaScript' type="text/javascript">
<!--
function ckeck_uncheck_all() {
    var frm = document.balance;
    for (var i=0;i<frm.elements.length;i++) {
        var elmnt = frm.elements[i];
        if (elmnt.type=='checkbox') {
            if(frm.master_box.checked == true){ elmnt.checked=false; }
            else{ elmnt.checked=true; }
        }
    }
    if(frm.master_box.checked == true){ frm.master_box.checked = false; }
    else{ frm.master_box.checked = true; }
}
-->
</script>
<input type="hidden" name="user_hash" value="{$dle_login_hash}">
HTML;
  echo "<table width=\"100%\" class=\"table table-normal\"><tr><td width=\"15%\"><b>Дата и время</b></td><td align='center' width=\"15%\"><b>Пользователь</b></td><td width=\"15%\" align=\"center\"><select onchange=\"location.href=this.value\">{$option_sort}</select></td><td>Комментарий</td><td align=\"center\"><input type=\"checkbox\" name=\"master_box\" title=\"Select all\" onclick=\"javascript:ckeck_uncheck_all()\"></td></tr>".$answer."</table>";

	$ADM_theme->end_copy();
	echofooter();

?>