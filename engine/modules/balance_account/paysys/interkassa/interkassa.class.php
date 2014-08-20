<?php

if( !defined( 'KG_MODULE' )) {
    die( "Открытие страницы вне модуля запрещено!" );
}

Class INTERKASSA {

	var $theme = false;

	function start() {
	  global $config;
		$info = array(
			'icon' => "{$config[http_home_url]}templates/{$config[skin]}/balance_account/icons/interkassa.png",
			'link' => "http://interkassa.com/",
			'desc' => "Сервис решает задачи по организации процесса приема платежей с помощью подключения всех (из возможного множества) платежных систем (Webmoney, RBKMoney, MoneyMail, WebCreds, НСМЭП, Приват24 и другие).",
		);

	  return $info;
	}

	function admin_form( $start_info ) {

		$this->theme->select("Иконка:", "Полный путь до иконки данной платёжной системы.", "<input name=\"save_con[icon]\" class=\"edit bk\" type=\"text\" value=\"{$start_info['icon']}\" size=\"40\">" );
		$this->theme->select("Ссылка:", "Ссылка на сайт платёжной системы.", "<input name=\"save_con[link]\" class=\"edit bk\" type=\"text\" value=\"{$start_info['link']}\" size=\"40\">" );
		$this->theme->select("Краткое описание :", "Краткое описание платёжной системы.", "<input name=\"save_con[desc]\" class=\"edit bk\" type=\"text\" value=\"{$start_info['desc']}\" size=\"40\">" );

		$this->theme->select("Идентификатор магазина (ID):", "Можно получить в <a href='https://new.interkassa.com/account/checkout' target='_blank'>личном кабинете</a>.", "<input name=\"save_con[ik_shop_id]\" class=\"edit bk\" type=\"text\" value=\"{$start_info['ik_shop_id']}\" size=\"40\">" );
		$this->theme->select("Ваш текущий секретный ключ:", "<a href='https://new.interkassa.com/account/checkout' target='_blank'>Настройка кассы</a> вкладка 'Безопасность'", "<input name=\"save_con[secret_key]\" class=\"edit bk\" type=\"text\" value=\"{$start_info['secret_key']}\" size=\"40\">" );

		$this->theme->select("", "", "<p><input class=\"btn btn-green\" name=\"save\" type=\"submit\" value=\"Сохранить\"> <input class=\"btn btn-red\" name=\"remove\" type=\"submit\"  onclick=\"return confirm('Вы действительно хотите удалить эту платёжную систему?')\" value=\"Удалить\"></p>");
		return $this->theme->ec_select;

	}

	function user_form($key_pay, $this_data, $money, $user, $cont) {

		$sing_hash_str = $this_data['ik_shop_id'].':'.	//id магазина
				 $money.':'.			//сумма платежа
				 $key_pay.':'.			//уникальный номер платежа
				 ''.':'.			//способы оплаты
				 $user.':'.			//Пользовательское поле
				 $this_data['secret_key'];	//Секретный ключ магазина

		$sign_hash = strtoupper(md5($sing_hash_str));

		return <<<HTML
      <form name="payment" method="post" action="https://sci.interkassa.com/" accept-charset="UTF-8"> 
      <input type="hidden" name="ik_co_id" value="{$this_data['ik_shop_id']}" /> 
      <input type="hidden" name="ik_pm_no" value="{$key_pay}" /> 
      <input type="hidden" name="ik_am" value="{$money}" /> 
      <input type="hidden" name="ik_desc" value="Пополнение баланса пользователем {$user} на сумму {$money} {$cont}" /> 
      <input type="submit" value="Оплатить"> 
      </form> 
HTML;

	}

}

$payment = new INTERKASSA;
$payment->theme = $ADM_theme;

?>

