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

if( !defined( 'DATALIFEENGINE' ) ) {
    die( "Hacking attempt!" );
}

define( 'KG_MODULE', TRUE );
define( 'MODULE_PATH', ENGINE_DIR . "/modules/balance_account" );

/* подключаем вспомогательные файлы модуля */
require_once MODULE_PATH . "/data/user.lng";
require_once MODULE_PATH . "/data/config.php";
require_once ENGINE_DIR . '/classes/parse.class.php';
require_once MODULE_PATH . "/pay.api.php";

  $parse = new ParseFilter( );

   /* для проверки запросов от пс */
	if( $parse->process( trim( $_GET['payment'] ) ) ) {
	
    /* ключ доступа */
    $key_acces = $parse->process( trim( $_GET['key'] ) );
    if( !$key_acces or $key_acces!=$ba_config['paycode']) exit();

    require_once MODULE_PATH . "/admin/paysys.class.php";
    $paysys_info = $Paysys->select();
    
    if( file_exists( MODULE_PATH . "/paysys/{$paysys_info[$_GET['payment']][file]}/{$paysys_info[$_GET['payment']][file]}.get.php" ) )	require_once MODULE_PATH . "/paysys/{$paysys_info[$_GET['payment']][file]}/{$paysys_info[$_GET['payment']][file]}.get.php"; else echo "Error payment.get!";
    die();
	}
	
	if( !$is_logged ) echo $ba_lang['needlogin'];
			else
				require_once ENGINE_DIR . "/modules/balance_account/user/cabinet.php";

	echo $tpl->result['content'];

?>