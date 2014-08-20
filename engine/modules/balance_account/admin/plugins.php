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

/* Управление плагином */

if( !defined( 'KG_MODULE' ) ) {
    die( "Hacking attempt!" );
}

require_once MODULE_PATH . '/pay.api.php';
require_once ENGINE_DIR . '/classes/parse.class.php';
require_once ENGINE_DIR . '/api/api.class.php';

$parse = new ParseFilter( );

$plugin_url = $parse->process( trim( $_GET['plugin'] ) );

if( !$plugin_url ) $plugin_url = "plugins";

if( !file_exists( MODULE_PATH . "/plugins/{$plugin_url}/info.php") or !isset($plugin_url) )

    msg( "error", "Ошибка", "Плагин не найден", "$PHP_SELF?mod=balance_account" );
    
else {

  require_once MODULE_PATH . "/plugins/{$plugin_url}/info.php";
  require_once MODULE_PATH . "/plugins/{$plugin_url}/admin.php";
   
}
?>