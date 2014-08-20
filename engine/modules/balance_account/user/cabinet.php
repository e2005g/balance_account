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

/* Страница: личный кабинет. */

if(!defined('KG_MODULE')) {
  die("Hacking attempt!");
}

require_once MODULE_PATH . "/user/pay.class.php";

$tpl->load_template('balance_account/pay.tpl');
$tpl->copy_template .= "<script type=\"text/javascript\" src=\"{$config['http_home_url']}engine/modules/balance_account/images/script.js\"></script>
                       <input type=\"hidden\" value=\"".$_GET['page']."\" id=\"pagename\">
                        <script type=\"text/javascript\" src=\"{$config['http_home_url']}templates/{$config['skin']}/balance_account/diagram/highcharts.js\"></script>
                        <script type=\"text/javascript\" src=\"{$config['http_home_url']}templates/{$config['skin']}/balance_account/diagram/exporting.js\"></script>";

                $tpl->set( "{balance}", $member_id[$ba_config['user_balance_field']] );
                $tpl->set( "{balance_cont}", $ba_config['money_cont'] );
                $tpl->set( "{navigation}", "<div id=\"payb_navigation\">{$ba_lang['nav_start']}</div>" );
                $tpl->set( "{main_pay}", "<div id=\"payb_main\">".$Pay_Process->menu( $member_id[$ba_config['user_balance_field']], $ba_config['money_cont'] )."</div>" );

$tpl->compile('content');
$tpl->clear();

?>
