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

/* Установка модуля */

error_reporting(E_ALL ^ E_NOTICE);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
@ini_set('error_reporting', E_ALL ^ E_NOTICE);

define('DATALIFEENGINE', true);
define('ROOT_DIR', dirname (__FILE__));
define('ENGINE_DIR', ROOT_DIR.'/engine');

require_once(ROOT_DIR.'/language/Russian/adminpanel.lng');
require_once(ENGINE_DIR.'/inc/include/functions.inc.php');
require_once(ENGINE_DIR.'/skins/default.skin.php');
require_once(ENGINE_DIR.'/data/config.php');
require_once (ENGINE_DIR . '/inc/include/init.php');
require_once (ENGINE_DIR . '/api/api.class.php');

require_once (ENGINE_DIR . '/modules/balance_account/data/config.php');
require_once (ENGINE_DIR . '/modules/balance_account/kg/adm.functions.php');
require_once (ENGINE_DIR . '/modules/balance_account/pay.api.php');

$title = "Баланс пользователя v.{$ba_config[version]}";
$skin_header = <<<HTML
<!doctype html>
<html>
<head>
  <meta charset="{$distr_charset}">
  <meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1, user-scalable=0">
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <title>DataLife Engine - Установка модуля</title>
  <link href="engine/skins/stylesheets/application.css" media="screen" rel="stylesheet" type="text/css" />
  <script type="text/javascript" src="engine/skins/javascripts/application.js"></script>
</head>
<body>
<style type="text/css">	
.fineprint {
	border:1px solid black; 
	padding:8px; 
	background:#ffffff;
	text-align:justify;
}
</style>
<nav class="navbar navbar-default navbar-inverse navbar-static-top" role="navigation">
  <div class="navbar-header">
    <a class="navbar-brand" href=""><img src="engine/skins/images/logo.png" />Мастер установки модуля</a>
  </div>
</nav>
<div class="container">
  <div class="col-md-8 col-md-offset-2">
    <div class="padded">
	    <div style="margin-top: 50px;">
<!--MAIN area-->
HTML;


$skin_footer = <<<HTML
	 <!--MAIN area-->
    </div>
  </div>
</div>
</div>

<p style="text-align:center;"><a href="http://www.kevagroup.ru/">mr_Evgen</a> &copy 2012</p>

</body>
</html>
HTML;

	echo $skin_header;

	if( isset($_POST['go']) ) {

		$dle_api->install_admin_module("balance_account", "Баланс пользователя", "Кошелёк пользователя с возможностью его пополнения, перевода, вывода средств.", "balance_account.png", 1 );

		$tableSchema[] = "DROP TABLE IF EXISTS " . PREFIX . "_payments";
		$tableSchema[] = "DROP TABLE IF EXISTS " . PREFIX . "_payments_log";
		$tableSchema[] = "DROP TABLE IF EXISTS " . PREFIX . "_payments_outmoney";
				
		$tableSchema[] = "ALTER TABLE `" . PREFIX . "_users` ADD user_balance decimal(10,2) NOT NULL";
				
		$tableSchema[] = "CREATE TABLE IF NOT EXISTS `" . PREFIX . "_payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_user` varchar(100) NOT NULL,
  `payment_datecreat` int(11) NOT NULL,
  `payment_datepay` int(11) NOT NULL,
  `payment_money` decimal(10,2) NOT NULL,
  `payment_cont` varchar(10) NOT NULL,
  `payment_system` text NOT NULL,
  PRIMARY KEY (`payment_id`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";

		$tableSchema[] = "CREATE TABLE IF NOT EXISTS `" . PREFIX . "_payments_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `log_user` varchar(100) NOT NULL,
  `log_do` varchar(5) NOT NULL,
  `log_money` decimal(10,2) NOT NULL,
  `log_cont` varchar(10) NOT NULL,
  `log_desc` text NOT NULL,
  `log_date` int(11) NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";

		$tableSchema[] = "CREATE TABLE IF NOT EXISTS `" . PREFIX . "_payments_outmoney` (
  `om_id` int(11) NOT NULL AUTO_INCREMENT,
  `om_user` varchar(100) NOT NULL,
  `om_money` decimal(10,2) NOT NULL,
  `om_back` decimal(10,2) NOT NULL,
  `om_cont` varchar(10) NOT NULL,
  `om_date_creat` int(11) NOT NULL,
  `om_date_pay` int(11) NOT NULL,
  `om_desc` varchar(200) NOT NULL,
  PRIMARY KEY (`om_id`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";

		foreach($tableSchema as $table) {
			$db->query($table);
		}
    
    /* настройки */
    $ba_config['paycode'] = genCode();
    //save_setting("config", $ba_config, "ba_config");

    /* Пользовательский интерфейс */
    $pay_api->creat_static_page( 'cabinet', "Личный кабинет", "balance_account/user");

    $title .= " &raquo; Модуль установлен";
		$text = <<<HTML
		<h3 style="text-align:center;margin: 0 auto">Поздравляем! Установка завершена</h3>
		
		<br /><p>Теперь Вам доступно:</p>
		
          <b><span class="status-success"><i class="icon-share"></i></span>&nbsp;&nbsp;<a href="{$config['admin_path']}?mod=balance_account">Админ панель модуля</a></b>
    <br /><b><span class="status-success"><i class="icon-share"></i></span>&nbsp;&nbsp;<a href="{$config['admin_path']}?mod=balance_account&section=plugins&plugin=plugins">Каталог плагинов</a></b>
    <br /><b><span class="status-success"><i class="icon-share"></i></span>&nbsp;&nbsp;<a href="cabinet.html">Личный кабинет управления балансом</a></b>
    <br /><b><span class="status-success"><i class="icon-share"></i></span>&nbsp;&nbsp;<a href="http://www.nukagame.ru/doc/balance_account/" target="_blank">Онлайн документация</a></b>
		<br />
		<br />Пожалуйста, удалите файл <b>{$PHP_SELF}</b> с Вашего сервера.
		
    <div class="row box-section">	
      <input class="btn btn-green" onClick="location.href='{$config['admin_path']}?mod=balance_account'" type=submit value="Перейти к управлению модулем">
    </div>
HTML;

	} else {

  $title .= " &raquo; Пользовательское соглашение";
	$text = <<<HTML
<form action="{$PHP_SELF}" method="post">
  <div style="height: 200px; border: 1px solid #76774C; background-color: #FDFDD3; padding: 5px; overflow: auto;">
    <b>Пользовательское соглашение</b></p>
    <p><a href="http://opensource.org/licenses/mit-license.php">The MIT License (MIT)</a></p>
    <p>Copyright (c) 2012 mr_Evgen (http://www.kevagroup.ru/)</p>
    <p>Данная лицензия разрешает лицам, получившим копию данного программного обеспечения и сопутствующей документации (в дальнейшем именуемыми «Программное Обеспечение»), безвозмездно использовать Программное Обеспечение без ограничений, включая неограниченное право на использование, копирование, изменение, добавление, публикацию, распространение, сублицензирование и/или продажу копий Программного Обеспечения, также как и лицам, которым предоставляется данное Программное Обеспечение, при соблюдении следующих условий:</p>
    <p>Указанное выше уведомление об авторском праве и данные условия должны быть включены во все копии или значимые части данного Программного Обеспечения.</p>
    <p>ДАННОЕ ПРОГРАММНОЕ ОБЕСПЕЧЕНИЕ ПРЕДОСТАВЛЯЕТСЯ «КАК ЕСТЬ», БЕЗ КАКИХ-ЛИБО ГАРАНТИЙ, ЯВНО ВЫРАЖЕННЫХ ИЛИ ПОДРАЗУМЕВАЕМЫХ, ВКЛЮЧАЯ, НО НЕ ОГРАНИЧИВАЯСЬ ГАРАНТИЯМИ ТОВАРНОЙ ПРИГОДНОСТИ, СООТВЕТСТВИЯ ПО ЕГО КОНКРЕТНОМУ НАЗНАЧЕНИЮ И ОТСУТСТВИЯ НАРУШЕНИЙ ПРАВ. НИ В КАКОМ СЛУЧАЕ АВТОРЫ ИЛИ ПРАВООБЛАДАТЕЛИ НЕ НЕСУТ ОТВЕТСТВЕННОСТИ ПО ИСКАМ О ВОЗМЕЩЕНИИ УЩЕРБА, УБЫТКОВ ИЛИ ДРУГИХ ТРЕБОВАНИЙ ПО ДЕЙСТВУЮЩИМ КОНТРАКТАМ, ДЕЛИКТАМ ИЛИ ИНОМУ, ВОЗНИКШИМ ИЗ, ИМЕЮЩИМ ПРИЧИНОЙ ИЛИ СВЯЗАННЫМ С ПРОГРАММНЫМ ОБЕСПЕЧЕНИЕМ ИЛИ ИСПОЛЬЗОВАНИЕМ ПРОГРАММНОГО ОБЕСПЕЧЕНИЯ ИЛИ ИНЫМИ ДЕЙСТВИЯМИ С ПРОГРАММНЫМ ОБЕСПЕЧЕНИЕМ.</p>
  </div>
HTML;
  $butt = <<<HTML
	<div class="row box-section">	
		<input class="btn btn-green" name="go" type=submit value="Установить">
	</div>
</form>
HTML;

	}

  echo <<<HTML
<div class="box">
  <div class="box-header">
    <div class="title">{$title}</div>
  </div>
  <div class="box-content">
	<div class="row box-section">
		{$text}
	</div>
	  {$butt}
  </div>
</div>
HTML;
	echo $skin_footer;
?>
