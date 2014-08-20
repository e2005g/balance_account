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

if( !defined( 'DATALIFEENGINE' ) OR !LOGED_IN ) {
    die( "Hacking attempt!" );
}
if( $member_id['user_group']!=1 ) {
	msg( "error", $lang['index_denied'], $lang['index_denied'] );
}

define( 'KG_MODULE', TRUE );
define( 'MODULE_PATH', ENGINE_DIR . "/modules/balance_account" );

  /* подключаем вспомогательные файлы модуля */
	require_once MODULE_PATH . "/data/user.lng";
	require_once MODULE_PATH . "/data/config.php";
	require_once MODULE_PATH . "/admin/paysys.class.php";
	require_once MODULE_PATH . "/kg/adm.theme.php";
	require_once MODULE_PATH . "/kg/adm.functions.php";
	require_once MODULE_PATH . "/pay.api.php";

  $get_file = ( in_array($_GET['section'], array('start', 'paysys', 'settings', 'paylog', 'moneyback', 'log', 'deal', 'plugins') ) ) ? $_GET['section'] : "start";

    require_once MODULE_PATH . "/admin/" . $get_file . ".php";
    
?>