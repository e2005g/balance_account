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

/* Ajax. */

@session_start();
@error_reporting ( E_ALL ^ E_WARNING ^ E_NOTICE );
@ini_set ( 'display_errors', true );
@ini_set ( 'html_errors', false );
@ini_set ( 'error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE );

define( 'KG_MODULE', true );
define( 'DATALIFEENGINE', true );
define( 'ROOT_DIR', substr( dirname(  __FILE__ ), 0, -35 ) );
define( 'ENGINE_DIR', ROOT_DIR . '/engine' );
define( 'MODULE_PATH', ENGINE_DIR . "/modules/balance_account" );

include ENGINE_DIR . '/data/config.php';

require_once ENGINE_DIR . '/classes/mysql.php';
require_once ENGINE_DIR . '/data/dbconfig.php';
require_once ENGINE_DIR . '/modules/functions.php';
require_once ENGINE_DIR . '/modules/sitelogin.php';
require_once ENGINE_DIR . '/classes/parse.class.php';
require_once ROOT_DIR . '/language/Russian/adminpanel.lng';

if( !$is_logged  ) {
	die ( "Hacking attempt!" );
}

require_once MODULE_PATH . "/data/user.lng";
require_once MODULE_PATH . "/user/pay.class.php";
require_once MODULE_PATH . "/kg/adm.functions.php";

@header( "Content-type: text/html; charset=" . $config['charset'] );

$parse = new ParseFilter( );

if( in_array($_REQUEST['page'], array('menu', 'bills', 'log', 'moneyback', 'tran', 'pay_next_step', 'paybill', '../admin/load_list_paybills', '../admin/load_paybill_remove')) ) 
  require_once ENGINE_DIR . "/modules/balance_account/user/{$_REQUEST['page']}.php";

?>