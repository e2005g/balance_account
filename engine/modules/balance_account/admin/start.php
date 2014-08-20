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

/* Меню */

if( !defined( 'KG_MODULE' ) ) {
    die( "Hacking attempt!" );
}

  echoheader( "Баланс пользователя v.".$ba_config['version'], "Кошелёк пользователя с возможностью его пополнения, перевода, вывода средств.");
  $ADM_theme->start("Меню");

	/* Статистика */
	$money_back = $db->super_query( "SELECT count(*) as count FROM " . USERPREFIX . "_payments_outmoney where om_date_pay=0" );
  $payments = $db->super_query( "SELECT count(*) as count FROM " . USERPREFIX . "_payments where payment_datepay=0" );
	$all_money = $db->super_query( "SELECT SUM({$ba_config[user_balance_field]}) as sum FROM " . USERPREFIX . "_users where {$ba_config[user_balance_field]} IS NOT NULL" );

  $moneback = ( $money_back['count'] ) ? "<font color=\"green\">Новых запросов: ".$money_back['count']."</font>": "Текущих запросов нет";
  $billsnotpay = ( $payments['count'] ) ? "<font color=\"red\">Неоплаченные счета: ".$payments['count']."</font>": "<font color=\"green\">Все счета оплачены</font>";
  $allmoneyinsite = "<font color=\"green\">Всего средств в системе: {$all_money[sum]} {$ba_config['money_cont']}</font>";

echo <<<HTML
<div class="box-content">

  <div class="row box-section">	  <div class="col-md-6">
      <div class="news with-icons">
        <div class="avatar"><img src="engine/modules/balance_account/images/configure.png"></div>
        <div class="news-content">
        <div class="news-title"><a href="{$PHP_SELF}?mod=balance_account&section=settings">Настройки модуля</a></div>
        <div class="news-text">
          <a href="{$PHP_SELF}?mod=balance_account&section=settings">Управление модулем - Баланс пользователя</a>
        </div>
        </div>
      </div>
      </div>	  <div class="col-md-6">
      <div class="news with-icons">
        <div class="avatar"><img src="engine/modules/balance_account/images/paysys.png"></div>
        <div class="news-content">
        <div class="news-title"><a href="{$PHP_SELF}?mod=balance_account&section=paysys">Платёжные системы</a></div>
        <div class="news-text">
          <a href="{$PHP_SELF}?mod=balance_account&section=paysys">Управление и настройка платёжных систем</a>
        </div>
        </div>
      </div>
      </div></div>

  <div class="row box-section">
  
    <div class="col-md-6">
      <div class="news with-icons">
        <div class="avatar"><img src="engine/modules/balance_account/images/moneyback.png"></div>
        <div class="news-content">
        <div class="news-title"><a href="{$PHP_SELF}?mod=balance_account&section=moneyback">Вывод средств</a></div>
        <div class="news-text">
          <a href="{$PHP_SELF}?mod=balance_account&section=moneyback">Заявки пользователей на вывод средств</a>
          <br />{$moneback}
        </div>
        </div>
      </div>
      </div>
      
      <div class="col-md-6">
      <div class="news with-icons">
        <div class="avatar"><img src="engine/modules/balance_account/images/paylog.png"></div>
        <div class="news-content">
        <div class="news-title"><a href="{$PHP_SELF}?mod=balance_account&section=paylog">Счета пользователей</a></div>
        <div class="news-text">
          <a href="{$PHP_SELF}?mod=balance_account&section=paylog">Просмотр созданных пользователями счетов</a>
          <br />{$billsnotpay}
        </div>
        </div>
      </div>
    </div>
    
   </div>
      
  <div class="row box-section">	  <div class="col-md-6">
      <div class="news with-icons">
        <div class="avatar"><img src="engine/modules/balance_account/images/deal.png"></div>
        <div class="news-content">
        <div class="news-title"><a href="{$PHP_SELF}?mod=balance_account&section=deal&user_name={$member_id['name']}">Досье</a></div>
        <div class="news-text">
          <a href="{$PHP_SELF}?mod=balance_account&section=deal&user_name={$member_id['name']}">История баланса пользователя</a>
        </div>
        </div>
      </div>
      </div>	  <div class="col-md-6">
      <div class="news with-icons">
        <div class="avatar"><img src="engine/modules/balance_account/images/log.png"></div>
        <div class="news-content">
        <div class="news-title"><a href="{$PHP_SELF}?mod=balance_account&section=log">Лог движения средств</a></div>
        <div class="news-text">
          <a href="{$PHP_SELF}?mod=balance_account&section=log">Просмотр общего лога баланса пользователей</a>
          <br />{$allmoneyinsite}
        </div>
        </div>
      </div>
      </div></div>

</div>
HTML;

/* Сторонние модули */
$plugins_return = array();
$plugins_echo = "";
$plugins_str = 0;

/* Список загруженных плагинов */
	$handle = opendir( MODULE_PATH . "/plugins/" );
  $plugins_return = array();
  $plugins_echo = "";
	$plugins_return['plugins'] = $plugin;
	
	while ($folder = readdir($handle)) {
     if (!in_array($folder, array(".", "..", "/", "index.php", ".htaccess"))) {
     
        if( file_exists( MODULE_PATH . "/plugins/{$folder}/info.php") ) {
        
          unset($plugin);
          require_once MODULE_PATH . "/plugins/{$folder}/info.php";
          $plugins_return[$folder] = $plugin;
        
        }
        
     }
  }

foreach( $plugins_return as $plugin_name=>$plugin_info ) {

  if( $plugin_info['name'] ) $plugins_echo .= <<<HTML
    <div class="col-md-6">
      <div class="news with-icons">
        <div class="avatar"><img src="engine/modules/balance_account/plugins/{$plugin_name}/images/icon.png"></div>
        <div class="news-content">
        <div class="news-title"><a href="{$PHP_SELF}?mod=balance_account&section=plugins&plugin={$plugin_name}">{$plugin_info['name']}</a></div>
        <div class="news-text">
          <a href="{$PHP_SELF}?mod=balance_account&section=plugins&plugin={$plugin_name}">{$plugin_info['desc']}</a>
          <br />Разработано: <a href="{$plugin_info['site']}" target="_blank">&copy {$plugin_info['autor']}</a>
        </div>
        </div>
      </div>
      </div>
HTML;

  $plugins_str++;
  if( $plugins_str > 1 ) {
    $plugins_echo .= "</div> <div class=\"row box-section\">";
    $plugins_str = 0;
  }

}

if( $plugins_echo ) {

  echo "</div></div>";
  $ADM_theme->start("Плагины");
  echo "<div class=\"box-content\"><div class=\"row box-section\">".$plugins_echo."</div></div>";

}

	$ADM_theme->end_copy();
	echofooter();

?>