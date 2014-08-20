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

/* Страница: перевод средств. */

if( $is_logged ) {

  /* Собственный API модуля */
  require_once "../pay.api.php";

  /* Доступ запрещен */
	if( !$ba_config['mback_tran'] ) {
		echo "<div id=\"payb_navigation\"><a href=\"#\" onClick=\"load_page_pay('menu', '')\">{$ba_lang['nav_start']}</a> &raquo; {$ba_lang['nav_tran']}</div><SPLIT><div id=\"payb_main\">{$ba_lang['accesdesc']}</div>";
	   exit();
	} elseif( !in_array($member_id['user_group'], explode(",", $ba_config['acess_tran'])) ) {
		echo "<div id=\"payb_navigation\"><a href=\"#\" onClick=\"load_page_pay('menu', '')\">{$ba_lang['nav_start']}</a> &raquo; {$ba_lang['nav_tran']}</div><SPLIT><div id=\"payb_main\">{$ba_lang['accesgroup']}</div>";
	   exit();
	}

	$for_user = $db->safesql( $parse->process( trim($_GET['pagin'])));
	$unit = ($_GET['unit']>0) ? number_format( str_replace(",", ".", $_GET['unit']) , 2, '.', '') : 0;
	$captcha = $parse->process( trim($_GET['code']));

	/* Форма перевода */
	if( !$for_user OR !$unit ) {

		$content = file_get_contents( ROOT_DIR . "/templates/{$config[skin]}/balance_account/tran.tpl");
		$content = str_replace("{payment_cont}", $ba_config['money_cont'], $content);
    $content = str_replace("{balance}", $member_id[$ba_config['user_balance_field']], $content);

		/* каптча*/
		if( in_array('tran', explode(',',$ba_config['captcha'])) ) {
			$content = str_replace("{payment_captcha}", "<span id=\"dle-captcha\"><img src=\"" . $path['path'] . "engine/modules/antibot/antibot.php\" alt=\"{$lang['sec_image']}\" width=\"160\" height=\"80\" /></span>", $content);
			$content = str_replace("[sec_code]", "", $content);
			$content = str_replace("[/sec_code]", "", $content);
		} else	$content = preg_replace( "'\\[sec_code\\].*?\\[/sec_code\\]'si", "<input name=\"tran_captcha\" id=\"tran_captcha\" type=\"hidden\" />", $content );

  /* Запрос перевода */
	} else {
	
		$content = "";

		$row = $db->super_query( "SELECT user_id FROM " . USERPREFIX . "_users WHERE name='$for_user' LIMIT 1" );

    /* Проверяем форму: пользователь не найден */
    if( !$row['user_id'] )
        $content = str_replace("{user}", $for_user, $ba_lang['tran_er1'])."<br /><a href=\"#\" onClick=\"load_page_pay('tran')\">{$ba_lang['back_re']}</a>.";
    /* ..: недостаточно средств */
    elseif( $unit>$member_id[$ba_config['user_balance_field']] )
        $content = "{$ba_lang['tran_er2']} <br /><a href=\"#\" onClick=\"load_page_pay('tran')\">{$ba_lang['back_re']}</a>.";
    /* ..: перевод самому себе */
    elseif( $member_id['name']==$for_user )
        $content = "{$ba_lang['tran_er3']} <br /><a href=\"#\" onClick=\"load_page_pay('tran')\">{$ba_lang['back_re']}</a>.";
    /* ..: неправильный код каптчи */
    elseif( in_array('tran', explode(',',$ba_config['captcha'])) and (!$captcha OR $captcha!=$_SESSION['sec_code_session']) )
        $content = "{$ba_lang['tran_er4']}<br /><a href=\"#\" onClick=\"load_page_pay('tran')\">{$ba_lang['back_re']}</a>.";
		else {
        /* всё хорошо, переводим */
				$pay_api->transmit($member_id['name'], $for_user, $unit);
				$content = $ba_lang['tran_send'];
		}

	}

  /* Вывод страницы */
	echo "<div id=\"payb_navigation\"><a href=\"#\" onClick=\"load_page_pay('menu')\">{$ba_lang['nav_start']}</a> &raquo; {$ba_lang['nav_tran']}</div><SPLIT><div id=\"payb_main\">{$content}</div>";

} else echo $ba_lang['needlogin'];
	

?>