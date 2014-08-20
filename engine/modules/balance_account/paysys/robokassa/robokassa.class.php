<?php

if( !defined( 'KG_MODULE' )) {
    die( "Открытие страницы вне модуля запрещено!" );
}

Class ROBOKASSA {

	var $theme = false;

	function start() {
	  global $config;
		$info = array(
			'icon' => "{$config[http_home_url]}templates/{$config[skin]}/balance_account/icons/robokassa.png",
			'link' => "http://www.robokassa.ru/",
			'desc' => "Сервис работает с системой WebMoney, Яндекс.Деньги, Единый кошелёк, Money Mail, EasyPay, LiqPay, RBK Money, E-gold и другими электронными валютами. ",
		);

	  return $info;
	}

	function admin_form( $start_info ) {

		$this->theme->select("Иконка:", "Полный путь до иконки данной платёжной системы.", "<input name=\"save_con[icon]\" class=\"edit bk\" type=\"text\" value=\"{$start_info['icon']}\" size=\"40\">" );
		$this->theme->select("Ссылка:", "Ссылка на сайт платёжной системы.", "<input name=\"save_con[link]\" class=\"edit bk\" type=\"text\" value=\"{$start_info['link']}\" size=\"40\">" );
		$this->theme->select("Краткое описание :", "Краткое описание платёжной системы.", "<input name=\"save_con[desc]\" class=\"edit bk\" type=\"text\" value=\"{$start_info['desc']}\" size=\"40\">" );

		$this->theme->select("Логин магазина:", "Вам логин в системе Робокасса.", "<input name=\"save_con[login]\" class=\"edit bk\" type=\"text\" value=\"{$start_info['login']}\" size=\"40\">" );
		$this->theme->select("Пароль #1:", "Используется интерфейсом инициализации оплаты.", "<input name=\"save_con[pass1]\" class=\"edit bk\" type=\"text\" value=\"{$start_info['pass1']}\" size=\"40\">" );
		$this->theme->select("Пароль #2:", "Используется интерфейсом оповещения о платеже, XML-интерфейсах.", "<input name=\"save_con[pass2]\" class=\"edit bk\" type=\"text\" value=\"{$start_info['pass2']}\" size=\"40\">" );

		$this->theme->select("", "", "<p><input class=\"btn btn-green\" name=\"save\" type=\"submit\" value=\"Сохранить\"> <input class=\"btn btn-red\" name=\"remove\" type=\"submit\"  onclick=\"return confirm('Вы действительно хотите удалить эту платёжную систему?')\" value=\"Удалить\"></p>");
		return $this->theme->ec_select;

	}

	function user_form($key_pay, $this_data, $money, $user, $cont) {

		$sign_hash = md5("$this_data[login]:$money:$key_pay:$this_data[pass1]");

		return <<<HTML
			<form name="payment" action="https://merchant.roboxchange.com/Index.aspx" method="post" />

			<input type=hidden name=MerchantLogin value=$this_data[login]>
			<input type=hidden name=OutSum value=$money>
			<input type=hidden name=InvId value=$key_pay>
			<input type=hidden name=Desc value='Пополнение баланса пользователем {$user} на сумму {$money} {$cont}'>
			<input type=hidden name=SignatureValue value=$sign_hash>

				<input type="submit" name="process" value="Оплатить" />
			</form>
HTML;

	}

}

$payment = new ROBOKASSA;
$payment->theme = $ADM_theme;

?>

