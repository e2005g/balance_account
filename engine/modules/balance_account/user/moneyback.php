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

/* Страница: вывод средств. */

if( $is_logged ) {

  require_once "../pay.api.php";

  /* навигация */
	if( !$ba_config['mback_status'] ) {
		echo "<div id=\"payb_navigation\"><a href=\"#\" onClick=\"load_page_pay('menu', '')\">{$ba_lang['nav_start']}</a> &raquo; {$ba_lang['nav_moneyvack']}</div><SPLIT><div id=\"payb_main\">{$ba_lang['accesdesc']}</div>";
	   exit();
	} elseif( !in_array($member_id['user_group'], explode(",", $ba_config['acess_outmoney'])) ) {
		echo "<div id=\"payb_navigation\"><a href=\"#\" onClick=\"load_page_pay('menu', '')\">{$ba_lang['nav_start']}</a> &raquo; {$ba_lang['nav_moneyvack']}</div><SPLIT><div id=\"payb_main\">{$ba_lang['accesgroup']}</div>";
	   exit();
	}

	/* Форма вывода */
	$file_tpl = file_get_contents( ROOT_DIR . "/templates/{$config[skin]}/balance_account/moneyback_form.tpl");
	$file_tpl = str_replace("{payment_cont}", $ba_config['money_cont'], $file_tpl);
	$file_tpl = str_replace("{outmoney_min}", $ba_config['mback_min'], $file_tpl);

		/* каптча*/
		if( in_array('outmoney', explode(',',$ba_config['captcha'])) ) {
			$file_tpl = str_replace("{payment_captcha}", "<span id=\"dle-captcha\"><img src=\"" . $path['path'] . "engine/modules/antibot/antibot.php\" alt=\"{$lang['sec_image']}\" width=\"160\" height=\"80\" /></span>", $file_tpl);
			$file_tpl = str_replace("[sec_code]", "", $file_tpl);
			$file_tpl = str_replace("[/sec_code]", "", $file_tpl);
		} else	$file_tpl = preg_replace( "'\\[sec_code\\].*?\\[/sec_code\\]'si", "<input name=\"mb_captcha\" id=\"mb_captcha\" type=\"hidden\" />", $file_tpl );
		
		/* комиссия */
		$type_com = explode(":", $ba_config['mback_com']);
    $outmoney_com = ( $type_com[1]=="percent" ) ? $type_com[0]."%": $type_com[0]." ".$ba_config['money_cont'];
		$file_tpl = str_replace("{outmoney_com}", $outmoney_com, $file_tpl);

	/* Создание вывода */
	$money = ($_GET['money']>0) ? number_format( str_replace(",", ".", $_GET['money']) , 2, '.', '') : "";
  if( $money ) {

		$info = $parse->process( trim($db->safesql($_GET['info'])) );
		$member_id['name'] = $db->safesql( $member_id['name'] );
		$ba_config['money_cont'] = $db->safesql( $ba_config['money_cont'] );
		$captcha = $parse->process( trim($_GET['code']) );

    /* Проверяем форму: незаполнены поля */
		if( !$money OR !$info )						$file_tpl = "{$ba_lang['mb_er1']} <br /><a href=\"#\" onClick=\"load_page_pay('moneyback', '')\">{$ba_lang['back_re']}</a>.";
    /* ..: недостаточно средств */
		elseif( $money>$member_id[$ba_config['user_balance_field']] )	$file_tpl = "{$ba_lang['mb_er2']} <br /><a href=\"#\" onClick=\"load_page_pay('moneyback', '')\">{$ba_lang['back_re']}</a>.";
    /* ..: несоблюден минимум */
		elseif( $money<$ba_config['mback_min'] )				$file_tpl = str_replace("{minsumma}", "{$ba_config[mback_min]} {$ba_config[money_cont]}", $ba_lang['mb_er3'])."<br /><a href=\"#\" onClick=\"load_page_pay('moneyback', '')\">{$ba_lang['back_re']}</a>.";
    /* ..: недостаточно средств с учетом фикс. комиссии */
		elseif( $type_com[1]=="fixed" and $money<=$type_com[0] )		$file_tpl = str_replace("{com}", "{$type_com[0]} {$ba_config['money_cont']}", $ba_lang['mb_er4'])."<br /><a href=\"#\" onClick=\"load_page_pay('moneyback', '')\">{$ba_lang['back_re']}</a>.";
    /* ..: неверный код проверки (каптча) */
		elseif( in_array('outmoney', explode(',',$ba_config['captcha'])) and (!$captcha OR $captcha!=$_SESSION['sec_code_session']) ) 
										$file_tpl = "{$ba_lang['mb_er5']}<br /><a href=\"#\" onClick=\"load_page_pay('moneyback', '')\">{$ba_lang['back_re']}</a>.";
    /* ..: всё верно, сохраняем */
		else {

			/* комиссия */
			if($type_com[1]=="fixed") { $minus_money = $money - $type_com[0]; $money_com = $type_com[0]; } else { $minus_money = $money - (($money/100) * $type_com[0]); $money_com = ($money/100) * $type_com[0]; }

				$db->query( "INSERT INTO " . PREFIX . "_payments_outmoney (om_user, om_money, om_back, om_cont, om_date_creat, om_date_pay, om_desc) values ('$member_id[name]', '$minus_money', '$money', '$ba_config[money_cont]', '".strtotime(date('j F Y  G:i'))."', '0', '$info')" );
				$desc_pay = str_replace("{money}", "{$minus_money} {$ba_config[money_cont]}", str_replace("{com}", "{$money_com} {$ba_config[money_cont]}", $ba_lang['mb_send']) );
				$pay_api->minus($member_id['name'], $money, $desc_pay);

		}

  }

	/* Отмена */
	if( $_GET['pagin']=="delmb" ) {
	
		$del_id = intval( $_GET['id'] );
    $member_id['name'] = $db->safesql( $member_id['name'] );
    
		if($del_id)	{
			$row = $db->super_query( "SELECT * FROM " . USERPREFIX . "_payments_outmoney WHERE om_user='$member_id[name]' and om_id='$del_id'" );

			/* Возврат средств */
			if( count($row) ) {
				if(!$row['om_date_pay']) $pay_api->plus($row['om_user'], $row['om_back'], $ba_lang['mb_send_re']);
				$db->query( "DELETE FROM " . USERPREFIX . "_payments_outmoney WHERE om_user='$member_id[name]' and om_id='$del_id'" );
			}
			
		}
		
	}

	/* Вывод всех заявок */
	$content = file_get_contents( ROOT_DIR . "/templates/{$config[skin]}/balance_account/moneyback_list.tpl");

  switch( $_GET['pagin'] ) {
    case "ok":
      $sort_mb = "om_date_pay!='0'";
    break;
    case "new":
      $sort_mb = "om_date_pay='0'";
    break;
    default:
      $sort_mb = "om_date_creat!='0'";
  }

	$db->query( "SELECT * FROM " . USERPREFIX . "_payments_outmoney where om_user ='{$member_id[name]}' and {$sort_mb} ORDER BY om_id desc limit 10" );

	while ( $row = $db->get_row() ) {
	
    $backlink = ( $row['om_date_pay'] ) ? "<font color='green' title='одобрено'>".langdate( "j F Y  G:i", $row['om_date_pay'])."<font>": "<a href=\"#\" onClick=\"kgba_delmb('{$row[om_id]}')\">{$ba_lang['mb_x']}</a>";
		$content_p .= "<tr><td>".langdate( "j F Y  G:i", $row['om_date_creat'])."</td><td>{$row[om_money]} {$row[om_cont]}</td><td>{$row[om_desc]}</td><td>{$backlink}</td></tr>";

	}	if(!$content_p) $content_p = "<tr><td colspan=\"4\">{$ba_lang['mb_notlist']}</td></tr>";

	$content = str_replace("{list}", $content_p, $content);

	echo "<div id=\"payb_navigation\"><a href=\"#\" onClick=\"load_page_pay('menu')\">{$ba_lang['nav_start']}</a> &raquo; {$ba_lang['nav_moneyvack']}</div><SPLIT><div id=\"payb_main\">{$file_tpl}{$content}</div>";

} else echo $ba_lang['needlogin'];
	

?>