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

/* Платёжные системы */

if( !defined( 'KG_MODULE' ) ) {
    die( "Hacking attempt!" );
}

	/* Форма: изменить */
	if( isset($_GET['ps_name']) ) {

			/* Сохранить */
			if( isset($_POST['save']) ) {
        if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {       
          die( "Hacking attempt! User not found {$_REQUEST['user_hash']}" );   
        } 				
				
				$name = $_POST['edit_name'];
				$status = intval($_POST['edit_status']);
				$save_con = $_POST['save_con'];

				$Paysys->edit($_GET['ps_name'], $name, $status, serialize($save_con));

					msg( "info", "Действие выполнено!", "Платёжная система сохранена", "$PHP_SELF?mod=balance_account&section=paysys" );

      /* Удалить */
			} else if( isset($_POST['remove']) ) {
				if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) die( "Hacking attempt! User not found {$_REQUEST['user_hash']}" );
					
				$Paysys->remove($_GET['ps_name']);
				
				msg( "info", "Действие выполнено!", "Платёжная система удалена", "$PHP_SELF?mod=balance_account&section=paysys" );
			}

    /* Форма редактирования */
		echoheader( "Баланс пользователя v.".$ba_config['version'], "Кошелёк пользователя с возможностью его пополнения, перевода, вывода средств.");
		$ADM_theme->start("<a href='{$PHP_SELF}?mod=balance_account'>Баланс пользователя v.{$ba_config[version]}</a> &raquo; <a href='{$PHP_SELF}?mod=balance_account&section=paysys'>Платёжные системы</a> &raquo; Настройки");

		$paysys_info = $Paysys->select();

    /* Получаем данные пс */
		if( file_exists( MODULE_PATH . "/paysys/{$paysys_info[$_GET['ps_name']][file]}/{$paysys_info[$_GET['ps_name']][file]}.class.php" ) )
			require_once MODULE_PATH . "/paysys/{$paysys_info[$_GET['ps_name']][file]}/{$paysys_info[$_GET['ps_name']][file]}.class.php";

		if( $paysys_info[$_GET['ps_name']] ) {

			$ADM_theme->select("URL проверки платежа:", "Result URL, нужен для настройки платёжной системы.", "<b>{$config['http_home_url']}index.php?do=static&page=balance&payment={$_GET['ps_name']}&key={$ba_config['paycode']}</b>");
			$ADM_theme->select("Название:", "Данное название увидят ваши пользователи.", "<input name=\"edit_name\" class=\"edit bk\" type=\"text\" value=\"{$paysys_info[$_GET['ps_name']][name]}\" size=\"40\"><input type=\"hidden\" name=\"user_hash\" value=\"{$dle_login_hash}\" />");
			$ADM_theme->select("Включить систему платежей:", "Разрешить пользователям пополнять свой баланс с помощью данной платёжной системы.", makeCheckBox("edit_status", $paysys_info[$_GET['ps_name']][status]));

			$start_info = unserialize($paysys_info[$_GET['ps_name']]['info']);

			$ADM_theme->select("Цена 1 ед. данной валюты:", "Относительно валюты на сайте.", "1 ед. = <input name=\"save_con[cu_one]\" class=\"edit bk\" type=\"text\" value=\"{$start_info['cu_one']}\" size=\"10\"> {$ba_config[money_cont]}" );
			$ADM_theme->select("Валюта пополнения баланса:", "Выберите валюту, которой будет произведена оплата по счёту через данную платёжную систему.", "<input name=\"save_con[cont]\" class=\"edit bk\" type=\"text\" value=\"{$start_info['cont']}\" size=\"40\">" );
			$ADM_theme->select("Мин. сумма платежа:", "Минимальная сумма платежа через данную платёжную систему", "<input name=\"save_con[minpay]\" class=\"edit bk\" type=\"text\" value=\"{$start_info['minpay']}\" size=\"40\">" );


			echo "<table width=\"100%\" class=\"table table-normal\">".$payment->admin_form( $start_info )."</table>";

		} else	header('Location: '.$PHP_SELF.'?mod=balance_account&section=paysys');

		$ADM_theme->end_copy();
		echofooter();
	 die();
	}

	/* Добавляем пс */
	if( isset($_POST['add']) ) {
		if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) die( "Hacking attempt! User not found {$_REQUEST['user_hash']}" );  

		$errors = "";
		$paysys_info = $Paysys->select();

    /* Разбор ошибок */
		if( preg_replace('/[^a-zа-яA-Z0-9\s]/', '', $_POST['add_name']) ) $name = $_POST['add_name'];
			else $errors = "Укажите название платёжной системы.";
		if( $_POST['add_file'] ) $file = $_POST['add_file'];
			else $errors = "Выберите файл платёжной системы.";
		if(!file_exists( MODULE_PATH . "/paysys/{$file}/{$file}.class.php" ) and $file)
			     $errors = "Файл /paysys/{$file}/{$file}.class.php платёжной системы не найден.";
		if(!file_exists( MODULE_PATH . "/paysys/{$file}/{$file}.get.php" ) and $file)
			     $errors = "Файл /paysys/{$file}/{$file}.get.php платёжной системы не найден.";

        /* Сохраняем новую пс */
        if( $errors )	msg( "info", "Ошибка", "{$errors}", "javascript:history.go(-1)" );
        else {

          require_once MODULE_PATH . "/paysys/{$file}/{$file}.class.php";
          $payment_config = serialize( $payment->start() );

          $Paysys->add($name, $file, $payment_config);
          msg( "info", "Действие выполнено!", "Платёжная система успешно добавлена", "$PHP_SELF?mod=balance_account&section=paysys" );

        }

	}

  /* Вывод страницы */
	echoheader( "Баланс пользователя v.".$ba_config[version], "Кошелёк пользователя с возможностью его пополнения, перевода, вывода средств.");
	$ADM_theme->start("<a href='{$PHP_SELF}?mod=balance_account'>Баланс пользователя v.{$ba_config[version]}</a> &raquo; Платёжные системы");

    /* Список пс */
		$get_paysys_list = @file("engine/modules/balance_account/data/paysys.dat.php");
		
		if( $get_paysys_list ) {

			foreach($get_paysys_list as $paysys_line) {
          $paysys_arr = explode("|", $paysys_line);
          $paysys_list .= "<tr><td><a href='{$PHP_SELF}?mod=balance_account&section=paysys&ps_name={$paysys_arr[0]}'>{$paysys_arr[2]}</a></td><td>/{$paysys_arr[1]}/</td><td align='center'>".switch_status($paysys_arr[3])."</td></tr>";
      }

		} else	$paysys_list = "<tr><td colspan='3'>Список платёжных систем пуст.</td></tr>";
		
		echo "<table width='100%' style='margin:10px;' class='table table-normal'><tr><td width='57%' valign='top'>
            <table width='100%' class='table table-normal'>
                <tr>
                  <td width='30%'><b>Название<b></td>
                  <td width='30%'><b>Папка<b></td>
                  <td width='30%' align='center'><b>Статус<b></td>
                </tr>
                {$paysys_list}
            </table>
            <br /><center><a href=\"{$PHP_SELF}?mod=balance_account&section=plugins&plugin=plugins&select=payments\"><b>Загрузить больше платёжных систем</b></a></center>";
		echo "</td><td witch='2'>&nbsp;</td><td valign='top'>";

	/* Форма для добавления пс */
	$payments = "<option value=''>- выберите -</option>";
	$handle = opendir('engine/modules/balance_account/paysys/');
	while ($folder = readdir($handle)) {
     if (!in_array($folder, array(".", "..", "/", "index.php"))) $payments .= "<option value='{$folder}'>/{$folder}/</option>";
  }

	echo <<<HTML
	<table width="90%">
    <tr>
        <td class="col-xs-10 col-sm-6 col-md-7"><b>Название:</b></td>
        <td class="col-xs-2 col-md-5 settingstd"><input name="add_name" type="text" size="15"></td>
    </tr>
    <tr>
        <td class="col-xs-10 col-sm-6 col-md-7"><b>Платёжная система:</b></span></td>
        <td class="col-xs-2 col-md-5 settingstd"><select name="add_file">{$payments}</select></td>
    </tr>
    <tr>
        <td></td>
        <td style='padding-top:7px;'><input class="btn btn-green" name="add" type="submit" value="Добавить"><input type="hidden" name="user_hash" value="{$dle_login_hash}"></td>
    </tr>
  </table>
</td></table>
HTML;

	$ADM_theme->end_copy();
	echofooter();

?>