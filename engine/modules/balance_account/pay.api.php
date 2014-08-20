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

/*
  Описание работы API модуля: http://www.nukagame.ru/doc/balance_account/
*/

if( !defined( 'DATALIFEENGINE' ) ) {
    die( "Hacking attempt!" );
}

Class PAY_API {

	var $config = false;
	var $db = false;
	var $user_balance = false;

	function minus($user, $money, $desc, $ya = 1) {

		if($ya and $money > $this->user_balance)	return false;	else {

			$this->db->query( "UPDATE " . USERPREFIX . "_users SET {$this->config[user_balance_field]}={$this->config[user_balance_field]}-'$money' where name='$user'");
			$this->set_log($user, 'minus', $money, $desc);

		 return true;
		}
		
	}

	function plus($user, $money, $desc) {

			$this->db->query( "UPDATE " . USERPREFIX . "_users SET {$this->config[user_balance_field]}={$this->config[user_balance_field]}+'$money' where name='$user'");
			$this->set_log($user, 'plus', $money, $desc);

		 return true;
	}

	function transmit($user_from, $user_to, $money) {

		if( $this->minus($user_from, $money, 'Перевод средств для '.$user_to) )	{ $this->plus($user_to, $money, 'Получен перевод от '.$user_from); return true; } else return false;

	}

	private function set_log($user, $act, $money, $desc) {

		$this->db->query( "INSERT INTO " . PREFIX . "_payments_log (log_user, log_do, log_money, log_cont, log_desc, log_date) values ('$user', '$act', '$money', '".$this->config['money_cont']."', '$desc', '".strtotime(date('j F Y  G:i'))."')" );

	 return true;
	}
	
	public function creat_static_page( $name, $desc, $load, $group = 'all', $metadescr = '', $metakeys = '', $metatitle = '') {
	
    $date = strtotime(date('j F Y  G:i'));
    $this->db->query( "INSERT INTO " . PREFIX . "_static (name, descr, template, allow_br, allow_template, grouplevel, tpl, metadescr, metakeys, template_folder, date, metatitle, 	allow_count, sitemap, disable_index) values ('$name', '$desc', '$load', 1, 1, '$group', 'module', '$metadescr', '$metakeys', '', '$date', '$metatitle', 1, 1, 0)" );
	
    return true;
	}

}

if( !$ba_config ) require_once ENGINE_DIR . "/modules/balance_account/data/config.php";

$pay_api = new PAY_API;
$pay_api->config = $ba_config;
$pay_api->db = $db;
$pay_api->user_balance = $member_id[$ba_config['user_balance_field']];
?>