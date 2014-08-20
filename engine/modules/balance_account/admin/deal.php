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

/* Досье на пользователя */

if( !defined( 'KG_MODULE' ) ) {
    die( "Hacking attempt!" );
}

  include_once ENGINE_DIR . '/classes/parse.class.php';
  include_once ENGINE_DIR . '/api/api.class.php';

  $parse = new ParseFilter( Array (), Array (), 1, 1 );

  echoheader( "Баланс пользователя v.".$ba_config['version'], "Кошелёк пользователя с возможностью его пополнения, перевода, вывода средств.");
	$ADM_theme->start("<a href='{$PHP_SELF}?mod=balance_account'>Баланс пользователя v.{$ba_config[version]}</a> &raquo; Досье на пользователя");

  $user_name = $db->safesql( $_REQUEST['user_name'] );

      //пополнить, опустошить баланс
      if( isset($_POST['edit_balance']) ) {
          $do_balance = ( $_POST['edit_balance_type']=="plus" ) ? "plus": "minus";
          $money = number_format( str_replace(",", ".", $_POST['edit_money']) , 2, '.', '');
          $desc = trim( $db->safesql( $parse->process( $_POST['edit_desc'] ) ) );
          
          if( $desc) {
            if( $_POST['edit_balance_type']=="plus" ) $pay_api->plus($user_name, $money, $desc);
            else $pay_api->minus($user_name, $money, $desc, 0);
            $msg = "Выполнено!";
          } else $msg = "Заполните поля!";
      } else $msg = "";

  $user_main_info = ( $user_name ) ? $dle_api->take_user_by_name( $user_name ) : false;

echo "<p style=\"text-align:center;padding-top:10px\"><span class=\"note large\">Имя пользователя:</span> <input name=\"user_name\" class=\"edit bk\" type=\"text\" value=\"{$user_name}\" size=\"60\"> <input class=\"btn btn-green\" style=\"margin:7px;\" name=\"search_now\" type=\"submit\" value=\"Найти\"></td></tr>";

  if( $user_main_info!=FALSE ) {
  
      echo "<table width='50%' class=\"table table-normal\">";
      echo "<tr><td width=\"50%\">Пользователь</td><td width=\"20%\">Выполнить</td></tr>";
      echo "<tr>";
      //Информация о пользователе

            //аватар
            if ( count(explode("@", $user_main_info['foto'])) == 2 ) {
                $avatar = 'http://www.gravatar.com/avatar/' . md5(trim($user_main_info['foto'])) . '?s=' . intval($user_group[$user_main_info['user_group']]['max_foto']);
            } else {
                if( $user_main_info['foto'] and (file_exists( ROOT_DIR . "/uploads/fotos/" . $row['foto'] )) ) $avatar = $config['http_home_url'] . "uploads/fotos/" . $user_main_info['foto'];
                else $avatar = "templates/{$config['skin']}/dleimages/noavatar.png";
            }
            
      echo "<td>
                  <table width='90%'>
                    <tr><td rowspan='5'><img src=\"{$avatar}\"></td><td>Пользователь:</td><td><b><a href=\"".$config['http_home_url'] . "index.php?subaction=userinfo&user=".urlencode($user_main_info['name'])."\" target=\"_blank\">{$user_name}</a></b></td></tr>
                    <tr><td>Email:</td><td><b>{$user_main_info['email']}</b></td></tr>
                    <tr><td>Зарегистрирован:</td><td><b>".langdate( "j F Y  G:i", $user_main_info['reg_date'])."</b></td></tr>
                    <tr><td>Последний визит:</td><td>".langdate( "j F Y  G:i", $user_main_info['lastdate'])."</td></tr>
                    <tr><td>Текущий баланс:</td><td><b>{$user_main_info[$ba_config['user_balance_field']]} {$ba_config['money_cont']}</b></td></tr>
                  </table>
                 </div>
           </td>";

      echo "<td>
          <br />
          Действие: <select class=\"uniform\" style=\"min-width:100px;\" name=\"edit_balance_type\"><option value=\"plus\">Пополнить</option><option value=\"minus\">Изъять</option></select> <input name=\"edit_money\" class=\"edit bk\" type=\"text\" value=\"100.00\" size=\"20\"> {$ba_config['money_cont']}
          <br /><br />
          Комментарий: <input name=\"edit_desc\" class=\"edit bk\" type=\"text\" size=\"50\">
          <br /><br />
          <input class=\"btn btn-gold\" style=\"margin:7px;\" name=\"edit_balance\" type=\"submit\" value=\"Выполнить\"> {$msg}
            </td>";
      echo "</tr></table></div></div>";
  
      //Лог баланса
      $ADM_theme->start("Лог баланса");

            //getlog
            $page = ($_GET['paging']) ? $_GET['paging'] : 1;
            $per_page = 10;
            $start_from = ($page * $per_page) - $per_page;
            $answer = "";
           
            $result_count = $db->super_query( "SELECT COUNT(*) as count FROM " . USERPREFIX . "_payments_log where log_user='{$user_main_info[name]}'" );
            $all_count = $result_count['count'];

            $db->query( "SELECT * FROM " . USERPREFIX . "_payments_log where log_user='{$user_main_info[name]}' ORDER BY log_id desc LIMIT {$start_from},{$per_page}" );
            while ( $row = $db->get_row() ) {
                $act_do = ( $row['log_do']=="plus" ) ? "<font color=green>+{$row[log_money]} {$row[log_cont]}</font>": "<font color=red>-{$row[log_money]} {$row[log_cont]}</font>";

                $answer .= "<tr>
                      <td>".langdate( "j F Y  G:i", $row['log_date'])."</td>
                      <td>{$act_do}</td>
                      <td>{$row[log_desc]}</td>
                    </tr>";
            } if( !$answer )	$answer = "<tr><td colspan='3' style='margin:10px;'>Лог пуст.</td></tr>";   
            /* Paging */
            $answer .= ( $all_count > $per_page ) ? "<tr><td colspan='3' style='margin:10px;'>".kgPaging( $all_count, $page, " <a href=\"{$PHP_SELF}?mod=balance_account&section=deal&user_name={$user_main_info['name']}&paging={p}\">{p}</a> ", $per_page )."</td></tr>": "";

      echo "<table width=\"100%\" class=\"table table-normal\">
               <tr>
                  <td><b>Дата и время<b></td>
                  <td><b>Действие<b></td>
                  <td width=\"60%\"><b>Комментарий<b></td>
              </tr>
              {$answer}</table></div></div>";
  
  //Счета пользователя
  $ADM_theme->start("Счета пользователя");
  
            //getbills
            $page = ($_GET['paging_bills']) ? $_GET['paging_bills'] : 1;
            $start_from = ($page * $per_page) - $per_page;
            $answer = "";

            $result_count = $db->super_query( "SELECT COUNT(*) as count FROM " . USERPREFIX . "_payments where payment_user='{$user_main_info[name]}'" );
            $all_count = $result_count['count'];
            $paysys_list = $Paysys->select();
            
            $db->query( "SELECT * FROM " . USERPREFIX . "_payments where payment_user='{$user_main_info[name]}' ORDER BY payment_id desc LIMIT {$start_from},{$per_page}" );
            while ( $row = $db->get_row() ) {
                $info_paymentsys = unserialize($paysys_list[$row['payment_system']]['info']);

                $datepay = ( $row['payment_datepay'] ) ? "<font color='green'>".langdate( "j F Y  G:i", $row['payment_datepay'])."</font>" : "<font color='red'>Не оплачено</font>";

                $answer .= "<tr>
                      <td align='center'>{$row[payment_id]}</td>
                      <td>{$datepay}</td>
                      <td>{$row[payment_money]} {$row[payment_cont]}</td>
                      <td>{$paysys_list[$row[payment_system]][name]}</td>
                      <td>".langdate( "j F Y  G:i", $row['payment_datecreat'])."</td>
                    </tr></tr>";
            } if( !$answer )	$answer = "<tr><td colspan='5' style='margin:10px;'>Счета не найдены.</td></tr>";   
            /* Paging */
            $answer .= ( $all_count > $per_page ) ? "<tr><td colspan='5' style='margin:10px;'>".kgPaging( $all_count, $page, " <a href=\"{$PHP_SELF}?mod=balance_account&section=deal&user_name={$user_main_info['name']}&paging_bills={p}\">{p}</a> ", $per_page )."</td></tr>": "";

      echo "<table width=\"100%\" class=\"table table-normal\">
              <tr>
                <td align='center'><b>Ключ<b></td>
                <td><b>Оплачено<b></td>
                <td><b>Сумма<b></td>
                <td><b>Платёжная система<b></td>
                <td><b>Платёж создан<b></td>
              </tr>
              {$answer}
          </table></div></div>";

  //Запросы вывода
  $ADM_theme->start("Запросы вывода средств");

            //getoutmoney
            $page = ($_GET['paging_outmoney']) ? $_GET['paging_outmoney'] : 1;
            $start_from = ($page * $per_page) - $per_page;
            $answer = "";

            $result_count = $db->super_query( "SELECT COUNT(*) as count FROM " . USERPREFIX . "_payments_outmoney where om_user='{$user_main_info[name]}'" );
            $all_count = $result_count['count'];
            $paysys_list = $Paysys->select();
            
            $db->query( "SELECT * FROM " . USERPREFIX . "_payments_outmoney where om_user='{$user_main_info[name]}' ORDER BY om_id desc LIMIT {$start_from},{$per_page}" );
            while ( $row = $db->get_row() ) {
            
                $backlink = ( $row['om_date_pay'] ) ? "<font color='green' title='одобрено'>".langdate( "j F Y  G:i", $row['om_date_pay'])."<font>": "<a href=\"{$PHP_SELF}?mod=balance_account&section=moneyback&type=ok&h={$dle_login_hash}&id={$row[om_id]}\">[одобрить]</a> <a href=\"{$PHP_SELF}?mod=balance_account&section=moneyback&type=r&h={$dle_login_hash}&id={$row[om_id]}\">[отказать]</a>";

                $answer .= "<tr>
                              <td>".langdate( "j F Y  G:i", $row['om_date_creat'])."</td>
                              <td>{$row[om_money]} {$row[om_cont]}</td>
                              <td>{$row[om_desc]}</td>
                              <td>{$backlink}</td>
                             </tr>";
            } if( !$answer )	$answer = "<tr><td colspan='4' style='margin:10px;'>Запросы не найдены.</td></tr>";   
            /* Paging */
            $answer .= ( $all_count > $per_page ) ? "<tr><td colspan='4' style='margin:10px;'>".kgPaging( $all_count, $page, " <a href=\"{$PHP_SELF}?mod=balance_account&section=deal&user_name={$user_main_info['name']}&paging_outmoney={p}\">{p}</a> ", $per_page )."</td></tr>": "";

      echo "<table width=\"100%\" class=\"table table-normal\">
              <tr>
                <td><b>Дата и время<b></td>
                <td><b>Сумма<b></td>
                <td><b>Примечание<b></td>
                <td><b>Статус<b></td>
              </tr>
              {$answer}
          </table>";

  } elseif( $user_name ) echo "<hr /><p style=\"text-align:center;padding-top:10px\">Пользователь не найден</td></tr>";

	$ADM_theme->end_copy();
	echofooter();

?>