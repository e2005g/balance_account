<?php

if( !defined( 'KG_MODULE' )) {
    die( "Открытие страницы вне модуля запрещено!" );
}

Class Z_PAYMENT {

	var $theme = false;

	function start() {
	  global $config;
	  
		$info = array(
			'icon' => "{$config[http_home_url]}templates/{$config[skin]}/balance_account/icons/z-payment.png",
			'link' => "https://z-payment.com/",
			'desc' => "Z-Payment — электронная платёжная система, интегрирующая различные виды оплаты, такие, как оплата по SMS, банковские переводы, оплата пластиковой картой и другие.",
		);

	  return $info;
	}

	function admin_form( $start_info ) {

		$this->theme->select("Иконка:", "Полный путь до иконки данной платёжной системы.", "<input name=\"save_con[icon]\" class=\"edit bk\" type=\"text\" value=\"{$start_info['icon']}\" size=\"40\">" );
		$this->theme->select("Ссылка:", "Ссылка на сайт платёжной системы.", "<input name=\"save_con[link]\" class=\"edit bk\" type=\"text\" value=\"{$start_info['link']}\" size=\"40\">" );
		$this->theme->select("Краткое описание :", "Краткое описание платёжной системы.", "<input name=\"save_con[desc]\" class=\"edit bk\" type=\"text\" value=\"{$start_info['desc']}\" size=\"40\">" );

		$this->theme->select("Идентификатор магазина", "Целое число - идентификатор магазина в системе Z-Payment Merchant. Назначается автоматически сервисом при создании нового магазина.", "<input name=\"save_con[purse]\" class=\"edit bk\" type=\"text\" value=\"{$start_info['purse']}\" size=\"40\">" );
		$this->theme->select("Секретный ключ Merchant Key:", "Задаётся в настройках уведомления вашего магазина.", "<input name=\"save_con[merchant_key]\" class=\"edit bk\" type=\"text\" value=\"{$start_info['merchant_key']}\" size=\"40\">" );
		$this->theme->select("Пароль инициализации магазина:", "Задаётся в настройках уведомления вашего магазина.", "<input name=\"save_con[access_pass]\" class=\"edit bk\" type=\"text\" value=\"{$start_info['access_pass']}\" size=\"40\">" );

		$this->theme->select("", "", "<p><input class=\"btn btn-green\" name=\"save\" type=\"submit\" value=\"Сохранить\"> <input class=\"btn btn-red\" name=\"remove\" type=\"submit\"  onclick=\"return confirm('Вы действительно хотите удалить эту платёжную систему?')\" value=\"Удалить\"></p>");
		return $this->theme->ec_select;

	}

	function user_form($key_pay, $this_data, $money, $user, $cont) {

    $hash = md5( $this_data['purse'].$key_pay.$money.$this_data['access_pass']);

		return <<<HTML
			<form id="pay" name="pay" method="post" action="https://z-payment.com/merchant.php">

			<input name="LMI_PAYMENT_NO" type="hidden" value="{$key_pay}">
			<input name="LMI_PAYMENT_AMOUNT" type="hidden" value="{$money}">
			<input name="LMI_PAYMENT_DESC" type="hidden" value="Пополнение баланса пользователем {$user} на сумму {$money} {$cont}">
      <input name="LMI_PAYEE_PURSE" type="hidden" value="{$this_data['purse']}">
      <input name="ZP_SIGN" type="hidden" value="{$hash}">
			<input class="fbutton" value="Оплатить" type="submit">

			</form>
HTML;

	}

}

$payment = new Z_PAYMENT;
$payment->theme = $ADM_theme;

?>

