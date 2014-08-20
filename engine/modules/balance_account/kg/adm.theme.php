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

Class ADB_THEME {

	var $ec_select = "";

	function start($title) {
			echo <<<HTML

          <script type="text/javascript" src="engine/modules/balance_account/images/admin.js"></script>
          <link media="screen" href="engine/modules/balance_account/images/admin.css" type="text/css" rel="stylesheet" />

          <div id="general" class="box">
            <div class="box-header">
              <div class="title">{$title}</div>
            </div>
            <div class="box-content">

          <form action="" method="post" name="balance">

HTML;
	}


	function end_copy( $copy = "[ <a href=\"https://github.com/mr-Evgen/balance_account\">страница на GitHub</a> ]<br /><a href=\"http://www.kevagroup.ru/\">mr_Evgen</a> &copy 2012" ) {
		global $ba_config;
		echo <<<HTML
		</div></div>
    <p style="text-align:center;">{$copy}</p>
HTML;

	}

	function select($title, $description, $field) {
		$this->ec_select .= <<<HTML
    <tr>
        <td class="col-xs-10 col-sm-6 col-md-7"><h6>{$title}</h6><span class="note large">{$description}</span></td>
        <td class="col-xs-2 col-md-5 settingstd">{$field}</td>
     </tr>
HTML;
	}

  /* Вид плагина в каталоге  */
  function list_catalog( $link, $plugin_info, $do, $icon, $style = "" ) {

    return <<<HTML
    <div style="{$style}">
      <div style="float:right">{$do}</div>
        <div class="news with-icons">
          <div class="avatar"><img src="{$icon}"></div>
          <div class="news-content">
          <div class="news-title"><a href="{$link}">{$plugin_info['name']} v.{$plugin_info['version']}</a></div>
          <div class="news-text">
            <a href="{$link}">{$plugin_info['desc']}</a>
            <br />Разработано: <a href="{$plugin_info['site']}" target="_blank">&copy {$plugin_info['autor']}</a>
          </div>
          </div>
        </div>
      </div>
        <hr />
HTML;

  }

}

$ADM_theme = new ADB_THEME;