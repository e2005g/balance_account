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

	require_once MODULE_PATH . "/admin/paysys.class.php";
  require_once MODULE_PATH . "/data/config.php";
  
Class PAY_PROCESS {

	var $paysys = false;
	var $theme = false;
	var $db = false;
	var $mlang = false;

	/*
	  Стартовая страница
	  $balance - баланс пользователя
	  $cont    - наименование валюты
	*/
	function menu($balance, $cont) {

		$get_tpl = file_get_contents( ROOT_DIR . "/templates/".$this->theme."/balance_account/cabinet.tpl");
		$get_tpl = str_replace("{balance}", $balance, $get_tpl);
		$get_tpl = str_replace("{balance_cont}", $cont, $get_tpl);

		return $get_tpl;
	}


	/*
	  Показать все платёжные системы
	*/
	function select_payments() {

		$answer = "";

		foreach($this->paysys->select(1) as $paymeny_altname=>$paymenu_info) {

			$file_tpl = file_get_contents( ROOT_DIR . "/templates/".$this->theme."/balance_account/payment.tpl");		

				$file_tpl = str_replace("{payment_name}", $paymenu_info['name'], $file_tpl);
				$file_tpl = str_replace("{payment_select}", $paymeny_altname, $file_tpl);

					// получаем инфор.
					$this_data = unserialize( $paymenu_info['info'] );

					$file_tpl = str_replace("{payment_info_icon}", $this_data['icon'], $file_tpl);
					$file_tpl = str_replace("{payment_info_link}", $this_data['link'], $file_tpl);
					$file_tpl = str_replace("{payment_info_desc}", $this_data['desc'], $file_tpl);

				if($paymenu_info['name'] and $paymenu_info['status'])	$answer .= $file_tpl;

			 unset($file_tpl); unset($this_data);

		}	if( !$this->paysys->select(1) ) $answer = $this->mlang['pay_c_dsyss'];

	 return $answer;
	}

	/*
	  ПС выбрана, вывод формы
	  $paysys - платёжная система (altname)
	*/
	function setunit($paysys) {
    global $ba_config, $lang, $config, $path;

		$paysystems = $this->paysys->select(1);
		$paysystem = $paysystems[$paysys];
		
		$file_tpl = file_get_contents( ROOT_DIR . "/templates/".$this->theme."/balance_account/payunit.tpl");
		$file_tpl = str_replace("{payment_select}", $paysys, $file_tpl);

		/* каптча*/
		if( in_array('pay', explode(',',$ba_config['captcha'])) ) {
			$file_tpl = str_replace("{payment_captcha}", "<span id=\"dle-captcha\"><img src=\"" . $path['path'] . "engine/modules/antibot/antibot.php\" alt=\"{$lang['sec_image']}\" width=\"160\" height=\"80\" /></span>", $file_tpl);
			$file_tpl = str_replace("[sec_code]", "", $file_tpl);
			$file_tpl = str_replace("[/sec_code]", "", $file_tpl);
		} else	$file_tpl = preg_replace( "'\\[sec_code\\].*?\\[/sec_code\\]'si", "<input name=\"pay_captcha\" id=\"pay_captcha\" type=\"hidden\" />", $file_tpl );

			$file_tpl = str_replace("{payment_name}", $paysystem['name'], $file_tpl);
				
			$this_data = unserialize( $paysystem[info] );

					$file_tpl = str_replace("{payment_info_icon}", $this_data['icon'], $file_tpl);
					$file_tpl = str_replace("{payment_info_link}", $this_data['link'], $file_tpl);
					$file_tpl = str_replace("{payment_info_desc}", $this_data['desc'], $file_tpl);
					$file_tpl = str_replace("{payment_info_cont}", $this_data['cont'], $file_tpl);
					
			if(!$paysystem['name'] OR !$paysystem['status'])	$file_tpl = $this->mlang['pay_c_dsyss'];

		return $file_tpl;	

	}

	/*
	  Создание счёта
	  $paysys - платёжная система (altname)
	  $money  - заявленная сумма для оплаты
	  $user	  - логин
	*/
	function creat_payment($paysys, $money, $user, $captcha = "") {	global $ba_config;

		$paysystems = $this->paysys->select(1);
		$paysystem = $paysystems[$paysys];

		$stop = "";
		if( !$money )						$stop .= $this->mlang['pay_c_er1'];

		// получаем config платёжной системы
		$this_data = unserialize( $paysystem[info] );	
		
		if( $money<$this_data['minpay'] )			$stop .= str_replace("{min_money}", "{$this_data['minpay']} {$this_data['cont']}", $this->mlang['pay_c_er2']);
		
		if( !$paysystem['name'] OR !$paysystem['status'])		$stop = $this->mlang['pay_c_dsyss'];

		/* каптча*/
		if( in_array('pay', explode(',',$ba_config['captcha'])) ) {
			if( !$captcha OR $captcha!=$_SESSION['sec_code_session'])
									$stop = $this->mlang['pay_c_er3'];
		}

		if( !$this_data )					$stop = $this->mlang['pay_c_er4'];

      /* Ошибка: вывод */
			if( $stop ) {
				$file_tpl = file_get_contents( ROOT_DIR . "/templates/".$this->theme."/balance_account/payerror.tpl");
				$file_tpl = str_replace("{errors}", $stop, $file_tpl);
				$file_tpl = str_replace("{payment_select}", $paysys, $file_tpl);	
				return $file_tpl;
			}

		/* Формируем счёт */
		$this->db->query( "INSERT INTO " . PREFIX . "_payments (payment_user, payment_datecreat, payment_datepay, payment_money, payment_cont, payment_system) values ('$user', '".strtotime(date('j F Y  G:i'))."', '0', '$money', '".$this_data['cont']."', '$paysys')" );
		$key_pay = $this->db->insert_id();

			$file_tpl = file_get_contents( ROOT_DIR . "/templates/".$this->theme."/balance_account/payend.tpl");
			$file_tpl = str_replace("{shot_id}", $key_pay, $file_tpl);
			$file_tpl = str_replace("{shot_money}", $money, $file_tpl);
			$file_tpl = str_replace("{shot_money_cont}", $this_data['cont'], $file_tpl);
			$file_tpl = str_replace("{shot_payment}", $paysystem['name'], $file_tpl);

		return $file_tpl;

	}

	/*
	  Счёт для оплаты
	  $paysys	- платёжная система (altname)
	  $key_pay	- уник. ключ счёта
	  $user		- логин пользователя
	  $money	- сумма к оплате
	*/
	function get_bill($paysys, $key_pay, $user, $money) {

		$paysystems = $this->paysys->select(1);
		$paysystem = $paysystems[$paysys];

		// получаем config платёжной системы
		$this_data = unserialize( $paysystem['info'] );

		if(file_exists( MODULE_PATH . "/paysys/{$paysystem[file]}/{$paysystem[file]}.class.php" )) {
			require_once MODULE_PATH . "/paysys/{$paysystem[file]}/{$paysystem[file]}.class.php";
		
			$file_tpl = file_get_contents( ROOT_DIR . "/templates/".$this->theme."/balance_account/paybill.tpl");
			$file_tpl = str_replace("{shot_id}", $key_pay, $file_tpl);
			$file_tpl = str_replace("{shot_unit}", $unit, $file_tpl);
			$file_tpl = str_replace("{shot_money}", $money, $file_tpl);
			$file_tpl = str_replace("{shot_money_cont}", $this_data['cont'], $file_tpl);
			$file_tpl = str_replace("{shot_payment}", $paysystem['name'], $file_tpl);
			$file_tpl = str_replace("{shor_pay}", $payment->user_form($key_pay, $this_data, $money, $user, $this_data['cont']), $file_tpl);			

			return $file_tpl;
		} else	return "Error payment.class!";

	}

}

$Pay_Process = new PAY_PROCESS;
$Pay_Process->paysys = $Paysys;
$Pay_Process->theme = $config['skin'];
$Pay_Process->db = $db;
$Pay_Process->mlang = $ba_lang;
?>