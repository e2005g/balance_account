<?php

if( !defined( 'KG_MODULE' )) {
    die( "Открытие страницы вне модуля запрещено!" );
}

Class WEBMONEY {

	var $theme = false;

	function start() {
	  global $config;
		$info = array(
			'icon' => "{$config[http_home_url]}templates/{$config[skin]}/balance_account/icons/webmoney_r.png",
			'link' => "http://www.webmoney.ru/",
			'desc' => "WebMoney — это универсальное средство для расчетов в Сети, целая среда финансовых взаимоотношений, которой сегодня пользуются миллионы людей по всему миру. ",
		);

	  return $info;
	}

	function admin_form( $start_info ) {

		$this->theme->select("Иконка:", "Полный путь до иконки данной платёжной системы.", "<input name=\"save_con[icon]\" class=\"edit bk\" type=\"text\" value=\"{$start_info['icon']}\" size=\"40\">" );
		$this->theme->select("Ссылка:", "Ссылка на сайт платёжной системы.", "<input name=\"save_con[link]\" class=\"edit bk\" type=\"text\" value=\"{$start_info['link']}\" size=\"40\">" );
		$this->theme->select("Краткое описание :", "Краткое описание платёжной системы.", "<input name=\"save_con[desc]\" class=\"edit bk\" type=\"text\" value=\"{$start_info['desc']}\" size=\"40\">" );

		$this->theme->select("Кошелек продавца:", "Кошелек продавца, на который покупатель должен совершить платеж. Формат – буква и 12 цифр. В настоящее время допускается использование кошельков Z-,R-,E-,U- и D-типа.", "<input name=\"save_con[wm_purse]\" class=\"edit bk\" type=\"text\" value=\"{$start_info['wm_purse']}\" size=\"40\">" );
		$this->theme->select("Секретный ключ:", "Задаётся в настройках торгового кошелько WebMoney.", "<input name=\"save_con[wm_secretkey]\" class=\"edit bk\" type=\"text\" value=\"{$start_info['wm_secretkey']}\" size=\"40\">" );

		$this->theme->select("", "", "<p><input class=\"btn btn-green\" name=\"save\" type=\"submit\" value=\"Сохранить\"> <input class=\"btn btn-red\" name=\"remove\" type=\"submit\"  onclick=\"return confirm('Вы действительно хотите удалить эту платёжную систему?')\" value=\"Удалить\"></p>");
		return $this->theme->ec_select;

	}

	function user_form($key_pay, $this_data, $money, $user, $cont) {

		return <<<HTML
			<form method="post" accept-charset="windows-1251" action="https://merchant.webmoney.ru/lmi/payment.asp">

			<input name="lmi_payment_desc" value="Пополнение баланса пользователем {$user} на сумму {$money} {$cont}" type="hidden">
			<input name="lmi_payment_no" value="{$key_pay}" type="hidden">
			<input name="lmi_payment_amount" value="{$money}" type="hidden">
			<input name="lmi_sim_mode" value="0" type="hidden">
			<input name="lmi_payee_purse" value="{$this_data[wm_purse]}" type="hidden">

			<input class="fbutton" value="Оплатить" type="submit">

			</form>
HTML;

	}

}

$payment = new WEBMONEY;
$payment->theme = $ADM_theme;

?>

