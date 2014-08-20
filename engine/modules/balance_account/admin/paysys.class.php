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

if( !defined( 'KG_MODULE' ) ) {
    die( "Hacking attempt!" );
}

Class PAYSYS {

	function add($name, $file, $data) {
    $altname = totranslit( $name );
		$db_file = fopen( MODULE_PATH . "/data/paysys.dat.php", "a");
    fwrite($db_file, "{$altname}|{$file}|{$name}|0|{$data}|\n");
    fclose($db_file);

	 return true;
	}

	function select($select=0) {

		$get_list = @file( MODULE_PATH . "/data/paysys.dat.php");

		if( count($get_list) ) {
			foreach($get_list as $line) {
				$ps_arr = explode("|", $line);

           if(!$select OR ($select and $ps_arr[2])) {
            $answer[$ps_arr[0]] = array();
            $answer[$ps_arr[0]]['name'] = $ps_arr[2];
            $answer[$ps_arr[0]]['file'] = $ps_arr[1];
            $answer[$ps_arr[0]]['status'] = $ps_arr[3];
            $answer[$ps_arr[0]]['info'] = $ps_arr[4];
          }
			}
		}

	   return $answer;
	}

	function edit($altname, $name, $status, $payment_config) {

		$list = file( MODULE_PATH . "/data/paysys.dat.php");
		$list_new = fopen( MODULE_PATH . "/data/paysys.dat.php","w");

		if($list) {
			foreach($list as $value) {
        $value_arr = explode("|", $value);
          if( $value_arr[0] != $altname ) fwrite($list_new, $value);
          else fwrite($list_new, "{$altname}|{$value_arr[1]}|{$name}|{$status}|{$payment_config}|\n");
			}
		}
		
	   return true;
	}

	function remove($altname) {

		$list = file( MODULE_PATH . "/data/paysys.dat.php");
		$list_new = fopen( MODULE_PATH . "/data/paysys.dat.php","w");

		if($list) {
			foreach($list as $value) {
        $value_arr = explode("|", $value);
          if( $value_arr[0] != $altname ) fwrite($list_new, $value);
			}
		}
	   return true;
	}

  function search_pay($key) {
    global $db;

    $row = $db->super_query( "SELECT * FROM " . USERPREFIX . "_payments WHERE payment_id='".intval($key)."'" );

    if( count($row) ) {
      return $row;
    }

   return false;
  }

  function pay_paybill($key, $user, $money, $cont, $coef, $payment_name, $config) {
    global $db;

    $db->query( "UPDATE " . USERPREFIX . "_payments SET payment_datepay='".strtotime(date('j F Y  G:i'))."' WHERE payment_id='$key'" );	

    $pay_api = new PAY_API;
    $pay_api->db = $db;
    $pay_api->config = $config;

      if($cont!=$config['money_cont']) {
        $money_conv = $money * $coef;

        $pay_api->plus($user, $money_conv, "Счёт #{$key} оплачен через систему {$payment_name}. Сумма до конвертации {$money} {$cont}.");
      } else $pay_api->plus($user, $money, "Счёт #{$key} оплачен через систему {$payment_name}."); 

   return true;
  }

}

$Paysys = new PAYSYS;

?>