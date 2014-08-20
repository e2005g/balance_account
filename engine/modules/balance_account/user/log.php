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

if(!defined('KG_MODULE')) {
  die("Hacking attempt!");
}

/* Страница: лог изменения баланса. */

if( $is_logged ) {

        $page = intval( $_GET['pagin'] );
        $member_id['name'] = $db->safesql( $member_id['name'] );

        if(!$page) $page  = 1;
        $per_page = $ba_lang['paging_log'];
        $start_from = ($page * $per_page) - $per_page;

        $query_count = "SELECT COUNT(*) as count FROM " . USERPREFIX . "_payments_log where log_user='{$member_id[name]}'";
        $result_count = $db->super_query( $query_count );
        $all_count = $result_count['count'];

        $user_date = array();
        $user_balance = array();
        $id_diag = -1;
        $set_jquery = "";

        $db->query( "SELECT * FROM " . USERPREFIX . "_payments_log where log_user='{$member_id[name]}' ORDER BY log_id desc LIMIT {$start_from},{$per_page}" );

        while ( $row = $db->get_row() ) {

                $act_do = ( $row['log_do']=="plus" ) ? "+": "-";

                $date_m = date( "n", $row['log_date'])-1;
                $date = date( "Y,{$date_m},j", $row['log_date']);
                $end_diag = ( !$end_diag ) ? langdate( "j F Y  G:i", $row['log_date']):  $end_diag;
                $start_diag = langdate( "j F Y  G:i", $row['log_date']);

                $id_diag++;
                $user_date[$id_diag] = $date;

                if($row['log_do']=="plus" ) $user_balance[$date] = $user_balance[$date]+$row['log_money'];
                else $user_balance[$date] = $user_balance[$date]-$row['log_money'];

                $answer .= "<tr>
                                <td>".langdate( "j F Y  G:i", $row['log_date'])."</td>
                                <td>{$act_do} {$row['log_money']} {$row['log_cont']}</td>
                                <td>{$row['log_desc']}</td>
                        </tr>";

        }

        if( !$answer )        $answer = "<tr><td colspan='3'>{$ba_lang['log_not']}</td></tr>";

        $user_date = array_reverse( $user_date );
        foreach($user_date as $id=>$date) $set_jquery .= "[Date.UTC({$date}), ".$user_balance[$date]." ],\n";

  /* Paging */
        $npp_nav = ( $all_count > $per_page ) ? kgPaging( $all_count, $page, " <a href=\"#\" onClick=\"load_page_pay('log', '{p}', false)\">{p}</a> ", $per_page ) : "";

        $content = file_get_contents( ROOT_DIR . "/templates/{$config[skin]}/balance_account/log.tpl");
        $content = str_replace("{log}", $answer, $content);
        $content = str_replace("{paging}", $npp_nav, $content);
        $content = str_replace("{user_name}", $member_id['name'], $content);
        $content = str_replace("{set_jquery}", $set_jquery, $content);
        $content = str_replace("{balance}", $member_id[$ba_config['user_balance_field']], $content);
        $content = str_replace("{balance_cont}", $ba_config['money_cont'], $content);
        $content = str_replace("{start_diag}", $start_diag, $content);
        $content = str_replace("{end_diag}", $end_diag, $content);

        echo "<div id=\"payb_navigation\"><a href=\"#\" onClick=\"load_page_pay('menu')\">{$ba_lang['nav_start']}</a> &raquo; {$ba_lang['nav_domoney']}</div><SPLIT><div id=\"payb_main\">{$content}</div>";

} else echo $ba_lang['needlogin'];


?>
